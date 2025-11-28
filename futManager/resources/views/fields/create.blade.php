<x-layouts.app :title="__('Nueva cancha')">
    <div class="mx-auto w-full max-w-3xl space-y-6 py-6">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Registrar cancha') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Define los datos de la cancha para asociarla con torneos y partidos.') }}
            </p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
            <form action="{{ route('fields.store') }}" method="POST" class="space-y-6">
                @csrf

                @include('fields.partials.form')

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('fields.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-800 dark:text-zinc-300 dark:hover:text-zinc-50" wire:navigate>
                        {{ __('Cancelar') }}
                    </a>
                    <flux:button type="submit" icon="check">
                        {{ __('Guardar cancha') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
