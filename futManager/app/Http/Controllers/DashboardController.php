<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $now = Carbon::now();
        $monthParam = request()->get('month');
        try {
            $currentMonth = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth() : $now->copy()->startOfMonth();
        } catch (\Exception $e) {
            $currentMonth = $now->copy()->startOfMonth();
        }

        $startRange = $currentMonth->copy()->startOfMonth();
        $endRange = $currentMonth->copy()->endOfMonth();

        $upcomingMatches = Game::with(['participants.team', 'field', 'tournament'])
            ->whereBetween('scheduled_at', [$startRange, $endRange])
            ->orderBy('scheduled_at')
            ->get();

        $matchesByDate = $upcomingMatches->groupBy(fn($match) => optional($match->scheduled_at)->toDateString());

        $calendarEvents = $matchesByDate->map(function ($matches) {
            return $matches->map(function ($match) {
                $home = $match->participants->where('is_home', true)->first();
                $away = $match->participants->where('is_home', false)->first();
                return [
                    'time' => optional($match->scheduled_at)->format('H:i'),
                    'date' => optional($match->scheduled_at)->toDateString(),
                    'home' => $home?->team?->name ?? 'TBD',
                    'away' => $away?->team?->name ?? 'TBD',
                    'field' => $match->field?->name,
                    'tournament' => $match->tournament?->name,
                    'round' => $match->round,
                    'status' => $match->status,
                ];
            })->values();
        });

        $activeTournaments = Tournament::where('status', 'active')->count();
        $teamsCount = Team::count();
        $playersCount = Player::count();

        return view('dashboard', [
            'matchesByDate' => $matchesByDate,
            'activeTournaments' => $activeTournaments,
            'teamsCount' => $teamsCount,
            'playersCount' => $playersCount,
            'calendarEvents' => $calendarEvents,
            'currentMonth' => $currentMonth,
        ]);
    }
}
