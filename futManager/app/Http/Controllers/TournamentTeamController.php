<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Tournament;
use App\Services\BracketGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TournamentTeamController extends Controller
{
    public function __construct(
        private BracketGenerator $bracketGenerator
    ) {}

    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'group' => 'nullable|string|max:50',
        ]);

        if ($tournament->teams()->where('team_id', $data['team_id'])->exists()) {
            return redirect()->back()->with('error', __('El equipo ya esta registrado en este torneo.'));
        }

        if ($tournament->is_bracket && $tournament->bracket_size) {
            if ($tournament->teams()->count() >= $tournament->bracket_size) {
                return redirect()->back()->with('error', __('El bracket ya esta completo con :size equipos.', ['size' => $tournament->bracket_size]));
            }
        }

        $tournament->teams()->attach($data['team_id'], ['group' => $data['group'] ?? null]);

        // Crear estructura del bracket en cuanto haya al menos un equipo para permitir asignar casillas
        if ($tournament->is_bracket && $tournament->bracket_size && !$tournament->bracket_data) {
            $tournament->update(['bracket_data' => $this->bracketGenerator->generate($tournament)]);
        }

        // Si ya se completo el tamano, asegurar que exista la estructura
        if ($tournament->is_bracket && $tournament->bracket_size && $tournament->teams()->count() === $tournament->bracket_size) {
            $tournament->update(['bracket_data' => $this->bracketGenerator->generate($tournament)]);
        }

        return redirect()->back()->with('status', __('Equipo agregado al torneo.'));
    }

    public function destroy(Tournament $tournament, Team $team): RedirectResponse
    {
        $tournament->teams()->detach($team->id);

        // Regenerar la estructura si el conteo baja para que siga habiendo casillas disponibles
        if ($tournament->is_bracket && $tournament->bracket_size && $tournament->teams()->count() < $tournament->bracket_size) {
            $tournament->update([
                'bracket_data' => $this->bracketGenerator->generate($tournament),
                'winner_id' => null,
            ]);
        }

        return redirect()->back()->with('status', __('Equipo removido del torneo.'));
    }
}
