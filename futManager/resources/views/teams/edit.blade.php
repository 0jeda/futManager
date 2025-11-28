<x-layouts.app :title="__('Editar equipo')">
    <div class="mx-auto w-full max-w-4xl space-y-6 py-6">
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $team->name }}</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('Actualiza los datos del club para mantener la información al día.') }}
            </p>
        </div>

        <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900">
            <form action="{{ route('teams.update', $team) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                @include('teams.partials.form')

                <div class="flex items-center justify-between">
                    <a href="{{ route('teams.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-300 dark:hover:text-neutral-100" wire:navigate>
                        {{ __('Volver al listado') }}
                    </a>
                    <flux:button type="submit" icon="check">
                        {{ __('Actualizar equipo') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
