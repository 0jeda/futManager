@php
    /** @var \App\Models\Team $team */
    $team = $team ?? null;
    $players = $team?->players ?? collect();
@endphp

<div class="mt-6">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ __('Jugadores') }}</h3>
    </div>

    <div class="mt-3 space-y-4">
        @if($players->isEmpty())
            <p class="text-sm text-neutral-500">{{ __('No hay jugadores aún. Agrega al primero.') }}</p>
        @else
            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($players as $player)
                    <div class="rounded-xl border border-neutral-200 p-3 bg-white dark:bg-neutral-800">
                        <div class="flex items-center gap-3">
                            <div style="width:56px; height:56px; overflow:hidden; border-radius:8px;">
                                @if($player->photo_path)
                                    <img src="{{ asset('storage/'.$player->photo_path) }}" alt="{{ $player->first_name }}" class="object-cover" style="width:100%; height:100%; object-fit:cover; display:block;" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-neutral-100 text-sm text-neutral-500">{{ strtoupper(substr($player->first_name,0,1) ?: 'P') }}</div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-neutral-800 dark:text-neutral-100">{{ $player->first_name }} {{ $player->last_name }}</div>
                                <div class="text-xs text-neutral-500">{{ $player->position }} @if($player->number) · #{{ $player->number }} @endif</div>
                                @if($player->curp)
                                    <div class="text-xs text-neutral-400 mt-1">CURP: {{ $player->curp }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <form action="{{ route('players.update', $player) }}" method="POST" enctype="multipart/form-data" class="flex-1">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="first_name" value="{{ $player->first_name }}" />
                                <input type="hidden" name="last_name" value="{{ $player->last_name }}" />
                                <input type="hidden" name="position" value="{{ $player->position }}" />
                                <input type="hidden" name="number" value="{{ $player->number }}" />
                                <input type="hidden" name="birthdate" value="{{ optional($player->birthdate)->format('Y-m-d') }}" />
                                <input type="file" name="photo" accept="image/*" class="text-xs" />
                                <input type="text" name="curp" placeholder="CURP" value="{{ $player->curp }}" class="ml-2 rounded-md border px-2 py-1 text-xs" />
                                <button type="submit" class="ml-2 rounded-md bg-indigo-600 px-2 py-1 text-xs text-white">{{ __('Guardar') }}</button>
                            </form>

                            <form action="{{ route('players.destroy', $player) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-rose-500 px-2 py-1 text-xs text-white">{{ __('Eliminar') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4 rounded-xl border border-dashed border-neutral-300 p-3 dark:border-neutral-700">
            <form action="{{ route('teams.players.store', $team) }}" method="POST" enctype="multipart/form-data" class="grid gap-2 sm:grid-cols-2">
                @csrf
                <input type="text" name="first_name" placeholder="Nombre" required class="rounded-md border px-2 py-1" />
                <input type="text" name="last_name" placeholder="Apellido" class="rounded-md border px-2 py-1" />
                <input type="text" name="position" placeholder="Posición" class="rounded-md border px-2 py-1" />
                <input type="number" name="number" placeholder="#" class="rounded-md border px-2 py-1" />
                <input type="date" name="birthdate" class="rounded-md border px-2 py-1" />
                <input type="text" name="curp" placeholder="CURP" class="rounded-md border px-2 py-1" />
                <div class="sm:col-span-2">
                    <label class="block text-xs text-neutral-600">{{ __('Foto (rostro)') }}</label>
                    <input type="file" name="photo" accept="image/*" class="mt-1 w-full" />
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm text-white">{{ __('Añadir jugador') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
