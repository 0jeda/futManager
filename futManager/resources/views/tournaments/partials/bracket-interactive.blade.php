@php
    /** @var \App\Models\Tournament $tournament */
    $tournament = $tournament ?? null;
    $rawBracket = $tournament?->bracket_data ?? [];
    $fields = $fields ?? \App\Models\Field::where('is_active', true)->get();

    $roundOrder = match($tournament?->bracket_size) {
        32 => ['Dieciseisavos', 'Octavos', 'Cuartos', 'Semifinales', 'Final'],
        16 => ['Octavos', 'Cuartos', 'Semifinales', 'Final'],
        8  => ['Cuartos', 'Semifinales', 'Final'],
        4  => ['Semifinales', 'Final'],
        default => array_keys($rawBracket),
    };

    $bracket = [];
    foreach ($roundOrder as $roundName) {
        if (isset($rawBracket[$roundName])) {
            $bracket[$roundName] = $rawBracket[$roundName];
        }
    }
    foreach ($rawBracket as $roundName => $matches) {
        if (!isset($bracket[$roundName])) {
            $bracket[$roundName] = $matches;
        }
    }

    $firstRoundName = array_key_first($bracket);

    $matchIds = collect($bracket)
        ->flatMap(fn($matches) => collect($matches)->pluck('match_id'))
        ->filter()
        ->values();
    $scheduledMatches = $matchIds->isNotEmpty()
        ? \App\Models\Game::with(['field', 'participants.team'])->whereIn('id', $matchIds)->orderBy('scheduled_at')->get()
        : collect();
@endphp

