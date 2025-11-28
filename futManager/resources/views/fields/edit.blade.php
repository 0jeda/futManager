<x-layouts.app :title="__('Editar cancha')">
    <div class="mx-auto w-full max-w-3xl space-y-6 py-6">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $field->name }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Actualiza la información para mantener tus datos organizados.') }}
            </p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
            <form action="{{ route('fields.update', $field) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                @include('fields.partials.form')

                <div class="flex items-center justify-between">
                    <a href="{{ route('fields.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-800 dark:text-zinc-300 dark:hover:text-zinc-50" wire:navigate>
                        {{ __('Volver al listado') }}
                    </a>
                    <div class="flex items-center gap-3">
                        <flux:button type="submit" icon="check">
                            {{ __('Guardar cambios') }}
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
