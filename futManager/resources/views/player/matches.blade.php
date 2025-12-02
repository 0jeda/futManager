<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Player & Team Info -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Player Info -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Mi Perfil</h2>
                    <div class="mt-4 flex items-center gap-4">
                        @if($player->photo_path)
                            <img src="{{ asset('storage/' . $player->photo_path) }}" alt="{{ $player->first_name }}"
                                class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                                <span
                                    class="text-xl font-medium text-neutral-500">{{ substr($player->first_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                                {{ $player->first_name }} {{ $player->last_name }}</h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">Posición: {{ $player->position }}
                            </p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">Dorsal: #{{ $player->number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Info -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Mi Equipo</h2>
                    <div class="mt-4 flex items-center gap-4">
                        @if($team->logo_path)
                            <img src="{{ asset('storage/' . $team->logo_path) }}" alt="{{ $team->name }}"
                                class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                                <span class="text-xl font-medium text-neutral-500">{{ substr($team->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">{{ $team->name }}
                            </h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">Entrenador:
                                {{ $team->coach_name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-1">
            <!-- Upcoming Matches -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Próximos Partidos</h2>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        Encuentros programados.
                    </p>

                    <div class="mt-6 flow-root">
                        <ul role="list" class="-my-5 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($upcomingMatches as $match)
                                <li class="py-5">
                                    <div class="relative focus-within:ring-2 focus-within:ring-indigo-500">
                                        <h3 class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                                            <span class="absolute inset-0" aria-hidden="true"></span>
                                            Contra:
                                            @foreach($match->participants as $participant)
                                                @if($participant->team_id !== $team->id)
                                                    {{ $participant->team->name }}
                                                @endif
                                            @endforeach
                                        </h3>
                                        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2">
                                            Fecha: {{ $match->scheduled_at->format('d/m/Y H:i') }} |
                                            Cancha: {{ $match->field->name }} |
                                            Torneo: {{ $match->tournament->name }}
                                        </p>
                                        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-500">
                                            Estado: {{ ucfirst($match->status) }}
                                        </p>
                                    </div>
                                </li>
                            @empty
                                <li class="py-5">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">No tienes partidos
                                        programados.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Match History -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Historial de Partidos</h2>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        Resultados anteriores.
                    </p>

                    <div class="mt-6 flow-root">
                        <ul role="list" class="-my-5 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($pastMatches as $match)
                                <li class="py-5">
                                    <div class="relative">
                                        <h3 class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                                            Contra:
                                            @foreach($match->participants as $participant)
                                                @if($participant->team_id !== $team->id)
                                                    {{ $participant->team->name }}
                                                @endif
                                            @endforeach
                                        </h3>
                                        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                            Fecha: {{ $match->scheduled_at->format('d/m/Y') }} |
                                            Resultado:
                                            @php
                                                $myTeam = $match->participants->where('team_id', $team->id)->first();
                                                $opponent = $match->participants->where('team_id', '!=', $team->id)->first();
                                            @endphp
                                            {{ $myTeam->goals ?? 0 }} - {{ $opponent->goals ?? 0 }}
                                        </p>
                                    </div>
                                </li>
                            @empty
                                <li class="py-5">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">No hay partidos jugados.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>