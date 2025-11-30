<x-layouts.app :title="__('Gestionar torneo')">
    <div class="mx-auto w-full max-w-6xl space-y-6 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $tournament->name }}</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __('Gestiona los detalles, equipos y calendario del torneo.') }}
                </p>
            </div>
            <a href="{{ route('tournaments.index') }}" class="rounded-lg bg-neutral-100 dark:bg-neutral-800 px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700" wire:navigate>
                {{ __('← Volver a torneos') }}
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        {{-- Información básica del torneo --}}
        <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ __('Información del torneo') }}</h2>
            
            <form action="{{ route('tournaments.update', $tournament) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                @include('tournaments.partials.form')

                <div class="flex items-center justify-end mt-6">
                    <flux:button type="submit" icon="check">
                        {{ __('Actualizar torneo') }}
                    </flux:button>
                </div>
            </form>
        </div>

        {{-- Gestión de equipos y partidos --}}
        @if($tournament->is_bracket && $tournament->bracket_data)
            {{-- Mostrar bracket interactivo --}}
            @include('tournaments.partials.bracket-interactive')
        @else
            {{-- Gestión normal de equipos y partidos --}}
            @include('tournaments.partials.teams-and-matches')
        @endif
    </div>
</x-layouts.app>
