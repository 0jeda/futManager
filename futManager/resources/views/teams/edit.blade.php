<x-layouts.app :title="__('Editar equipo')">
    <div class="mx-auto w-full max-w-4xl space-y-6 py-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($team->logo_path)
                    <div class="overflow-hidden rounded-xl border-2 border-neutral-200 dark:border-neutral-700" style="width:80px; height:80px;">
                        <img src="{{ asset('storage/'.$team->logo_path) }}" alt="{{ $team->name }}" class="object-cover" style="width:100%; height:100%; object-fit:cover;" />
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $team->name }}</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ __('Actualiza los datos del club para mantener la información al día.') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('teams.index') }}" class="rounded-lg bg-neutral-100 dark:bg-neutral-800 px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700" wire:navigate>
                {{ __('← Volver a equipos') }}
            </a>
        </div>

        <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-lg dark:border-neutral-700 dark:bg-zinc-900">
            <form action="{{ route('teams.update', $team) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                @include('teams.partials.form', ['showPlayers' => false])

                <div class="flex items-center justify-end mt-6">
                    <flux:button type="submit" icon="check">
                        {{ __('Actualizar equipo') }}
                    </flux:button>
                </div>
            </form>
        </div>

        {{-- Sección de jugadores FUERA del formulario de equipo --}}
        @include('teams.partials.players', ['team' => $team])
    </div>
</x-layouts.app>
