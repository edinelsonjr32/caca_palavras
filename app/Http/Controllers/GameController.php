<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\Level;
use App\Models\DailyActivity;
use App\Models\Ranking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class GameController extends Controller
{
    /**
     * Inicia uma nova partida para o usuário.
     * Cria uma GameSession e redireciona para o primeiro nível.
     */
    public function start()
    {
        $user = Auth::user();
        $firstLevel = Level::where('level_number', 1)->firstOrFail();

        // 1. Criar uma nova GameSession
        $gameSession = GameSession::create([
            'user_id' => $user->id,
            'current_level_id' => $firstLevel->id,
            'total_score' => 0,
            'status' => 'active',
        ]);

        // Armazenar a sessão do jogo na sessão HTTP do Laravel
        Session::put('game_session_id', $gameSession->id);

        // 2. Registrar que o usuário jogou hoje
        DailyActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_date' => Carbon::today()],
            ['has_played_game' => true]
        );

        // 3. Redirecionar para a tela de jogo do primeiro nível
        return redirect()->route('game.play', ['level' => $firstLevel->id]);
    }

    /**
     * Gera e exibe o tabuleiro para o nível atual.
     *
     * @param  \App\Models\Level  $level
     */
    public function play(Level $level)
    {
        $user = Auth::user();
        $gameSessionId = Session::get('game_session_id');

        if (!$gameSessionId) {
            return redirect()->route('dashboard')->with('error', 'Nenhuma sessão de jogo ativa encontrada. Por favor, inicie uma nova partida.');
        }

        $gameSession = GameSession::find($gameSessionId);

        // Verifica se a sessão de jogo pertence ao usuário e está ativa
        if (!$gameSession || $gameSession->user_id !== $user->id || $gameSession->status !== 'active') {
            Session::forget('game_session_id'); // Limpa a sessão inválida
            return redirect()->route('dashboard')->with('error', 'Sessão de jogo inválida ou encerrada.');
        }

        // Se o nível solicitado não for o nível atual da sessão, redireciona para o correto
        if ($gameSession->current_level_id !== $level->id) {
            return redirect()->route('game.play', ['level' => $gameSession->currentLevel->id]);
        }

        // Palavras secretas para este nível
        $secretWords = $level->words->pluck('word')->toArray();

        // Lógica de geração do tabuleiro (simplificada para o backend)
        // A geração visual e interativa ocorrerá no frontend (JavaScript)
        $gridSize = $level->grid_size;
        $board = $this->generateGameBoard($secretWords, $gridSize);

        return view('game.play', [
            'level' => $level,
            'secretWords' => $secretWords,
            'board' => $board,
            'gameSession' => $gameSession,
        ]);
    }

    /**
     * Valida uma palavra selecionada pelo usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function validateWord(Request $request)
    {
        $request->validate([
            'selected_word' => ['required', 'string'],
            'level_id' => ['required', 'exists:levels,id'],
            'time_left' => ['required', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        $gameSessionId = Session::get('game_session_id');

        if (!$gameSessionId) {
            return response()->json(['success' => false, 'message' => 'Nenhuma sessão de jogo ativa.']);
        }

        $gameSession = GameSession::find($gameSessionId);
        $level = Level::find($request->level_id);
        $selectedWord = strtoupper($request->selected_word);
        $timeLeft = $request->time_left;

        // Verifica se a sessão e o nível são válidos para o usuário
        if (!$gameSession || $gameSession->user_id !== $user->id || $gameSession->current_level_id !== $level->id || $gameSession->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Sessão de jogo inválida.']);
        }

        // Busca as palavras secretas do nível
        $secretWords = $level->words->pluck('word')->map(fn($word) => strtoupper($word))->toArray();

        // 1. Validação da palavra
        if (in_array($selectedWord, $secretWords) && !Session::get("found_words.{$level->id}.{$selectedWord}")) {
            // Palavra encontrada e ainda não foi marcada como encontrada neste nível
            $wordLength = strlen($selectedWord);
            $points = $wordLength * 10 + ($timeLeft > 0 ? floor($timeLeft / 10) : 0); // Exemplo de pontuação

            $gameSession->total_score += $points;
            $gameSession->save();

            // Marca a palavra como encontrada na sessão HTTP
            Session::put("found_words.{$level->id}.{$selectedWord}", true);

            // Verifica se todas as palavras foram encontradas no nível
            $foundWordsCount = collect(Session::get("found_words.{$level->id}", []))->filter()->count();
            if ($foundWordsCount === count($secretWords)) {
                // Todas as palavras encontradas, avança para o próximo nível
                $nextLevel = Level::where('level_number', $level->level_number + 1)->first();

                if ($nextLevel) {
                    $gameSession->current_level_id = $nextLevel->id;
                    $gameSession->save();
                    Session::forget("found_words.{$level->id}"); // Limpa as palavras encontradas do nível anterior
                    return response()->json(['success' => true, 'found' => true, 'score' => $gameSession->total_score, 'level_completed' => true, 'next_level_id' => $nextLevel->id]);
                } else {
                    // Todos os níveis foram completados
                    return $this->finishGame($gameSession, 'completed');
                }
            }

            return response()->json(['success' => true, 'found' => true, 'score' => $gameSession->total_score, 'level_completed' => false]);
        }

        return response()->json(['success' => true, 'found' => false]);
    }

    /**
     * Finaliza a partida de jogo.
     *
     * @param  \App\Models\GameSession  $gameSession
     * @param  string  $status ('completed' ou 'failed')
     */
    public function end(Request $request)
    {
        $gameSessionId = Session::get('game_session_id');
        if (!$gameSessionId) {
            return redirect()->route('dashboard')->with('error', 'Nenhuma sessão de jogo ativa para finalizar.');
        }

        $gameSession = GameSession::find($gameSessionId);
        if (!$gameSession || $gameSession->user_id !== Auth::id()) {
            Session::forget('game_session_id');
            return redirect()->route('dashboard')->with('error', 'Sessão de jogo inválida.');
        }

        // Se o status já não estiver definido (ex: via game over do frontend)
        if ($gameSession->status === 'active') {
            $gameSession->status = $request->input('status', 'failed'); // Pode vir do frontend ou ser 'failed' por padrão
            $gameSession->save();
        }

        // Salvar no ranking global
        Ranking::create([
            'user_id' => $gameSession->user_id,
            'score' => $gameSession->total_score,
            'level_reached' => $gameSession->currentLevel->level_number,
        ]);

        // Limpar a sessão de jogo
        Session::forget('game_session_id');
        Session::forget('found_words'); // Limpa palavras encontradas de todos os níveis

        return view('game.end', [
            'gameSession' => $gameSession,
        ]);
    }


    /**
     * Helper: Termina a partida, salva no ranking e retorna a resposta JSON (usado internamente).
     *
     * @param  \App\Models\GameSession  $gameSession
     * @param  string  $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function finishGame(GameSession $gameSession, string $status)
    {
        $gameSession->status = $status;
        $gameSession->save();

        Ranking::create([
            'user_id' => $gameSession->user_id,
            'score' => $gameSession->total_score,
            'level_reached' => $gameSession->currentLevel->level_number,
        ]);

        Session::forget('game_session_id');
        Session::forget('found_words');

        return response()->json(['success' => true, 'found' => true, 'level_completed' => true, 'game_over' => true, 'final_score' => $gameSession->total_score, 'status' => $status]);
    }

    /**
     * Helper: Gera um tabuleiro de letras preenchido aleatoriamente.
     * A inserção real das palavras será feita no frontend com JS para maior interatividade.
     * Este método apenas simula um tabuleiro.
     *
     * @param array $secretWords
     * @param int $gridSize
     * @return array
     */
    private function generateGameBoard(array $secretWords, int $gridSize): array
    {
        $board = [];
        for ($i = 0; $i < $gridSize; $i++) {
            for ($j = 0; $j < $gridSize; $j++) {
                $board[$i][$j] = chr(rand(65, 90)); // Letras maiúsculas A-Z
            }
        }
        // No backend, não vamos inserir as palavras no tabuleiro, pois isso é responsabilidade do JS para a interação visual.
        // O frontend receberá as palavras secretas e as inserirá no tabuleiro gerado aleatoriamente por ele mesmo.
        return $board; // Retorna um tabuleiro aleatório.
    }
}
