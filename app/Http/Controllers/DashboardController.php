<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ranking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $highestLevelReached = Ranking::where('user_id', $user->id)->max('level_reached') ?? 0;
        $playerCurrentLevel = $highestLevelReached + 1;
        if ($playerCurrentLevel > 100) {
            $playerCurrentLevel = 100;
        }

        $stats = [
            'highestScore' => Ranking::where('user_id', $user->id)->max('score') ?? 0,
            'gamesPlayed' => Ranking::where('user_id', $user->id)->count(),
            'levelsCompleted' => $highestLevelReached,
            'currentStreak' => $this->calculateCurrentStreak($user),
        ];

        $weeklyActivities = $this->getThisWeekActivities($user);

        return view('dashboard', [
            'playerCurrentLevel' => $playerCurrentLevel,
            'weeklyActivities' => $weeklyActivities,
            'stats' => $stats,
        ]);
    }

    /**
     * Helper para buscar as atividades da SEMANA ATUAL (Dom - Sab).
     */
    private function getThisWeekActivities($user)
    {
        // --- CORREÇÃO APLICADA AQUI ---
        // Removemos as linhas que davam erro.
        // Em vez disso, pedimos o início e o fim da semana a partir da data atual.
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SATURDAY);

        // O resto da lógica permanece o mesmo.
        $dateRange = CarbonPeriod::create($startOfWeek, $endOfWeek);
        
        $dates = [];
        foreach($dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $activities = $user->dailyActivities()
            ->whereIn('activity_date', $dates)
            ->get()->keyBy(fn($item) => Carbon::parse($item->activity_date)->format('Y-m-d'));

        $weeklyData = [];
        foreach ($dateRange as $date) {
            $dateString = $date->format('Y-m-d');
            $weeklyData[] = [
                'date' => $date,
                'day_name_short' => $date->translatedFormat('D'),
                'activity' => $activities->get($dateString)
            ];
        }
        return $weeklyData;
    }
    
    /**
     * Helper para calcular a sequência atual de dias jogados.
     */
    private function calculateCurrentStreak($user)
    {
        $activities = $user->dailyActivities()
                            ->where('has_played_game', true)
                            ->orderBy('activity_date', 'desc')
                            ->get();

        if ($activities->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastActivityDate = Carbon::parse($activities->first()->activity_date);

        if (!$lastActivityDate->isSameDay($today) && !$lastActivityDate->isSameDay($yesterday)) {
            return 0;
        }
        
        $streak = 1;
        $expectedDate = $lastActivityDate->copy()->subDay();

        foreach ($activities->slice(1) as $activity) {
            $activityDate = Carbon::parse($activity->activity_date);
            if ($activityDate->isSameDay($expectedDate)) {
                $streak++;
                $expectedDate->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }
}