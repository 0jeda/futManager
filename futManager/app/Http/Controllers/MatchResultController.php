<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\PlayerMatchStats;
use App\Services\BracketGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchResultController extends Controller
{
    public function __construct(
        private BracketGenerator $bracketGenerator
    ) {}

    public function update(Request $request, Game $match): RedirectResponse
    {
        $data = $request->validate([
            'home_goals' => 'required|integer|min:0',
            'away_goals' => 'required|integer|min:0',
            'player_stats' => 'nullable|array',
            'player_stats.*.player_id' => 'required|exists:players,id',
            'player_stats.*.team_id' => 'required|exists:teams,id',
            'player_stats.*.goals' => 'nullable|integer|min:0',
            'player_stats.*.assists' => 'nullable|integer|min:0',
            'player_stats.*.yellow_cards' => 'nullable|integer|min:0|max:2',
            'player_stats.*.red_cards' => 'nullable|integer|min:0|max:1',
        ]);

        DB::transaction(function () use ($data, $match) {
            $participants = $match->participants;
            $homeParticipant = $participants->where('is_home', true)->first();
            $awayParticipant = $participants->where('is_home', false)->first();

            if (!$homeParticipant || !$awayParticipant) {
                throw new \Exception(__('Partido inválido.'));
            }

            // Actualizar goles
            $homeParticipant->update(['goals' => $data['home_goals']]);
            $awayParticipant->update(['goals' => $data['away_goals']]);

            // Determinar ganador
            $winnerId = null;
            if ($data['home_goals'] > $data['away_goals']) {
                $winnerId = $homeParticipant->team_id;
                $homeParticipant->update(['points_awarded' => 3]);
                $awayParticipant->update(['points_awarded' => 0]);
            } elseif ($data['away_goals'] > $data['home_goals']) {
                $winnerId = $awayParticipant->team_id;
                $awayParticipant->update(['points_awarded' => 3]);
                $homeParticipant->update(['points_awarded' => 0]);
            } else {
                // Empate
                $homeParticipant->update(['points_awarded' => 1]);
                $awayParticipant->update(['points_awarded' => 1]);
            }

            // Actualizar estado del partido
            $match->update(['status' => 'completed']);

            // Guardar estadísticas de jugadores
            if (!empty($data['player_stats'])) {
                // Eliminar estadísticas anteriores si existen
                $match->playerStats()->delete();
                
                foreach ($data['player_stats'] as $stat) {
                    // Solo crear si hay alguna estadística
                    if (($stat['goals'] ?? 0) > 0 || 
                        ($stat['assists'] ?? 0) > 0 || 
                        ($stat['yellow_cards'] ?? 0) > 0 || 
                        ($stat['red_cards'] ?? 0) > 0) {
                        
                        PlayerMatchStats::create([
                            'match_id' => $match->id,
                            'player_id' => $stat['player_id'],
                            'team_id' => $stat['team_id'],
                            'goals' => $stat['goals'] ?? 0,
                            'assists' => $stat['assists'] ?? 0,
                            'yellow_cards' => $stat['yellow_cards'] ?? 0,
                            'red_cards' => $stat['red_cards'] ?? 0,
                        ]);
                    }
                }
            }

            // Si es bracket y hay ganador, avanzar en el bracket
            $tournament = $match->tournament;
            if ($tournament->is_bracket && $tournament->bracket_data && $winnerId) {
                $bracket = $tournament->bracket_data;
                
                // Buscar el partido en el bracket y avanzar ganador
                foreach ($bracket as $roundName => $matches) {
                    foreach ($matches as $position => $bracketMatch) {
                        if ($bracketMatch['match_id'] == $match->id) {
                            $updatedBracket = $this->bracketGenerator->advanceWinner(
                                $tournament,
                                $roundName,
                                $position,
                                $winnerId
                            );
                            
                            // Actualizar marcadores en el bracket
                            $updatedBracket[$roundName][$position]['score1'] = $data['home_goals'];
                            $updatedBracket[$roundName][$position]['score2'] = $data['away_goals'];
                            
                            // Solo marcar campeón si el partido es de la FINAL (no semifinales ni otras rondas)
                            if (strtolower($roundName) === 'final') {
                                $tournament->update([
                                    'bracket_data' => $updatedBracket,
                                    'winner_id' => $winnerId,
                                    'status' => 'finished'
                                ]);
                            } else {
                                $tournament->update(['bracket_data' => $updatedBracket]);
                            }
                            
                            break 2;
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('status', __('Resultado actualizado correctamente.'));
    }
}
