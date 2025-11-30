@php
    $stats = $matchModel?->playerStats->where('team_id', $teamId) ?? collect();
    $otherTeamId = $teamSlot === 'team1' ? ($match['team2_id'] ?? null) : ($match['team1_id'] ?? null);
    // Evitar que un equipo pueda seleccionarse contra sí mismo (UI)
    $availableTeams = $teams->reject(fn($t) => $t->id === $otherTeamId);
@endphp

<div class="p-3 {{ $isWinner ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 flex-1 min-w-0">
            @if($teamId && $teamId !== 'TBD')
                @php $team = \App\Models\Team::find($teamId); @endphp
                @if($team?->logo_path)
                    <img src="{{ asset('storage/'.$team->logo_path) }}" alt="{{ $teamName }}" class="w-7 h-7 rounded object-cover flex-shrink-0" />
                @endif
                <span class="text-sm font-semibold text-neutral-900 dark:text-neutral-100 truncate">
                    {{ $teamName }}
                </span>
            @else
                {{-- TBD - Click to assign team --}}
                @if($canEdit && ($isFirstRound ?? false))
                    <flux:modal.trigger name="assign-{{ $roundName }}-{{ $position }}-{{ $teamSlot }}">
                        <button type="button" class="flex-1 text-left px-2 py-1 rounded border-2 border-dashed border-indigo-300 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                            <span class="text-sm font-medium">{{ __('+ Asignar Equipo') }}</span>
                        </button>
                    </flux:modal.trigger>
                @else
                    <span class="text-sm text-neutral-400 dark:text-neutral-600 italic">
                        @if($isFirstRound)
                            TBD
                        @else
                            Esperando resultado...
                        @endif
                    </span>
                @endif
            @endif
        </div>
        
        @if($score !== null)
            <span class="text-lg font-bold text-neutral-900 dark:text-neutral-100 ml-auto">{{ $score }}</span>
        @endif
    </div>
    
    {{-- Player Stats --}}
    @if($stats->count() > 0)
        <div class="mt-2 pt-2 border-t border-neutral-200 dark:border-neutral-700 space-y-1">
            @foreach($stats as $stat)
                <div class="flex items-center gap-1.5 text-xs text-neutral-700 dark:text-neutral-300">
                    @if($stat->player->photo_path)
                        <img src="{{ asset('storage/'.$stat->player->photo_path) }}" alt="{{ $stat->player->first_name }}" class="w-4 h-4 rounded-full object-cover flex-shrink-0" />
                    @endif
                    <span class="truncate font-medium flex-1">{{ $stat->player->first_name }} {{ substr($stat->player->last_name, 0, 1) }}.</span>
                    <div class="flex items-center gap-1">
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

{{-- Modal for Team Assignment - SOLO EN PRIMERA RONDA --}}
@if($canEdit && (!$teamId || $teamId === 'TBD') && ($isFirstRound ?? false))
    <flux:modal name="assign-{{ $roundName }}-{{ $position }}-{{ $teamSlot }}" class="max-w-md">
        <form action="{{ route('tournaments.bracket.assign', $tournament) }}" method="POST">
            @csrf
            <input type="hidden" name="round" value="{{ $roundName }}" />
            <input type="hidden" name="position" value="{{ $position }}" />
            <input type="hidden" name="slot" value="{{ $teamSlot }}" />
            
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                {{ __('Asignar Equipo a') }} {{ $roundName }} - {{ __('Partido') }} {{ $position + 1 }}
            </h3>
            
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                {{ __('Seleccionar equipo') }}
            </label>
            <select name="team_id" required class="block w-full rounded-md border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100">
                <option value="">{{ __('-- Seleccionar --') }}</option>
                @foreach($availableTeams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
            
            <div class="flex justify-end gap-2 mt-6">
                <flux:modal.close>
                    <button type="button" class="rounded-md bg-neutral-200 dark:bg-neutral-700 px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-300 dark:hover:bg-neutral-600">
                        {{ __('Cancelar') }}
                    </button>
                </flux:modal.close>
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    {{ __('Asignar Equipo') }}
                </button>
            </div>
        </form>
    </flux:modal>
@endif
