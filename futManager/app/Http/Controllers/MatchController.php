<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Game;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'match_date' => 'required|date',
            'match_time' => 'required|date_format:H:i',
            'round' => 'nullable|string|max:100',
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
        ], [
            'away_team_id.different' => 'Un equipo no puede jugar contra sí mismo.',
            'match_date.required' => 'La fecha del partido es obligatoria.',
            'match_time.required' => 'La hora del partido es obligatoria.',
            'match_time.date_format' => 'La hora debe estar en formato HH:MM.',
        ]);

        // Combinar fecha y hora
        $scheduledAt = Carbon::parse($data['match_date'] . ' ' . $data['match_time']);

        // Validar rango de fechas del torneo
        if ($tournament->start_date && $scheduledAt->lt($tournament->start_date->startOfDay())) {
            return redirect()->back()->with('error', __('La fecha del partido debe ser igual o posterior al inicio del torneo.'))->withInput();
        }
        if ($tournament->end_date && $scheduledAt->gt($tournament->end_date->endOfDay())) {
            return redirect()->back()->with('error', __('La fecha del partido debe ser anterior o igual al fin del torneo.'))->withInput();
        }

        // Verificar disponibilidad de la cancha en ese horario
        $existingMatch = Game::where('field_id', $data['field_id'])
            ->where('scheduled_at', $scheduledAt)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingMatch) {
            return redirect()->back()->with('error', __('La cancha ya está ocupada en ese horario.'))->withInput();
        }

        // Crear el partido
        $match = $tournament->matches()->create([
            'field_id' => $data['field_id'],
            'scheduled_at' => $scheduledAt,
            'round' => $data['round'] ?? null,
            'status' => 'scheduled',
        ]);

        // Crear los participantes
        $match->participants()->create([
            'team_id' => $data['home_team_id'],
            'is_home' => true,
            'goals' => 0,
            'points_awarded' => 0,
        ]);

        $match->participants()->create([
            'team_id' => $data['away_team_id'],
            'is_home' => false,
            'goals' => 0,
            'points_awarded' => 0,
        ]);

        // Si es bracket, vincular el partido con el bracket
        if ($tournament->is_bracket && $tournament->bracket_data) {
            $bracket = $tournament->bracket_data;
            
            // Buscar el slot correspondiente en el bracket
            foreach ($bracket as $roundName => &$matches) {
                foreach ($matches as &$bracketMatch) {
                    if ($bracketMatch['team1_id'] == $data['home_team_id'] && 
                        $bracketMatch['team2_id'] == $data['away_team_id'] &&
                        $bracketMatch['match_id'] === null) {
                        $bracketMatch['match_id'] = $match->id;
                        $tournament->update(['bracket_data' => $bracket]);
                        break 2;
                    }
                }
            }
        }

        return redirect()->back()->with('status', __('Partido creado correctamente.'));
    }

    public function destroy(Game $match): RedirectResponse
    {
        $match->participants()->delete();
        $match->delete();

        return redirect()->back()->with('status', __('Partido eliminado.'));
    }
}
