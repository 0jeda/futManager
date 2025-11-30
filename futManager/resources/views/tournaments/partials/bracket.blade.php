@php
    /** @var \App\Models\Tournament $tournament */
    $tournament = $tournament ?? null;
    $rawBracket = $tournament?->bracket_data ?? [];

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

    $availableTeams = $teams->whereNotIn('id', 
        collect($bracket)->flatten()->filter(fn($v, $k) => in_array($k, ['team1_id', 'team2_id']))->filter()->values()
    );
@endphp

@if(!empty($bracket))
<div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Bracket de Eliminación') }}</h2>
            @if($tournament->winner_id)
                @php $champion = \App\Models\Team::find($tournament->winner_id); @endphp
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-sm text-amber-600 dark:text-amber-400 material-symbols-rounded align-middle text-base mr-1 leading-none">emoji_events</span>
                    <span class="text-sm text-amber-600 dark:text-amber-400">{{ __('Campeón:') }}</span>
                    @if($champion?->logo_path)
                        <img src="{{ asset('storage/'.$champion->logo_path) }}" alt="{{ $champion->name }}" class="w-6 h-6 rounded object-cover" />
                    @endif
                    <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $champion?->name }}</span>
                </div>
            @endif
        </div>
        <div class="flex items-center gap-2 text-xs text-neutral-600 dark:text-neutral-400">
            <span>⚽ Goles</span>
            <span>🟨 Amarilla</span>
            <span>🟥 Roja</span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <div class="inline-flex gap-8 min-w-full pb-4">
            @foreach($bracket as $roundName => $matches)
                <div class="flex flex-col justify-around min-w-[320px]">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4 text-center">
                        {{ $roundName }}
                    </h3>
                    
                    <div class="flex flex-col justify-around h-full space-y-8">
                        @foreach($matches as $match)
                            @php
                                $matchModel = isset($match['match_id']) && $match['match_id'] ? \App\Models\Game::with(['playerStats.player'])->find($match['match_id']) : null;
                                
                                if ($matchModel) {
                                    $homeStats = $matchModel->playerStats->where('team_id', $match['team1_id']);
                                    $awayStats = $matchModel->playerStats->where('team_id', $match['team2_id']);
                                } else {
                                    $homeStats = collect();
                                    $awayStats = collect();
                                }
                            @endphp
                            
                            <div class="relative">
                                <div class="rounded-lg border-2 border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    {{-- Team 1 --}}
                                    <div class="border-b border-neutral-200 dark:border-neutral-700 {{ $match['winner_id'] == $match['team1_id'] ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                        <div class="flex items-center justify-between p-3">
                                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                                @if($match['team1_id'])
                                                    @php $team1 = \App\Models\Team::find($match['team1_id']); @endphp
                                                    @if($team1?->logo_path)
                                                        <img src="{{ asset('storage/'.$team1->logo_path) }}" alt="{{ $match['team1_name'] }}" class="w-6 h-6 rounded object-cover flex-shrink-0" />
                                                    @endif
                                                @endif
                                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate">
                                                    {{ $match['team1_name'] }}
                                                </span>
                                            </div>
                                            @if($match['score1'] !== null)
                                                <span class="ml-2 text-sm font-bold text-neutral-900 dark:text-neutral-100">{{ $match['score1'] }}</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Estadísticas del equipo 1 --}}
                                        @if($homeStats->count() > 0)
                                            <div class="px-3 pb-2 space-y-1 border-t border-neutral-200 dark:border-neutral-700 pt-2">
                                                @foreach($homeStats as $stat)
                                                    <div class="flex items-center gap-1.5 text-xs text-neutral-700 dark:text-neutral-300">
                                                        @if($stat->player->photo_path)
                                                            <img src="{{ asset('storage/'.$stat->player->photo_path) }}" alt="{{ $stat->player->first_name }}" class="w-4 h-4 rounded-full object-cover flex-shrink-0" />
                                                        @endif
                                                        <span class="truncate font-medium">{{ $stat->player->first_name }} {{ substr($stat->player->last_name, 0, 1) }}.</span>
                                                        <div class="flex items-center gap-1 ml-auto">
                                                            @if($stat->goals > 0)
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-semibold">⚽{{ $stat->goals }}</span>
                                                            @endif
                                                            @if($stat->assists > 0)
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold">🎯{{ $stat->assists }}</span>
                                                            @endif
                                                            @if($stat->yellow_cards > 0)
                                                                <span class="inline-flex items-center px-1 py-0.5 rounded bg-yellow-100 dark:bg-yellow-900/30">🟨</span>
                                                            @endif
                                                            @if($stat->red_cards > 0)
                                                                <span class="inline-flex items-center px-1 py-0.5 rounded bg-red-100 dark:bg-red-900/30">🟥</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Team 2 --}}
                                    <div class="{{ $match['winner_id'] == $match['team2_id'] ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                        <div class="flex items-center justify-between p-3">
                                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                                @if($match['team2_id'])
                                                    @php $team2 = \App\Models\Team::find($match['team2_id']); @endphp
                                                    @if($team2?->logo_path)
                                                        <img src="{{ asset('storage/'.$team2->logo_path) }}" alt="{{ $match['team2_name'] }}" class="w-6 h-6 rounded object-cover flex-shrink-0" />
                                                    @endif
                                                @endif
                                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate">
                                                    {{ $match['team2_name'] }}
                                                </span>
                                            </div>
                                            @if($match['score2'] !== null)
                                                <span class="ml-2 text-sm font-bold text-neutral-900 dark:text-neutral-100">{{ $match['score2'] }}</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Estadísticas del equipo 2 --}}
                                        @if($awayStats->count() > 0)
                                            <div class="px-3 pb-2 space-y-1 border-t border-neutral-200 dark:border-neutral-700 pt-2">
                                                @foreach($awayStats as $stat)
                                                    <div class="flex items-center gap-1.5 text-xs text-neutral-700 dark:text-neutral-300">
                                                        @if($stat->player->photo_path)
                                                            <img src="{{ asset('storage/'.$stat->player->photo_path) }}" alt="{{ $stat->player->first_name }}" class="w-4 h-4 rounded-full object-cover flex-shrink-0" />
                                                        @endif
                                                        <span class="truncate font-medium">{{ $stat->player->first_name }} {{ substr($stat->player->last_name, 0, 1) }}.</span>
                                                        <div class="flex items-center gap-1 ml-auto">
                                                            @if($stat->goals > 0)
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-semibold">⚽{{ $stat->goals }}</span>
                                                            @endif
                                                            @if($stat->assists > 0)
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold">🎯{{ $stat->assists }}</span>
                                                            @endif
                                                            @if($stat->yellow_cards > 0)
                                                                <span class="inline-flex items-center px-1 py-0.5 rounded bg-yellow-100 dark:bg-yellow-900/30">🟨</span>
                                                            @endif
                                                            @if($stat->red_cards > 0)
                                                                <span class="inline-flex items-center px-1 py-0.5 rounded bg-red-100 dark:bg-red-900/30">🟥</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Indicador de estado --}}
                                    @if($matchModel)
                                        <div class="px-3 py-1 text-center text-xs {{ $matchModel->status == 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                            {{ $matchModel->status == 'completed' ? '✓ Finalizado' : '◷ Programado' }}
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Connector line to next round (except for final) --}}
                                @if(!$loop->parent->last)
                                    <div class="absolute top-1/2 -right-8 w-8 h-0.5 bg-neutral-300 dark:bg-neutral-600"></div>
                                    @if($loop->iteration % 2 == 1 && !$loop->last)
                                        <div class="absolute top-1/2 -right-8 w-0.5 bg-neutral-300 dark:bg-neutral-600" style="height: calc(100% + 2rem + {{ $loop->iteration == 1 ? '0' : '0' }}px);"></div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    @if($tournament->bracket_data)
        <div class="mt-6 pt-4 border-t border-neutral-200 dark:border-neutral-700">
            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                {{ __('Los equipos avanzan automáticamente cuando se registran resultados de los partidos.') }}
            </p>
        </div>
    @endif
</div>
@endif
