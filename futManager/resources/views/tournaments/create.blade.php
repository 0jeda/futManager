<x-layouts.app :title="__('Crear torneo')">
    <div class="mx-auto w-full max-w-2xl space-y-6 py-6">
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Crear torneo') }}</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('Registra una nueva competición.') }}
            </p>
        </div>

        <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900">
            <form action="{{ route('tournaments.store') }}" method="POST" class="space-y-6">
                @csrf

                @include('tournaments.partials.form')

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('tournaments.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-300 dark:hover:text-neutral-100" wire:navigate>
                        {{ __('Cancelar') }}
                    </a>
                    <flux:button type="submit" icon="check">
                        {{ __('Crear torneo') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
