@php
    /** @var \App\Models\Tournament $tournament */
    $tournament = $tournament ?? null;
    $teams = $teams ?? collect();
    $tournamentTeams = $tournament?->teams ?? collect();
    $matches = $tournament?->matches ?? collect();
@endphp

{{-- Equipos participantes --}}
<div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Equipos participantes') }}</h2>
        @if(!$tournamentTeams->isEmpty())
            <span class="text-xs text-neutral-500">{{ $tournamentTeams->count() }} {{ __('equipos') }}</span>
        @endif
    </div>

    {{-- Mensaje de error --}}
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Lista de equipos --}}
    @if($tournamentTeams->isEmpty())
        <p class="text-sm text-neutral-500 mb-4">{{ __('No hay equipos en este torneo aún.') }}</p>
    @else
        <div class="space-y-2 mb-4">
            @foreach($tournamentTeams as $team)
                <div class="flex items-center justify-between p-3 rounded-xl border border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex items-center gap-3">
                        @if($team->logo_path)
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ asset('storage/'.$team->logo_path) }}" alt="{{ $team->name }}" class="w-full h-full object-cover" />
                            </div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $team->name }}</div>
                            @if($team->pivot->group)
                                <div class="text-xs text-neutral-500">{{ __('Grupo') }}: {{ $team->pivot->group }}</div>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('tournaments.teams.destroy', [$tournament, $team]) }}" method="POST" onsubmit="return confirm('¿Remover equipo del torneo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md bg-rose-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-600">
                            {{ __('Remover') }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Formulario para agregar equipo --}}
    <details class="group">
        <summary class="cursor-pointer list-none">
            <div class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                <svg class="w-4 h-4 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Agregar equipo') }}
            </div>
        </summary>
        
        <form action="{{ route('tournaments.teams.store', $tournament) }}" method="POST" class="mt-4 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="team_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        {{ __('Equipo') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="team_id" id="team_id" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm">
                        <option value="">{{ __('Seleccionar equipo') }}</option>
                        @foreach($teams->whereNotIn('id', $tournamentTeams->pluck('id')) as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="group" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        {{ __('Grupo') }}
                    </label>
                    <input type="text" name="group" id="group" placeholder="{{ __('Ej: A, B, 1, 2') }}" class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm" />
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    {{ __('Agregar') }}
                </button>
            </div>
        </form>
    </details>
</div>

{{-- Calendario de partidos --}}
<div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Calendario de partidos') }}</h2>
        @if(!$matches->isEmpty())
            <span class="text-xs text-neutral-500">{{ $matches->count() }} {{ __('partidos') }}</span>
        @endif
    </div>

    {{-- Lista de partidos --}}
    @if($matches->isEmpty())
        <p class="text-sm text-neutral-500 mb-4">{{ __('No hay partidos programados aún.') }}</p>
    @else
        <div class="space-y-3 mb-6">
            @foreach($matches->sortBy('scheduled_at') as $match)
                @php
                    $homeTeam = $match->participants->where('is_home', true)->first()?->team;
                    $awayTeam = $match->participants->where('is_home', false)->first()?->team;
                    $homeStats = $match->playerStats->where('team_id', $homeTeam?->id)->filter(fn($s) => $s->goals > 0 || $s->yellow_cards > 0 || $s->red_cards > 0);
                    $awayStats = $match->playerStats->where('team_id', $awayTeam?->id)->filter(fn($s) => $s->goals > 0 || $s->yellow_cards > 0 || $s->red_cards > 0);
                @endphp
                <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400 mb-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $match->scheduled_at->format('d/m/Y H:i') }}</span>
                                <span class="mx-1">•</span>
                                <span>{{ $match->field->name }}</span>
                                @if($match->round)
                                    <span class="mx-1">•</span>
                                    <span>{{ $match->round }}</span>
                                @endif
                            </div>
                            
                            <div class="space-y-3">
                                {{-- Equipo Local --}}
                                <div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-2 flex-1">
                                            @if($homeTeam?->logo_path)
                                                <img src="{{ asset('storage/'.$homeTeam->logo_path) }}" alt="{{ $homeTeam->name }}" class="w-8 h-8 rounded object-cover" />
                                            @endif
                                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $homeTeam?->name ?? __('TBD') }}</span>
                                        </div>
                                        
                                        @if($match->status == 'completed')
                                            <span class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                                                {{ $match->participants->where('is_home', true)->first()?->goals ?? 0 }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Estadísticas equipo local --}}
                                    @if($match->status == 'completed' && $homeStats->count() > 0)
                                        <div class="ml-10 mt-1 space-y-1">
                                            @foreach($homeStats as $stat)
                                                <div class="flex items-center gap-2 text-xs text-neutral-600 dark:text-neutral-400">
                                                    @if($stat->player->photo_path)
                                                        <img src="{{ asset('storage/'.$stat->player->photo_path) }}" alt="{{ $stat->player->first_name }}" class="w-4 h-4 rounded-full object-cover" />
                                                    @endif
                                                    <span>{{ $stat->player->first_name }} {{ substr($stat->player->last_name, 0, 1) }}.</span>
                                                    @if($stat->goals > 0)
                                                        <span class="text-green-600 dark:text-green-400">⚽{{ $stat->goals }}</span>
                                                    @endif
                                                    @if($stat->assists > 0)
                                                        <span class="text-blue-600 dark:text-blue-400">🎯{{ $stat->assists }}</span>
                                                    @endif
                                                    @if($stat->yellow_cards > 0)
                                                        <span>🟨</span>
                                                    @endif
                                                    @if($stat->red_cards > 0)
                                                        <span>🟥</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Equipo Visitante --}}
                                <div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-2 flex-1">
                                            @if($awayTeam?->logo_path)
                                                <img src="{{ asset('storage/'.$awayTeam->logo_path) }}" alt="{{ $awayTeam->name }}" class="w-8 h-8 rounded object-cover" />
                                            @endif
                                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $awayTeam?->name ?? __('TBD') }}</span>
                                        </div>
                                        
                                        @if($match->status == 'completed')
                                            <span class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                                                {{ $match->participants->where('is_home', false)->first()?->goals ?? 0 }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Estadísticas equipo visitante --}}
                                    @if($match->status == 'completed' && $awayStats->count() > 0)
                                        <div class="ml-10 mt-1 space-y-1">
                                            @foreach($awayStats as $stat)
                                                <div class="flex items-center gap-2 text-xs text-neutral-600 dark:text-neutral-400">
                                                    @if($stat->player->photo_path)
                                                        <img src="{{ asset('storage/'.$stat->player->photo_path) }}" alt="{{ $stat->player->first_name }}" class="w-4 h-4 rounded-full object-cover" />
                                                    @endif
                                                    <span>{{ $stat->player->first_name }} {{ substr($stat->player->last_name, 0, 1) }}.</span>
                                                    @if($stat->goals > 0)
                                                        <span class="text-green-600 dark:text-green-400">⚽{{ $stat->goals }}</span>
                                                    @endif
                                                    @if($stat->assists > 0)
                                                        <span class="text-blue-600 dark:text-blue-400">🎯{{ $stat->assists }}</span>
                                                    @endif
                                                    @if($stat->yellow_cards > 0)
                                                        <span>🟨</span>
                                                    @endif
                                                    @if($stat->red_cards > 0)
                                                        <span>🟥</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            @if($match->status == 'completed')
                                <div class="mt-2 pt-2 border-t border-neutral-200 dark:border-neutral-700">
                                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">{{ __('✓ Finalizado') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            @if($match->status != 'completed')
                                <flux:modal.trigger name="result-match-{{ $match->id }}">
                                    <button type="button" class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-500">
                                        {{ __('Resultado') }}
                                    </button>
                                </flux:modal.trigger>
                            @endif
                            
                            <form action="{{ route('matches.destroy', $match) }}" method="POST" onsubmit="return confirm('¿Eliminar partido?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-md bg-rose-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-600">
                                    {{ __('Eliminar') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    {{-- Modal para registrar resultado --}}
                    @if($match->status != 'completed')
                        @php
                            $homeTeamPlayers = $homeTeam?->players ?? collect();
                            $awayTeamPlayers = $awayTeam?->players ?? collect();
                        @endphp
                        
                        <flux:modal name="result-match-{{ $match->id }}" class="max-w-4xl">
                            <form action="{{ route('matches.result.update', $match) }}" method="POST" class="space-y-6" x-data="matchResultForm()">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Registrar resultado del partido') }}</h3>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $match->scheduled_at->format('d/m/Y H:i') }} - {{ $match->field?->name }}</p>
                                </div>
                                
                                {{-- Resultado principal --}}
                                <div class="grid grid-cols-2 gap-4 p-4 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg">
                                    <div>
                                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                            <div class="flex items-center gap-2">
                                                @if($homeTeam?->logo_path)
                                                    <img src="{{ asset('storage/'.$homeTeam->logo_path) }}" alt="{{ $homeTeam->name }}" class="w-6 h-6 rounded object-cover" />
                                                @endif
                                                <span>{{ $homeTeam?->name ?? __('Local') }}</span>
                                            </div>
                                        </label>
                                        <input type="number" name="home_goals" x-model="homeGoals" min="0" required class="block w-full text-2xl font-bold text-center rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100" />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                            <div class="flex items-center gap-2">
                                                @if($awayTeam?->logo_path)
                                                    <img src="{{ asset('storage/'.$awayTeam->logo_path) }}" alt="{{ $awayTeam->name }}" class="w-6 h-6 rounded object-cover" />
                                                @endif
                                                <span>{{ $awayTeam?->name ?? __('Visitante') }}</span>
                                            </div>
                                        </label>
                                        <input type="number" name="away_goals" x-model="awayGoals" min="0" required class="block w-full text-2xl font-bold text-center rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100" />
                                    </div>
                                </div>
                                
                                {{-- Estadísticas de jugadores --}}
                                <div class="space-y-4">
                                    <h4 class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Estadísticas de jugadores') }}</h4>
                                    
                                    {{-- Equipo Local --}}
                                    @if($homeTeamPlayers->count() > 0)
                                        <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4">
                                            <h5 class="text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3 flex items-center gap-2">
                                                @if($homeTeam?->logo_path)
                                                    <img src="{{ asset('storage/'.$homeTeam->logo_path) }}" alt="{{ $homeTeam->name }}" class="w-5 h-5 rounded object-cover" />
                                                @endif
                                                {{ $homeTeam?->name }}
                                            </h5>
                                            
                                            <div class="space-y-2">
                                                @foreach($homeTeamPlayers as $index => $player)
                                                    <div class="grid grid-cols-12 gap-2 items-center text-sm" x-data="{ show: false }">
                                                        <div class="col-span-4 flex items-center gap-2">
                                                            @if($player->photo_path)
                                                                <img src="{{ asset('storage/'.$player->photo_path) }}" alt="{{ $player->first_name }}" class="w-6 h-6 rounded-full object-cover" />
                                                            @endif
                                                            <span class="text-neutral-900 dark:text-neutral-100">
                                                                @if($player->number)<span class="font-bold">#{{ $player->number }}</span> @endif
                                                                {{ $player->first_name }} {{ $player->last_name }}
                                                            </span>
                                                        </div>
                                                        
                                                        <template x-if="show">
                                                            <div class="col-span-8 grid grid-cols-4 gap-2">
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('⚽ Goles') }}</label>
                                                                    <input type="number" :name="'player_stats[home_{{ $index }}][goals]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                    <input type="hidden" :name="'player_stats[home_{{ $index }}][player_id]'" value="{{ $player->id }}" />
                                                                    <input type="hidden" :name="'player_stats[home_{{ $index }}][team_id]'" value="{{ $homeTeam->id }}" />
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('🎯 Asist.') }}</label>
                                                                    <input type="number" :name="'player_stats[home_{{ $index }}][assists]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('🟨 Amar.') }}</label>
                                                                    <input type="number" :name="'player_stats[home_{{ $index }}][yellow_cards]'" min="0" max="2" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('🟥 Roja') }}</label>
                                                                    <input type="number" :name="'player_stats[home_{{ $index }}][red_cards]'" min="0" max="1" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                </div>
                                                            </div>
                                                        </template>
                                                        
                                                        <div class="col-span-8 text-right" x-show="!show">
                                                            <button type="button" @click="show = true" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                                                {{ __('+ Agregar estadísticas') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Equipo Visitante --}}
                                    @if($awayTeamPlayers->count() > 0)
                                        <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4">
                                            <h5 class="text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3 flex items-center gap-2">
                                                @if($awayTeam?->logo_path)
                                                    <img src="{{ asset('storage/'.$awayTeam->logo_path) }}" alt="{{ $awayTeam->name }}" class="w-5 h-5 rounded object-cover" />
                                                @endif
                                                {{ $awayTeam?->name }}
                                            </h5>
                                            
                                            <div class="space-y-2">
                                                @foreach($awayTeamPlayers as $index => $player)
                                                    <div class="grid grid-cols-12 gap-2 items-center text-sm" x-data="{ show: false }">
                                                        <div class="col-span-4 flex items-center gap-2">
                                                            @if($player->photo_path)
                                                                <img src="{{ asset('storage/'.$player->photo_path) }}" alt="{{ $player->first_name }}" class="w-6 h-6 rounded-full object-cover" />
                                                            @endif
                                                            <span class="text-neutral-900 dark:text-neutral-100">
                                                                @if($player->number)<span class="font-bold">#{{ $player->number }}</span> @endif
                                                                {{ $player->first_name }} {{ $player->last_name }}
                                                            </span>
                                                        </div>
                                                        
                                                        <template x-if="show">
                                                            <div class="col-span-8 grid grid-cols-4 gap-2">
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('⚽ Goles') }}</label>
                                                                    <input type="number" :name="'player_stats[away_{{ $index }}][goals]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                    <input type="hidden" :name="'player_stats[away_{{ $index }}][player_id]'" value="{{ $player->id }}" />
                                                                    <input type="hidden" :name="'player_stats[away_{{ $index }}][team_id]'" value="{{ $awayTeam->id }}" />
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('🎯 Asist.') }}</label>
                                                                    <input type="number" :name="'player_stats[away_{{ $index }}][assists]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('🟨 Amar.') }}</label>
                                                                    <input type="number" :name="'player_stats[away_{{ $index }}][yellow_cards]'" min="0" max="2" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">{{ __('🟥 Roja') }}</label>
                                                                    <input type="number" :name="'player_stats[away_{{ $index }}][red_cards]'" min="0" max="1" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                                </div>
                                                            </div>
                                                        </template>
                                                        
                                                        <div class="col-span-8 text-right" x-show="!show">
                                                            <button type="button" @click="show = true" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                                                {{ __('+ Agregar estadísticas') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex justify-end gap-2 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                                    <flux:modal.close>
                                        <button type="button" class="rounded-md bg-neutral-200 dark:bg-neutral-700 px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-300 dark:hover:bg-neutral-600">
                                            {{ __('Cancelar') }}
                                        </button>
                                    </flux:modal.close>
                                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                                        {{ __('Guardar resultado') }}
                                    </button>
                                </div>
                            </form>
                        </flux:modal>
                        
                        <script>
                            function matchResultForm() {
                                return {
                                    homeGoals: 0,
                                    awayGoals: 0
                                }
                            }
                        </script>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Formulario para crear partido --}}
    @if($tournamentTeams->count() >= 2)
        {{-- Errores de validación --}}
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">{{ __('Error al crear partido') }}</h4>
                <ul class="text-xs text-red-700 dark:text-red-300 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <details class="group">
            <summary class="cursor-pointer list-none">
                <div class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    <svg class="w-4 h-4 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Programar partido') }}
                </div>
            </summary>
            
            <form action="{{ route('tournaments.matches.store', $tournament) }}" method="POST" class="mt-4 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 space-y-4" id="create-match-form">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="home_team_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            {{ __('Equipo local') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="home_team_id" id="home_team_id" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('home_team_id') border-red-500 @enderror">
                            <option value="">{{ __('Seleccionar') }}</option>
                            @foreach($tournamentTeams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('home_team_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="away_team_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            {{ __('Equipo visitante') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="away_team_id" id="away_team_id" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('away_team_id') border-red-500 @enderror">
                            <option value="">{{ __('Seleccionar') }}</option>
                            @foreach($tournamentTeams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('away_team_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="match_field_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            {{ __('Cancha') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="field_id" id="match_field_id" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('field_id') border-red-500 @enderror">
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}" {{ $field->id == $tournament->field_id ? 'selected' : '' }}>{{ $field->name }}</option>
                            @endforeach
                        </select>
                        @error('field_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="round" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            {{ __('Jornada/Ronda') }}
                        </label>
                        <input type="text" name="round" id="round" placeholder="{{ __('Ej: Jornada 1, Semifinal, Final') }}" class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('round') border-red-500 @enderror" />
                        @error('round')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="match_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            {{ __('Fecha') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="match_date" id="match_date" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('match_date') border-red-500 @enderror" />
                        @error('match_date')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            {{ __('Hora') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select name="match_hour" id="match_hour" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('match_time') border-red-500 @enderror">
                                <option value="">HH</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <span class="flex items-center text-neutral-500">:</span>
                            <select name="match_minute" id="match_minute" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('match_time') border-red-500 @enderror">
                                <option value="">MM</option>
                                <option value="00">00</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                            </select>
                            <select name="match_period" id="match_period" required class="block w-24 rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 text-sm @error('match_time') border-red-500 @enderror">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                        @error('match_time')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <input type="hidden" name="match_time" id="match_time_hidden" />
                    </div>
                </div>
                
                <script>
                    // Convertir de 12h a 24h cuando se envía el formulario
                    document.getElementById('create-match-form').addEventListener('submit', function(e) {
                        const hour = document.getElementById('match_hour').value;
                        const minute = document.getElementById('match_minute').value;
                        const period = document.getElementById('match_period').value;
                        
                        if (hour && minute && period) {
                            let hour24 = parseInt(hour);
                            if (period === 'PM' && hour24 !== 12) {
                                hour24 += 12;
                            } else if (period === 'AM' && hour24 === 12) {
                                hour24 = 0;
                            }
                            
                            const timeString = String(hour24).padStart(2, '0') + ':' + minute;
                            document.getElementById('match_time_hidden').value = timeString;
                        }
                    });
                </script>
                
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                        {{ __('Crear partido') }}
                    </button>
                </div>
            </form>
        </details>
    @else
        <p class="text-sm text-neutral-500">{{ __('Agrega al menos 2 equipos para poder programar partidos.') }}</p>
    @endif
</div>
