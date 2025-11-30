<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;
use App\Services\BracketGenerator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BracketSlotController extends Controller
{
    public function __construct(
        private BracketGenerator $bracketGenerator
    ) {}

    /**
     * Asignar un equipo a un slot especifico del bracket.
     */
    public function assignTeam(Request $request, Tournament $tournament): RedirectResponse
    {
        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'round' => 'required|string',
            'position' => 'required|integer',
            'slot' => 'required|in:team1,team2',
        ]);

        if (!$tournament->is_bracket || !$tournament->bracket_data) {
            return redirect()->back()->with('error', __('Este torneo no es de tipo bracket.'))->withInput();
        }

        $bracket = $tournament->bracket_data;
        $matchSlot = $bracket[$data['round']][$data['position']] ?? null;

        if (!$matchSlot) {
            return redirect()->back()->with('error', __('No se encontro la casilla seleccionada.'))->withInput();
        }

        if (!empty($matchSlot['match_id'])) {
            return redirect()->back()->with('error', __('No se puede reasignar un equipo porque el partido ya fue programado.'))->withInput();
        }

        foreach ($bracket as $roundName => $matches) {
            foreach ($matches as $match) {
                if ($match['team1_id'] == $data['team_id'] || $match['team2_id'] == $data['team_id']) {
                    return redirect()->back()->with('error', __('Este equipo ya esta asignado en el bracket.'))->withInput();
                }
            }
        }

        $slotKey = $data['slot'] === 'team1' ? 'team1_id' : 'team2_id';
        $nameKey = $data['slot'] === 'team1' ? 'team1_name' : 'team2_name';

        $team = \App\Models\Team::find($data['team_id']);
        $bracket[$data['round']][$data['position']][$slotKey] = $team->id;
        $bracket[$data['round']][$data['position']][$nameKey] = $team->name;

        if (!$tournament->teams()->where('team_id', $team->id)->exists()) {
            $tournament->teams()->attach($team->id);
        }

        $tournament->update(['bracket_data' => $bracket]);

        return redirect()->back()->with('status', __('Equipo asignado correctamente.'));
    }

    /**
     * Remover un equipo de un slot del bracket.
     */
    public function removeTeam(Request $request, Tournament $tournament): RedirectResponse
    {
        $data = $request->validate([
            'round' => 'required|string',
            'position' => 'required|integer',
            'slot' => 'required|in:team1,team2',
        ]);

        if (!$tournament->is_bracket || !$tournament->bracket_data) {
            return redirect()->back()->with('error', __('Este torneo no es de tipo bracket.'))->withInput();
        }

        $bracket = $tournament->bracket_data;

        if (!isset($bracket[$data['round']][$data['position']])) {
            return redirect()->back()->with('error', __('No se encontro la casilla seleccionada.'))->withInput();
        }

        if (!empty($bracket[$data['round']][$data['position']]['match_id'])) {
            return redirect()->back()->with('error', __('No se puede remover un equipo cuando ya existe un partido programado.'))->withInput();
        }

        $slotKey = $data['slot'] === 'team1' ? 'team1_id' : 'team2_id';
        $nameKey = $data['slot'] === 'team1' ? 'team1_name' : 'team2_name';

        $bracket[$data['round']][$data['position']][$slotKey] = null;
        $bracket[$data['round']][$data['position']][$nameKey] = 'TBD';

        $tournament->update(['bracket_data' => $bracket]);

        return redirect()->back()->with('status', __('Equipo removido del bracket.'));
    }

    /**
     * Crear partido cuando ambos equipos ya estan asignados (programar partido).
     */
    public function createMatch(Request $request, Tournament $tournament): RedirectResponse
    {
        $data = $request->validate([
            'round' => 'required|string',
            'position' => 'required|integer',
            'field_id' => 'required|exists:fields,id',
            'match_date' => 'required|date',
            'match_time' => 'required|date_format:H:i',
            'round_label' => 'nullable|string|max:100',
        ], [
            'match_time.date_format' => 'La hora debe estar en formato HH:MM.',
        ]);

        if (!$tournament->is_bracket || !$tournament->bracket_data) {
            return redirect()->back()->with('error', __('Este torneo no es de tipo bracket.'))->withInput();
        }

        $bracket = $tournament->bracket_data;
        $match = $bracket[$data['round']][$data['position']];

        if (!$match['team1_id'] || !$match['team2_id']) {
            return redirect()->back()->with('error', __('Ambos equipos deben estar asignados.'))->withInput();
        }

        if ($match['match_id']) {
            return redirect()->back()->with('error', __('El partido ya existe.'))->withInput();
        }

        $scheduledAt = Carbon::parse($data['match_date'] . ' ' . $data['match_time']);

        if ($tournament->start_date && $scheduledAt->lt($tournament->start_date->startOfDay())) {
            return redirect()->back()->with('error', __('La fecha del partido debe ser igual o posterior al inicio del torneo.'))->withInput();
        }
        if ($tournament->end_date && $scheduledAt->gt($tournament->end_date->endOfDay())) {
            return redirect()->back()->with('error', __('La fecha del partido debe ser anterior o igual al fin del torneo.'))->withInput();
        }

        $game = $tournament->matches()->create([
            'field_id' => $data['field_id'],
            'scheduled_at' => $scheduledAt,
            'round' => $data['round_label'] ?? $data['round'],
            'status' => 'scheduled',
        ]);

        $game->participants()->create([
            'team_id' => $match['team1_id'],
            'is_home' => true,
            'goals' => 0,
            'points_awarded' => 0,
        ]);

        $game->participants()->create([
            'team_id' => $match['team2_id'],
            'is_home' => false,
            'goals' => 0,
            'points_awarded' => 0,
        ]);

        $bracket[$data['round']][$data['position']]['match_id'] = $game->id;
        $tournament->update(['bracket_data' => $bracket]);

        return redirect()->back()->with('status', __('Partido creado correctamente.'));
    }
}
