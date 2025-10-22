<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NavbarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentStreak = $this->calculateCurrentStreak($user);
            $userHearts = '5/5'; // Valor de exemplo para o sistema de vidas

            $view->with([
                'currentStreak' => $currentStreak,
                'userHearts' => $userHearts
            ]);
        }
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

        if ($activities->isEmpty()) return 0;

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
