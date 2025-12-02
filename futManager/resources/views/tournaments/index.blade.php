<x-layouts.app :title="__('Torneos')">
    <div class="mx-auto w-full max-w-6xl space-y-6 py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Torneos') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Administra las competiciones, equipos participantes y calendarios de partidos.') }}
                </p>
            </div>

            <flux:button icon="plus" href="{{ route('tournaments.create') }}" wire:navigate>
                {{ __('Nuevo torneo') }}
            </flux:button>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        @if ($tournaments->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($tournaments as $tournament)
                    <article class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-zinc-900 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white break-words">{{ $tournament->name }}</h2>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $tournament->category ?? __('Sin categoría') }}</p>
                                </div>
                            </div>
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
                                    'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                ];
                                $statusLabels = [
                                    'draft' => 'Borrador',
                                    'active' => 'Activo',
                                    'completed' => 'Finalizado',
                                    'cancelled' => 'Cancelado',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusClasses[$tournament->status] ?? $statusClasses['draft'] }}">
                                {{ $statusLabels[$tournament->status] ?? $tournament->status }}
                            </span>
                        </div>

                        <div class="space-y-2 text-sm flex-1">
                            <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="truncate">{{ $tournament->field->name ?? __('Sin cancha asignada') }}</span>
                            </div>
                            
                            <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $tournament->start_date->format('d/m/Y') }} - {{ $tournament->end_date?->format('d/m/Y') ?? __('Sin fecha fin') }}</span>
                            </div>

                            @if($tournament->format)
                                <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="truncate">{{ $tournament->format }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-neutral-200 dark:border-neutral-700 flex gap-2">
                            <a href="{{ route('tournaments.edit', $tournament) }}" class="flex-1 rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-medium text-white hover:bg-indigo-500" wire:navigate>
                                {{ __('Gestionar') }}
                            </a>
                            <a href="{{ route('tournaments.pdf', $tournament) }}" class="flex-shrink-0 rounded-md bg-neutral-100 px-3 py-2 text-center text-sm font-medium text-neutral-700 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700" title="Descargar Calendario" target="_blank">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </a>
                            <form action="{{ route('tournaments.destroy', $tournament) }}" method="POST" onsubmit="return confirm('¿Eliminar torneo?');" class="flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-rose-500 px-3 py-2 text-sm font-medium text-white hover:bg-rose-600">
                                    {{ __('Eliminar') }}
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $tournaments->links() }}
            </div>
        @else
            <div class="rounded-2xl border-2 border-dashed border-neutral-200 bg-neutral-50/50 px-6 py-12 text-center dark:border-neutral-700 dark:bg-neutral-800/50">
                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Sin torneos') }}</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __('Comienza creando tu primera competición.') }}
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>
