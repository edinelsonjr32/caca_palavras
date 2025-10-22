<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index()
    {
        // 1. Buscar os 3 melhores jogadores para o Pódio
        $podiumPlayers = Ranking::with('user')
                                ->orderBy('score', 'desc')
                                ->orderBy('level_reached', 'desc')
                                ->take(3)
                                ->get();

        // 2. Buscar a lista paginada do restante dos jogadores (a partir do 4º lugar)
        $rankings = Ranking::with('user')
                           ->orderBy('score', 'desc')
                           ->orderBy('level_reached', 'desc')
                           ->skip(3)
                           ->paginate(15);

        // 3. Buscar a melhor pontuação e a posição do usuário logado
        $userRank = null;
        if (Auth::check()) {
            $user = Auth::user();
            $userBestScore = $user->rankings()->orderBy('score', 'desc')->first();

            if ($userBestScore) {
                // Calcula a posição contando quantos jogadores têm uma pontuação maior
                $higherScoresCount = Ranking::where('score', '>', $userBestScore->score)->count();
                $userRankPosition = $higherScoresCount + 1;

                $userRank = [
                    'position' => $userRankPosition,
                    'score' => $userBestScore->score,
                    'level_reached' => $userBestScore->level_reached
                ];
            }
        }

        // 4. Enviar todos os dados para a view
        return view('ranking.index', [
            'podiumPlayers' => $podiumPlayers,
            'rankings' => $rankings,
            'userRank' => $userRank
        ]);
    }
}