@if(!empty($bracket))
<div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Bracket de Eliminación') }}</h2>
            @if($tournament->winner_id)
                @php $champion = \App\Models\Team::find($tournament->winner_id); @endphp
                <div class="flex items-center gap-2 mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                    <span class="text-2xl material-symbols-rounded text-amber-500 align-middle">emoji_events</span>
                    @if($champion?->logo_path)
                        <img src="{{ asset('storage/'.$champion->logo_path) }}" alt="{{ $champion->name }}" class="w-8 h-8 rounded object-cover" />
                    @endif
                    <div>
                        <span class="text-xs text-amber-600 dark:text-amber-400 block">{{ __('Campeón del Torneo') }}</span>
                        <span class="font-bold text-amber-900 dark:text-amber-100">{{ $champion?->name }}</span>
                    </div>
                </div>
            @endif
        </div>
        <div class="text-xs bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
            <div class="flex items-start gap-2">
                <span class="text-lg material-symbols-rounded text-amber-500 align-middle leading-none">lightbulb</span>
                <div class="text-blue-900 dark:text-blue-100">
                    <p class="font-semibold mb-1">Cómo funciona:</p>
                    <ol class="list-decimal list-inside space-y-0.5 text-blue-800 dark:text-blue-200">
                        <li>Asigna equipos en <strong>la primera ronda</strong></li>
                        <li>Programa partidos → registra resultados → ganadores avanzan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <div class="inline-flex gap-8 min-w-full pb-4">
            @foreach($bracket as $roundName => $matches)
                <div class="flex flex-col justify-around min-w-[350px]">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4 text-center uppercase tracking-wide">
                        {{ $roundName }}
                    </h3>
                    
                    <div class="flex flex-col justify-around h-full space-y-8">
                        @foreach($matches as $position => $match)
                            @php
                                $matchModel = isset($match['match_id']) && $match['match_id'] ? \App\Models\Game::with(['playerStats.player'])->find($match['match_id']) : null;
                                $isCompleted = $matchModel && $matchModel->status === 'completed';
                                $canEdit = ($roundName === $firstRoundName) && !$isCompleted;
                                $homeTeam = $match['team1_id'] ? \App\Models\Team::with('players')->find($match['team1_id']) : null;
                                $awayTeam = $match['team2_id'] ? \App\Models\Team::with('players')->find($match['team2_id']) : null;
                            @endphp
                            
                            <div class="relative group">
                                <div class="rounded-lg border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-700' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-800 overflow-hidden shadow-sm hover:shadow-lg transition-all">
                                    
                                    {{-- Team 1 Slot --}}
                                    @include('tournaments.partials.bracket-team-slot', [
                                        'match' => $match,
                                        'position' => $position,
                                        'roundName' => $roundName,
                                        'teamSlot' => 'team1',
                                        'teamId' => $match['team1_id'],
                                        'teamName' => $match['team1_name'],
                                        'score' => $match['score1'] ?? null,
                                        'isWinner' => $match['winner_id'] == $match['team1_id'],
                                        'canEdit' => $canEdit,
                                        'matchModel' => $matchModel,
                                        'isFirstRound' => ($roundName === $firstRoundName),
                                    ])
                                    
                                    <div class="h-px bg-neutral-200 dark:bg-neutral-700"></div>
                                    
                                    {{-- Team 2 Slot --}}
                                    @include('tournaments.partials.bracket-team-slot', [
                                        'match' => $match,
                                        'position' => $position,
                                        'roundName' => $roundName,
                                        'teamSlot' => 'team2',
                                        'teamId' => $match['team2_id'],
                                        'teamName' => $match['team2_name'],
                                        'score' => $match['score2'] ?? null,
                                        'isWinner' => $match['winner_id'] == $match['team2_id'],
                                        'canEdit' => $canEdit,
                                        'matchModel' => $matchModel,
                                        'isFirstRound' => ($roundName === $firstRoundName),
                                    ])
                                    
                                    {{-- Match Actions --}}
                                    @if($matchModel)
                                        <div class="px-3 py-2 bg-neutral-50 dark:bg-neutral-900/50 border-t border-neutral-200 dark:border-neutral-700">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="flex items-center gap-1 text-xs {{ $isCompleted ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-amber-600 dark:text-amber-400' }}">
                                                    <span class="material-symbols-rounded text-[14px] align-middle leading-none">{{ $isCompleted ? 'check_circle' : 'schedule' }}</span>
                                                    <span>{{ $isCompleted ? 'Finalizado' : 'Programado' }}</span>
                                                </span>
                                                @if(!$isCompleted)
                                                    <flux:modal.trigger name="result-bracket-{{ $match['match_id'] }}">
                                                        <button type="button" class="rounded-md bg-indigo-600 px-3 py-1 text-xs font-medium text-white hover:bg-indigo-500">
                                                            {{ __('Registrar Resultado') }}
                                                        </button>
                                                    </flux:modal.trigger>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if(!$isCompleted)
                                            @include('tournaments.partials.bracket-result-modal', [
                                                'match' => $matchModel,
                                                'homeTeam' => $homeTeam,
                                                'awayTeam' => $awayTeam,
                                                'modalId' => 'result-bracket-' . $match['match_id']
                                            ])
                                        @endif
                                    @elseif($match['team1_id'] && $match['team2_id'] && !$match['match_id'])
                                        <div class="px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border-t border-amber-200 dark:border-amber-700 space-y-2">
                                            <flux:modal.trigger name="schedule-{{ $roundName }}-{{ $position }}">
                                                <button type="button" class="w-full rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">
                                                    Programar partido
                                                </button>
                                            </flux:modal.trigger>
                                            <p class="text-xs text-amber-700 dark:text-amber-300 text-center">Equipos asignados sin partido</p>
                                        </div>
                                        <flux:modal name="schedule-{{ $roundName }}-{{ $position }}" class="max-w-2xl">
                                            <form action="{{ route('tournaments.bracket.createMatch', $tournament) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <input type="hidden" name="round" value="{{ $roundName }}">
                                                <input type="hidden" name="position" value="{{ $position }}">
                                                
                                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                                    Programar partido - {{ $roundName }} (Partido {{ $position + 1 }})
                                                </h3>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                                            Cancha
                                                        </label>
                                                        <select name="field_id" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100">
                                                            <option value="">{{ __('-- Seleccionar --') }}</option>
                                                            @foreach($fields as $field)
                                                                <option value="{{ $field->id }}" @selected(old('field_id') == $field->id)>{{ $field->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                                            Jornada / Ronda (opcional)
                                                        </label>
                                                        <input type="text" name="round_label" value="{{ old('round_label', $roundName) }}" class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100" />
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                                            Fecha
                                                        </label>
                                                        <input type="date" name="match_date" value="{{ old('match_date') }}" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100" />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                                            Hora
                                                        </label>
                                                        <input type="time" name="match_time" value="{{ old('match_time') }}" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100" />
                                                    </div>
                                                </div>
                                                
                                                <div class="flex justify-end gap-2 pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                                    <flux:modal.close>
                                                        <button type="button" class="rounded-md bg-neutral-200 dark:bg-neutral-700 px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-300 dark:hover:bg-neutral-600">
                                                            Cancelar
                                                        </button>
                                                    </flux:modal.close>
                                                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                                                        Guardar
                                                    </button>
                                                </div>
                                            </form>
                                        </flux:modal>
                                    @endif
                                </div>
                                
                                @if(!$loop->parent->last)
                                    <div class="absolute top-1/2 -right-8 w-8 h-0.5 bg-neutral-300 dark:bg-neutral-600 z-0"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

    @if($scheduledMatches->count())
        <div class="mt-6 pt-4 border-t border-neutral-200 dark:border-neutral-700">
            <h4 class="text-sm font-semibold text-neutral-800 dark:text-neutral-200 mb-3">Partidos programados del bracket</h4>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach($scheduledMatches as $scheduled)
                    @php
                        $home = $scheduled->participants->where('is_home', true)->first();
                        $away = $scheduled->participants->where('is_home', false)->first();
                    @endphp
                    <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-3 shadow-sm">
                        <div class="flex items-center justify-between text-xs text-neutral-600 dark:text-neutral-400 mb-2">
                            <span>{{ $scheduled->round ?? 'Partido' }}</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-rounded text-[14px] text-rose-500 align-middle leading-none">place</span> {{ $scheduled->field?->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $home?->team?->name }}</span>
                            </div>
                            <span class="text-sm text-neutral-500 dark:text-neutral-400">vs</span>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $away?->team?->name }}</span>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-neutral-600 dark:text-neutral-400">
                            <span class="flex items-center gap-1"><span class="material-symbols-rounded text-[14px] text-indigo-500 align-middle leading-none">event</span> {{ $scheduled->scheduled_at?->format('d/m/Y H:i') }}</span>
                            <span class="flex items-center gap-1 {{ $scheduled->status === 'completed' ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $scheduled->status === 'completed' ? 'Finalizado' : 'Programado' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif
