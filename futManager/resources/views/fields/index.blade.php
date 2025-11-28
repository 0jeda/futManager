<x-layouts.app :title="__('Canchas')">
    <div class="mx-auto w-full max-w-6xl space-y-6 py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Canchas') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Gestiona las canchas disponibles dentro del complejo.') }}
                </p>
            </div>

            <flux:button icon="plus" href="{{ route('fields.create') }}" wire:navigate>
                {{ __('Nueva cancha') }}
            </flux:button>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        @if ($fields->count())
            <div class="grid gap-6 lg:grid-cols-2">
                @foreach ($fields as $field)
                    <section class="rounded-3xl border border-neutral-200 bg-white/90 p-6 shadow-lg shadow-neutral-200/40 backdrop-blur-sm transition hover:-translate-y-1 hover:shadow-xl dark:border-zinc-700 dark:bg-zinc-900/80 dark:shadow-black/40">
                        <header class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">{{ __('Cancha') }}</p>
                                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-white">
                                    {{ $field->name }}
                                </h2>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                    {{ $field->surface ?? __('Superficie sin definir') }}
                                </span>
                                @if ($field->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-100">
                                        <span class="material-symbols-rounded text-base">check_circle</span>
                                        {{ __('Activa') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-900/40 dark:text-rose-100">
                                        <span class="material-symbols-rounded text-base">error</span>
                                        {{ __('Inactiva') }}
                                    </span>
                                @endif
                            </div>
                        </header>

                        <div class="mt-6 space-y-4 text-sm text-neutral-600 dark:text-neutral-300">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-rounded text-xl text-rose-500">location_on</span>
                                <div>
                                    <p class="font-semibold text-neutral-900 dark:text-neutral-100">
                                        {{ $field->location ?? __('Ubicación pendiente') }}
                                    </p>
                                    <p class="text-xs text-neutral-500">{{ __('Dirección principal donde se juega el torneo.') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="material-symbols-rounded text-xl text-indigo-500">person</span>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Dueño') }}</p>
                                    <p class="font-semibold text-neutral-900 dark:text-neutral-100">
                                        {{ $field->owner->name ?? __('Sin asignar') }}
                                    </p>
                                    <p class="text-xs text-neutral-500">
                                        {{ $field->owner?->contact_phone ?? $field->owner?->contact_email ?? __('Sin contacto') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="material-symbols-rounded text-xl text-amber-500">event_note</span>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Torneos programados') }}</p>
                                    <p class="font-semibold text-neutral-900 dark:text-neutral-100">
                                        {{ trans_choice('{0}Sin torneos activos|{1}:count torneo activo|[2,*]:count torneos activos', $field->tournaments_count, ['count' => $field->tournaments_count]) }}
                                    </p>
                                    <p class="text-xs text-neutral-500">{{ __('Planifica los próximos encuentros en esta cancha.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-3 border-t border-neutral-100 pt-4 dark:border-neutral-800 sm:grid-cols-2">
                            <a
                                href="{{ route('fields.edit', $field) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-neutral-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-neutral-700 dark:bg-white dark:text-neutral-900"
                                wire:navigate
                            >
                                <span class="material-symbols-rounded text-base">edit</span>
                                {{ __('Editar') }}
                            </a>
                            <form method="POST" action="{{ route('fields.destroy', $field) }}" onsubmit="return confirm('{{ __('¿Eliminar esta cancha?') }}')" class="flex">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:border-rose-400 hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-200"
                                >
                                    <span class="material-symbols-rounded text-base">delete</span>
                                    {{ __('Eliminar') }}
                                </button>
                            </form>
                        </div>
                    </section>
                @endforeach
            </div>

            @if ($fields->hasPages())
                <div>
                    {{ $fields->links() }}
                </div>
            @endif
        @else
            <div class="rounded-xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center text-neutral-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-300">
                <p class="mb-3 text-base font-semibold">{{ __('Todavía no hay canchas registradas.') }}</p>
                <p class="mb-6 text-sm">{{ __('Agrega la primera cancha para comenzar a planear torneos.') }}</p>
                <flux:button icon="plus" href="{{ route('fields.create') }}" wire:navigate>
                    {{ __('Registrar cancha') }}
                </flux:button>
            </div>
        @endif
    </div>
</x-layouts.app>
