@php
    $homeTeamPlayers = $homeTeam?->players ?? collect();
    $awayTeamPlayers = $awayTeam?->players ?? collect();
@endphp

<flux:modal name="{{ $modalId }}" class="max-w-4xl">
    <form action="{{ route('matches.result.update', $match) }}" method="POST" class="space-y-6" x-data="matchResultForm()">
        @csrf
        @method('PUT')
        
        <div>
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Registrar resultado del partido') }}</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $match->scheduled_at->format('d/m/Y H:i') }}</p>
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
        <details class="group">
            <summary class="cursor-pointer list-none">
                <div class="flex items-center gap-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span>{{ __('Estadísticas detalladas de jugadores (opcional)') }}</span>
                </div>
            </summary>
            
            <div class="mt-4 space-y-4">
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
                                        <span class="text-neutral-900 dark:text-neutral-100 truncate">
                                            @if($player->number)<span class="font-bold">#{{ $player->number }}</span> @endif
                                            {{ $player->first_name }} {{ $player->last_name }}
                                        </span>
                                    </div>
                                    
                                    <template x-if="show">
                                        <div class="col-span-8 grid grid-cols-4 gap-2">
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">⚽</label>
                                                <input type="number" :name="'player_stats[home_{{ $index }}][goals]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                <input type="hidden" :name="'player_stats[home_{{ $index }}][player_id]'" value="{{ $player->id }}" />
                                                <input type="hidden" :name="'player_stats[home_{{ $index }}][team_id]'" value="{{ $homeTeam->id }}" />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">🎯</label>
                                                <input type="number" :name="'player_stats[home_{{ $index }}][assists]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">🟨</label>
                                                <input type="number" :name="'player_stats[home_{{ $index }}][yellow_cards]'" min="0" max="2" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">🟥</label>
                                                <input type="number" :name="'player_stats[home_{{ $index }}][red_cards]'" min="0" max="1" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <div class="col-span-8 text-right" x-show="!show">
                                        <button type="button" @click="show = true" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                            + Agregar estadísticas
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
                                        <span class="text-neutral-900 dark:text-neutral-100 truncate">
                                            @if($player->number)<span class="font-bold">#{{ $player->number }}</span> @endif
                                            {{ $player->first_name }} {{ $player->last_name }}
                                        </span>
                                    </div>
                                    
                                    <template x-if="show">
                                        <div class="col-span-8 grid grid-cols-4 gap-2">
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">⚽</label>
                                                <input type="number" :name="'player_stats[away_{{ $index }}][goals]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                                <input type="hidden" :name="'player_stats[away_{{ $index }}][player_id]'" value="{{ $player->id }}" />
                                                <input type="hidden" :name="'player_stats[away_{{ $index }}][team_id]'" value="{{ $awayTeam->id }}" />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">🎯</label>
                                                <input type="number" :name="'player_stats[away_{{ $index }}][assists]'" min="0" max="20" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">🟨</label>
                                                <input type="number" :name="'player_stats[away_{{ $index }}][yellow_cards]'" min="0" max="2" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-neutral-600 dark:text-neutral-400 mb-1">🟥</label>
                                                <input type="number" :name="'player_stats[away_{{ $index }}][red_cards]'" min="0" max="1" value="0" class="block w-full text-sm rounded border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 px-2 py-1" />
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <div class="col-span-8 text-right" x-show="!show">
                                        <button type="button" @click="show = true" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                            + Agregar estadísticas
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </details>
        
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
