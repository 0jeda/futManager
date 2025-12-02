@php
    use Illuminate\Support\Str;
@endphp

<x-layouts.app :title="__('Equipos')">
    <div class="mx-auto w-full max-w-6xl space-y-6 py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Equipos') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Administra los clubes registrados, sus contactos y escudos.') }}
                </p>
            </div>

            <flux:button icon="plus" href="{{ route('teams.create') }}" wire:navigate>
                {{ __('Nuevo equipo') }}
            </flux:button>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        @if ($teams->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($teams as $team)
                    <article class="rounded-2xl border border-neutral-200 bg-white/95 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-zinc-900/80 dark:shadow-black/20 overflow-hidden flex flex-col h-full">
                        <header class="flex items-start gap-3 min-h-[120px]">
                            <div class="relative flex-shrink-0 w-20 h-20 overflow-hidden rounded-lg border border-neutral-200 shadow-sm dark:border-neutral-700">
                                @if ($team->logo_path)
                                    <img
                                        src="{{ asset('storage/'.$team->logo_path) }}"
                                        alt="{{ $team->name }}"
                                        class="block h-full w-full object-cover"
                                    />
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-500 via-purple-500 to-sky-500 text-lg font-semibold uppercase text-white">
                                        {{ Str::substr($team->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 space-y-0.5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">{{ __('Equipo') }}</p>
                                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white break-words leading-tight">{{ $team->name }}</h2>
                                <p class="text-xs text-neutral-500 dark:text-neutral-300 truncate leading-tight" title="{{ $team->short_name }}">
                                    {{ $team->short_name ? __('Abreviatura: :short', ['short' => $team->short_name]) : __('Sin abreviatura definida') }}
                                </p>
                                @if ($team->coach_name)
                                    <p class="text-xs text-neutral-500 dark:text-neutral-300 leading-tight">{{ __('DT: :name', ['name' => $team->coach_name]) }}</p>
                                @else
                                    <p class="text-xs text-neutral-500 dark:text-neutral-300 opacity-0 select-none leading-tight">DT: -</p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if ($team->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-100">
                                        <span class="material-symbols-rounded text-base">check_circle</span>
                                        {{ __('Activo') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-900/40 dark:text-rose-100">
                                        <span class="material-symbols-rounded text-base">close</span>
                                        {{ __('Inactivo') }}
                                    </span>
                                @endif
                            </div>
                        </header>

                        <div class="mt-4 grid gap-3 text-sm text-neutral-600 dark:text-neutral-300 flex-grow min-h-[120px]">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-rounded text-xl text-indigo-500">person</span>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Dueño') }}</p>
                                    <p class="font-semibold text-neutral-900 dark:text-neutral-100 break-words">
                                        {{ $team->owner_name ?? __('No asignado') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="material-symbols-rounded text-xl text-sky-500">mail</span>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Contacto') }}</p>
                                    <p class="break-words">{{ $team->contact_email ?? __('Sin correo') }}</p>
                                    <p class="break-words">{{ $team->contact_phone ?? __('Sin teléfono') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 border-t border-neutral-100 pt-3 dark:border-neutral-800 grid-cols-3">
                            <a
                                href="{{ route('teams.edit', $team) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-neutral-900 px-3 py-1.5 text-sm font-semibold text-white shadow hover:bg-neutral-700 dark:bg-white dark:text-neutral-900"
                                wire:navigate
                            >
                                <span class="material-symbols-rounded text-base">edit</span>
                            </a>
                            <a
                                href="{{ route('teams.pdf', $team) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow hover:bg-indigo-500"
                                target="_blank"
                                title="Descargar Reporte"
                            >
                                <span class="material-symbols-rounded text-base">download</span>
                            </a>
                            <form method="POST" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('{{ __('¿Eliminar este equipo?') }}')" class="flex">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-1.5 text-sm font-semibold text-rose-600 transition hover:border-rose-400 hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-200"
                                >
                                    <span class="material-symbols-rounded text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($teams->hasPages())
                <div>
                    {{ $teams->links() }}
                </div>
            @endif
        @else
            <div class="rounded-xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center text-neutral-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-300">
                <p class="mb-3 text-base font-semibold">{{ __('Aún no hay equipos registrados.') }}</p>
                <p class="mb-6 text-sm">{{ __('Registra tu primer equipo para comenzar a asignar jugadores.') }}</p>
                <flux:button icon="plus" href="{{ route('teams.create') }}" wire:navigate>
                    {{ __('Registrar equipo') }}
                </flux:button>
            </div>
        @endif
    </div>
</x-layouts.app>
