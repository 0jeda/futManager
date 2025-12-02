<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;

class PlayerMatchController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $player = $user->player;

        if (!$player) {
            abort(403, 'User is not a player.');
        }

        $player->load('team');
        $team = $player->team;
        $teamId = $player->team_id;

        $allMatches = Game::whereHas('participants', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->with(['participants.team', 'field', 'tournament'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $upcomingMatches = $allMatches->where('scheduled_at', '>=', now());
        $pastMatches = $allMatches->where('scheduled_at', '<', now())->sortByDesc('scheduled_at');

        return view('player.matches', compact('player', 'team', 'upcomingMatches', 'pastMatches'));
    }
}
