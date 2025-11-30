@php
    /** @var \App\Models\Team $team */
    $team = $team ?? null;
    $players = $team?->players ?? collect();
@endphp

<div class="mt-6">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ __('Jugadores') }}</h3>
        @if(!$players->isEmpty())
            <span class="text-xs text-neutral-500">{{ $players->count() }} {{ __('registrados') }}</span>
        @endif
    </div>

    {{-- Mostrar credenciales del jugador recién creado --}}
    @if(session('player_credentials'))
        <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-green-800 dark:text-green-200">{{ __('¡Jugador creado con éxito!') }}</h4>
                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">{{ __('Guarda estas credenciales de acceso:') }}</p>
                    <div class="mt-2 space-y-1 font-mono text-sm bg-white dark:bg-neutral-900 p-3 rounded-lg border border-green-200 dark:border-green-800">
                        <div><span class="text-neutral-600 dark:text-neutral-400">Email:</span> <strong class="text-neutral-900 dark:text-neutral-100">{{ session('player_credentials')['email'] }}</strong></div>
                        <div><span class="text-neutral-600 dark:text-neutral-400">Contraseña:</span> <strong class="text-neutral-900 dark:text-neutral-100">{{ session('player_credentials')['password'] }}</strong></div>
                    </div>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2">{{ __('⚠️ Esta contraseña solo se muestra una vez. Compártela con el jugador de forma segura.') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('player_credentials_custom'))
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200">{{ __('¡Jugador creado con éxito!') }}</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">{{ session('player_credentials_custom')['message'] }}</p>
                    <div class="mt-2 font-mono text-sm bg-white dark:bg-neutral-900 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div><span class="text-neutral-600 dark:text-neutral-400">Email:</span> <strong class="text-neutral-900 dark:text-neutral-100">{{ session('player_credentials_custom')['email'] }}</strong></div>
                        <div class="text-xs text-neutral-500 mt-1">{{ __('La contraseña personalizada fue configurada correctamente.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-3 space-y-3">
        @if($players->isEmpty())
            <p class="text-sm text-neutral-500">{{ __('No hay jugadores aún. Agrega al primero.') }}</p>
        @else
            <div class="space-y-3">
                @foreach($players as $player)
                    <div class="rounded-xl border border-neutral-200 bg-white dark:bg-neutral-800 p-3 flex flex-col w-full">
                        <div class="flex items-center gap-3 justify-between">
                            <div class="flex items-center gap-2 flex-1">
                                <div class="overflow-hidden rounded-lg flex-shrink-0" style="width:48px; height:48px;">
                                    @if($player->photo_path)
                                        <img src="{{ asset('storage/'.$player->photo_path) }}" alt="{{ $player->first_name }}" class="object-cover" style="width:100%; height:100%; object-fit:cover;" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-neutral-100 text-sm text-neutral-500">{{ strtoupper(substr($player->first_name,0,1) ?: 'P') }}</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-neutral-800 dark:text-neutral-100 truncate">{{ $player->first_name }} {{ $player->last_name }}</div>
                                    <div class="text-xs text-neutral-500 truncate">{{ $player->position }} @if($player->number) · #{{ $player->number }} @endif</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <flux:modal.trigger name="edit-player-{{ $player->id }}">
                                    <button type="button" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500">
                                        {{ __('Editar') }}
                                    </button>
                                </flux:modal.trigger>
                                <form action="{{ route('players.destroy', $player) }}" method="POST" onsubmit="return confirm('¿Eliminar jugador?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-rose-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-600">{{ __('Eliminar') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal de edición --}}
                    <flux:modal name="edit-player-{{ $player->id }}" class="max-w-md">
                        <form action="{{ route('players.update', $player) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <flux:heading size="lg">{{ __('Editar jugador') }}</flux:heading>
                                <flux:subheading>{{ $player->first_name }} {{ $player->last_name }}</flux:subheading>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="col-span-2 sm:col-span-1">
                                    <flux:input name="first_name" label="Nombre" value="{{ $player->first_name }}" required />
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <flux:input name="last_name" label="Apellido" value="{{ $player->last_name }}" />
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-200 mb-1">Email (no editable)</label>
                                    <input type="text" value="{{ $player->user?->email ?? 'Sin email' }}" disabled class="w-full rounded-md border border-neutral-300 bg-neutral-100 px-3 py-2 text-sm text-neutral-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400" />
                                </div>
                                <div class="col-span-2">
                                    <flux:input name="password" label="Nueva contraseña (opcional)" type="password" placeholder="Dejar vacío para no cambiar" />
                                    <p class="text-[10px] text-neutral-500 mt-1">{{ __('Solo completa si deseas cambiar la contraseña del jugador') }}</p>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <flux:input name="position" label="Posición" value="{{ $player->position }}" />
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <flux:input name="number" label="Número" type="number" value="{{ $player->number }}" />
                                </div>
                                <div class="col-span-2">
                                    <flux:input name="birthdate" label="Fecha de nacimiento" type="date" value="{{ optional($player->birthdate)->format('Y-m-d') }}" />
                                </div>
                                <div class="col-span-2">
                                    <flux:input name="curp" label="CURP" value="{{ $player->curp }}" />
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-200 mb-1">{{ __('Foto (rostro)') }}</label>
                                    <input type="file" name="photo" accept="image/*" class="block w-full text-sm rounded-md border border-dashed border-neutral-300 dark:border-neutral-700 px-2 py-2" />
                                </div>
                            </div>

                            <div class="flex justify-end gap-2">
                                <flux:modal.close>
                                    <flux:button variant="ghost">{{ __('Cancelar') }}</flux:button>
                                </flux:modal.close>
                                <flux:button type="submit" variant="primary">{{ __('Guardar cambios') }}</flux:button>
                            </div>
                        </form>
                    </flux:modal>
                @endforeach
            </div>
        @endif

        <div class="rounded-xl border border-dashed border-neutral-300 p-4 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/40">
            <h4 class="text-xs font-semibold text-neutral-600 dark:text-neutral-300 mb-2">{{ __('Añadir jugador') }}</h4>
            
            @if($errors->any())
                <div class="mb-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-xs font-semibold text-red-800 dark:text-red-200 mb-1">{{ __('Errores en el formulario:') }}</p>
                    <ul class="text-xs text-red-700 dark:text-red-300 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('teams.players.store', $team) }}" method="POST" enctype="multipart/form-data" class="grid">
                @csrf
                <div class="space-y-3">
                    <div>
                        <input type="text" name="first_name" placeholder="Nombre" value="{{ old('first_name') }}" required class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('first_name') border-red-500 @enderror" />
                        @error('first_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="text" name="last_name" placeholder="Apellido" value="{{ old('last_name') }}" class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('last_name') border-red-500 @enderror" />
                        @error('last_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Email (para acceso al sistema)" value="{{ old('email') }}" required class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('email') border-red-500 @enderror" />
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="password" name="password" placeholder="Contraseña (opcional, se genera automáticamente si no se ingresa)" class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('password') border-red-500 @enderror" />
                        <p class="text-[10px] text-neutral-500 mt-1">{{ __('Deja vacío para generar una contraseña aleatoria') }}</p>
                        @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="text" name="position" placeholder="Posición" value="{{ old('position') }}" class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('position') border-red-500 @enderror" />
                        @error('position')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="number" name="number" placeholder="#" value="{{ old('number') }}" class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('number') border-red-500 @enderror" />
                        @error('number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('birthdate') border-red-500 @enderror" />
                        @error('birthdate')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="text" name="curp" placeholder="CURP" value="{{ old('curp') }}" class="w-full rounded-md border px-2 py-2 text-sm dark:bg-neutral-900 dark:border-neutral-700 @error('curp') border-red-500 @enderror" />
                        @error('curp')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-medium text-neutral-600 dark:text-neutral-300">{{ __('Foto (rostro)') }}</label>
                    <input type="file" name="photo" accept="image/*" class="block w-full text-xs rounded-md border border-dashed border-neutral-300 dark:border-neutral-700 @error('photo') border-red-500 @enderror" />
                    @error('photo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end mt-3 mb-1">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">{{ __('Añadir jugador') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
