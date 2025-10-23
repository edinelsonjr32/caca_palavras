<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\Level;
use App\Models\DailyActivity;
use App\Models\Ranking; // Importar Ranking
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class GameController extends Controller
{
    public function start()
    {
        $user = Auth::user();
        $firstLevel = Level::where('level_number', 1)->firstOrFail();

        // Limpa sessões de jogo antigas do usuário
        $user->gameSessions()->where('status', 'active')->update(['status' => 'failed']);
        // Limpa palavras encontradas de jogos anteriores
        Session::forget('found_words');

        $gameSession = GameSession::create([
            'user_id' => $user->id,
            'current_level_id' => $firstLevel->id,
            'total_score' => 0,
            'status' => 'active',
        ]);

        Session::put('game_session_id', $gameSession->id);

        DailyActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_date' => Carbon::today()],
            ['has_played_game' => true]
        );

        return redirect()->route('game.play', ['level' => $firstLevel]);
    }

    public function play(Level $level)
    {
        $user = Auth::user();
        $gameSessionId = Session::get('game_session_id');

        if (!$gameSessionId) {
            return redirect()->route('dashboard')->with('error', 'Nenhuma sessão de jogo ativa.');
        }

        $gameSession = GameSession::find($gameSessionId);

        if (!$gameSession || $gameSession->user_id !== $user->id || $gameSession->status !== 'active') {
            Session::forget('game_session_id');
            return redirect()->route('dashboard')->with('error', 'Sessão de jogo inválida.');
        }

        if ($gameSession->current_level_id !== $level->id) {
            $correctLevel = Level::find($gameSession->current_level_id);
            return redirect()->route('game.play', ['level' => $correctLevel]);
        }

        // --- MUDANÇA PRINCIPAL AQUI ---
        // Carrega o nível com todas as suas palavras (objetos completos)
        $level->load('words');

        // O grid será gerado pelo JS, como antes
        $gridSize = $level->grid_size;

        return view('game.play', [
            'level' => $level, // Passa o objeto Nível completo
            'gameSession' => $gameSession,
        ]);
    }

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
            return response()->json(['success' => false, 'message' => 'Sessão de jogo ativa não encontrada.']);
        }

        $gameSession = GameSession::find($gameSessionId);
        $level = Level::find($request->level_id);
        $selectedWord = strtoupper($request->selected_word);
        $timeLeft = $request->time_left;

        if (!$gameSession || $gameSession->user_id !== $user->id || $gameSession->current_level_id !== $level->id || $gameSession->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Sessão de jogo inválida.']);
        }

        $level->load('words');
        $secretWords = $level->words->pluck('word')->map(fn($word) => strtoupper($word))->toArray();

        // Palavra encontrada
        if (in_array($selectedWord, $secretWords) && !Session::get("found_words.{$level->id}.{$selectedWord}")) {
            $wordLength = strlen($selectedWord);
            $points = ($wordLength * 10) + ($timeLeft > 0 ? floor($timeLeft / 5) : 0);

            $gameSession->total_score += $points;
            $gameSession->save();

            Session::put("found_words.{$level->id}.{$selectedWord}", true);

            $foundWordsCount = collect(Session::get("found_words.{$level->id}", []))->filter()->count();

            // Nível completo
            if ($foundWordsCount === count($secretWords)) {
                $nextLevel = Level::where('level_number', $level->level_number + 1)->first();

                if ($nextLevel) {
                    $gameSession->current_level_id = $nextLevel->id;
                    $gameSession->save();
                    Session::forget("found_words.{$level->id}");
                    return response()->json(['success' => true, 'found' => true, 'score' => $gameSession->total_score, 'level_completed' => true, 'next_level_id' => $nextLevel->id]);
                } else {
                    return $this->finishGame($gameSession, 'completed');
                }
            }
            return response()->json(['success' => true, 'found' => true, 'score' => $gameSession->total_score, 'level_completed' => false]);
        }
        return response()->json(['success' => true, 'found' => false]);
    }

    public function end(Request $request)
    {
        $gameSessionId = Session::get('game_session_id');
        if (!$gameSessionId) {
            return redirect()->route('dashboard');
        }

        $gameSession = GameSession::find($gameSessionId);
        if (!$gameSession || $gameSession->user_id !== Auth::id()) {
            Session::forget('game_session_id');
            return redirect()->route('dashboard');
        }

        if ($gameSession->status === 'active') {
            $gameSession->status = $request->input('status', 'failed');
            $gameSession->save();
        }

        Ranking::create([
            'user_id' => $gameSession->user_id,
            'score' => $gameSession->total_score,
            'level_reached' => $gameSession->currentLevel->level_number,
        ]);

        Session::forget('game_session_id');
        Session::forget('found_words');

        return view('game.end', [
            'gameSession' => $gameSession,
        ]);
    }

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
}
