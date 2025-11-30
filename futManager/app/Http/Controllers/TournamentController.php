<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\BracketGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function __construct(
        private BracketGenerator $bracketGenerator
    ) {}

    public function index(): View
    {
        $tournaments = Tournament::with('field')
            ->latest('start_date')
            ->paginate(12);

        return view('tournaments.index', compact('tournaments'));
    }

    public function create(): View
    {
        $fields = Field::where('is_active', true)->get();
        
        return view('tournaments.create', [
            'tournament' => new Tournament(),
            'fields' => $fields,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'field_id' => 'required|exists:fields,id',
            'category' => 'nullable|string|max:100',
            'format' => 'nullable|string|max:255',
            'status' => 'required|in:draft,active,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_bracket' => 'nullable|boolean',
            'bracket_size' => 'nullable|integer|in:4,8,16,32',
        ]);

        $data['is_bracket'] = $request->has('is_bracket');

        $tournament = Tournament::create($data);

        if ($tournament->is_bracket && $tournament->bracket_size) {
            $tournament->update([
                'bracket_data' => $this->bracketGenerator->generate($tournament),
            ]);
        }

        return redirect()->route('tournaments.index')->with('status', __('Torneo creado correctamente.'));
    }

    public function edit(Tournament $tournament): View
    {
        $fields = Field::where('is_active', true)->get();
        $teams = Team::where('is_active', true)->get();
        
        $tournament->load([
            'teams.players', 
            'matches.field', 
            'matches.participants.team',
            'matches.playerStats.player'
        ]);

        return view('tournaments.edit', compact('tournament', 'fields', 'teams'));
    }

    public function update(Request $request, Tournament $tournament): RedirectResponse
    {
        $previousSize = $tournament->bracket_size;
        $wasBracket = $tournament->is_bracket;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'field_id' => 'required|exists:fields,id',
            'category' => 'nullable|string|max:100',
            'format' => 'nullable|string|max:255',
            'status' => 'required|in:draft,active,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_bracket' => 'nullable|boolean',
            'bracket_size' => 'nullable|integer|in:4,8,16,32',
        ]);

        $data['is_bracket'] = $request->has('is_bracket');

        $tournament->update($data);

        // Regenerar la estructura del bracket si se activa, no existe o cambia el tamano
        if ($tournament->is_bracket && $tournament->bracket_size) {
            if (!$tournament->bracket_data || !$wasBracket || $previousSize !== $tournament->bracket_size) {
                $tournament->update([
                    'bracket_data' => $this->bracketGenerator->generate($tournament),
                    'winner_id' => null,
                ]);
            }
        }

        return redirect()->route('tournaments.index')->with('status', __('Torneo actualizado correctamente.'));
    }

    public function destroy(Tournament $tournament): RedirectResponse
    {
        $tournament->delete();

        return redirect()->route('tournaments.index')->with('status', __('Torneo eliminado.'));
    }
}
