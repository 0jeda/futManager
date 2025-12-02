<x-layouts.app :title="__('Dashboard')">
    @php
        $matchesByDate = $matchesByDate ?? collect();
        $calendarEvents = $calendarEvents ?? collect();
        $today = now();
        $monthStart = $currentMonth ?? $today->copy()->startOfMonth();
        $daysInMonth = $monthStart->daysInMonth;
        $startOffset = $monthStart->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        $calendarEventsJson = $calendarEvents->toArray();
        $firstDateWithEvents = $matchesByDate->keys()->first() ?? $today->toDateString();
        $prevMonth = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthStart->copy()->addMonth()->format('Y-m');
    @endphp

    <div class="space-y-6">
        @if(auth()->user()->role !== 'player')
            {{-- Stats --}}
            <div class="grid gap-4 md:grid-cols-4">
                <div
                    class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div
                        class="flex items-center justify-between text-xs uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        <span>Torneos activos</span>
                        <span class="material-symbols-rounded text-amber-500 text-4xl leading-none">flag</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span
                            class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $activeTournaments }}</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">en curso</span>
                    </div>
                </div>
                <div
                    class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div
                        class="flex items-center justify-between text-xs uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        <span>Equipos</span>
                        <span class="material-symbols-rounded text-emerald-500 text-4xl leading-none">groups</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $teamsCount }}</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">registrados</span>
                    </div>
                </div>
                <div
                    class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div
                        class="flex items-center justify-between text-xs uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        <span>Jugadores</span>
                        <span class="material-symbols-rounded text-rose-500 text-4xl leading-none">sports_soccer</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $playersCount }}</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">registrados</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-6">
            {{-- Calendario interactivo --}}
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900"
                x-data="calendarComponent(@js($calendarEventsJson), '{{ $firstDateWithEvents }}')">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Calendario</p>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Próximos partidos</h2>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">
                            {{ $monthStart->translatedFormat('F Y') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard', ['month' => $prevMonth]) }}"
                            class="rounded-lg border border-neutral-200 bg-neutral-50 px-2 py-1 text-neutral-600 hover:border-neutral-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:border-neutral-600">←</a>
                        <span class="material-symbols-rounded text-indigo-500 text-3xl">event_upcoming</span>
                        <a href="{{ route('dashboard', ['month' => $nextMonth]) }}"
                            class="rounded-lg border border-neutral-200 bg-neutral-50 px-2 py-1 text-neutral-600 hover:border-neutral-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:border-neutral-600">→</a>
                    </div>
                </div>

                <div class="grid grid-cols-7 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400">
                    <span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span>D</span>
                </div>
                <div class="mt-2 grid grid-cols-7 gap-1 text-sm">
                    @for($i = 1; $i < $startOffset; $i++)
                        <div></div>
                    @endfor
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateObj = $monthStart->copy()->day($day);
                            $dateKey = $dateObj->toDateString();
                            $count = optional($matchesByDate->get($dateKey))->count() ?? 0;
                        @endphp
                        <button type="button"
                            class="group flex flex-col items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 px-2 py-2 text-neutral-800 transition hover:border-indigo-400 hover:bg-indigo-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:border-indigo-500 dark:hover:bg-neutral-800/70"
                            :class="{ 'ring-2 ring-indigo-500': selectedDate === '{{ $dateKey }}' }"
                            @click="openDay('{{ $dateKey }}')">
                            <span class="text-base font-semibold">{{ $day }}</span>
                            @if($count > 0)
                                <span
                                    class="mt-1 inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">
                                    <span class="material-symbols-rounded text-[14px]">sports_soccer</span>
                                    {{ $count }}
                                </span>
                            @else
                                <span class="mt-1 text-[11px] text-neutral-400 dark:text-neutral-500">—</span>
                            @endif
                        </button>
                    @endfor
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100"
                            x-text="selectedDateLabel"></h3>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400"
                            x-text="selectedMatches.length + ' partidos'"></span>
                    </div>
                    <div class="space-y-2" x-show="selectedMatches.length > 0">
                        <template x-for="match in selectedMatches"
                            :key="match.date + match.time + match.home + match.away">
                            <div
                                class="flex items-center justify-between rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm dark:border-neutral-700 dark:bg-neutral-800/60">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400">
                                        <span
                                            class="material-symbols-rounded text-[14px] text-amber-500">emoji_events</span>
                                        <span x-text="match.tournament ?? 'Torneo'"></span>
                                    </div>
                                    <div class="font-semibold text-neutral-900 dark:text-neutral-100 truncate"
                                        x-text="match.home"></div>
                                    <div class="text-[11px] text-neutral-500 dark:text-neutral-400">vs</div>
                                    <div class="font-semibold text-neutral-900 dark:text-neutral-100 truncate"
                                        x-text="match.away"></div>
                                </div>
                                <div class="text-right text-xs text-neutral-600 dark:text-neutral-400 space-y-0.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <span
                                            class="material-symbols-rounded text-[15px] text-indigo-500">schedule</span>
                                        <span class="font-semibold text-neutral-900 dark:text-neutral-100"
                                            x-text="match.time"></span>
                                    </div>
                                    <div class="flex items-center justify-end gap-1">
                                        <span
                                            class="material-symbols-rounded text-[15px] text-emerald-500">stadium</span>
                                        <span class="truncate" x-text="match.field ?? '-'"></span>
                                    </div>
                                    <template x-if="match.round">
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400"
                                            x-text="match.round"></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="selectedMatches.length === 0"
                        class="rounded-xl border border-dashed border-neutral-200 bg-neutral-50 px-3 py-2 text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-800/60 dark:text-neutral-400">
                        Selecciona un día con partidos programados.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calendarComponent(events, defaultDate) {
            const formatter = new Intl.DateTimeFormat('es-MX', { weekday: 'long', day: 'numeric', month: 'long' });
            const today = new Date();
            const todayKey = today.toISOString().slice(0, 10);
            const initialDate = defaultDate || todayKey;
            return {
                events,
                selectedDate: initialDate,
                selectedMatches: events[initialDate] || [],
                get selectedDateLabel() {
                    const date = this.selectedDate ? new Date(this.selectedDate + 'T00:00:00') : today;
                    return formatter.format(date);
                },
                openDay(date) {
                    this.selectedDate = date;
                    this.selectedMatches = this.events[date] || [];
                }
            };
        }
    </script>
</x-layouts.app>