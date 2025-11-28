@php
    $currentTeam = $team ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Left column -->
    <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Nombre del equipo') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name', $currentTeam?->name) }}" required
               class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50" />
        @error('name') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror

        <label for="owner_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Dueño del equipo') }}</label>
        <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name', $currentTeam?->owner_name) }}" required
               class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50" />
        @error('owner_name') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror

        <label for="contact_email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Correo de contacto') }}</label>
        <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $currentTeam?->contact_email) }}"
               class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50" />
        @error('contact_email') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror

        <label for="contact_phone" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Teléfono de contacto') }}</label>
        <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $currentTeam?->contact_phone) }}"
               class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50" />
        @error('contact_phone') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
    </div>

    <!-- Right column -->
    <div class="space-y-2">
        <label for="short_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Abreviatura') }}</label>
        <input type="text" id="short_name" name="short_name" value="{{ old('short_name', $currentTeam?->short_name) }}" placeholder="Ej. TOR"
               class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50" />
        @error('short_name') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror

        <label for="coach_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Director técnico') }}</label>
        <input type="text" id="coach_name" name="coach_name" value="{{ old('coach_name', $currentTeam?->coach_name) }}"
               class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50" />
        @error('coach_name') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror

        <!-- keep layout symmetric: add birthdate input here if desired or leave blank -->
        <label for="birthdate" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Fecha de fundación (opcional)') }}</label>
        <input type="date" id="birthdate" name="foundation_date" value="{{ old('foundation_date') }}"
               class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50" />
    </div>

    <!-- Logo spans both columns -->
    <div class="md:col-span-2 space-y-2">
        <label for="logo" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Escudo del equipo') }}</label>
        <input type="file" id="logo" name="logo" accept="image/*" class="w-full rounded-xl border border-dashed border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 dark:border-neutral-600 dark:bg-zinc-900 dark:text-neutral-50" />
        <p class="text-xs text-neutral-500">{{ __('Formatos permitidos: JPG, PNG, SVG. Máx 2 MB.') }}</p>
        @error('logo') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror

        @if ($currentTeam?->logo_path)
            <div class="mt-3 flex items-center gap-3 rounded-xl border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-700 dark:bg-neutral-800/40">
                <div style="width:120px; height:120px; overflow:hidden; border-radius:8px;">
                    <img src="{{ asset('storage/'.$currentTeam->logo_path) }}" alt="{{ $currentTeam->name }}" class="object-cover" style="width:100%; height:100%; object-fit:cover; display:block;" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ __('Escudo actual') }}</p>
                    <p class="text-xs text-neutral-500">{{ __('Al subir uno nuevo sustituirá a este archivo.') }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Status spans both columns -->
    <div class="md:col-span-2 space-y-2">
        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Estatus del equipo') }}</span>
        <label class="flex items-center gap-3 text-sm text-neutral-600 dark:text-neutral-300">
            <input type="hidden" name="is_active" value="0" />
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-600" @checked(old('is_active', $currentTeam?->is_active ?? true)) />
            {{ __('Disponible para torneos y asignación de jugadores') }}
        </label>
    </div>

    <!-- Players section spans both columns -->
    @if($currentTeam?->id)
        <div class="md:col-span-2">
            @include('teams.partials.players', ['team' => $currentTeam])
        </div>
    @endif
</div>
@php
    $currentTeam = $team ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Nombre del equipo') }}
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $currentTeam?->name) }}"
                required
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="short_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Abreviatura') }}
            </label>
            <input
                type="text"
                id="short_name"
                name="short_name"
                value="{{ old('short_name', $currentTeam?->short_name) }}"
                placeholder="Ej. TOR"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('short_name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label for="owner_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Dueño del equipo') }}
            </label>
            <input
                type="text"
                id="owner_name"
                name="owner_name"
                value="{{ old('owner_name', $currentTeam?->owner_name) }}"
                required
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('owner_name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="coach_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Director técnico') }}
            </label>
            <input
                type="text"
                id="coach_name"
                name="coach_name"
                value="{{ old('coach_name', $currentTeam?->coach_name) }}"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('coach_name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label for="contact_email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Correo de contacto') }}
            </label>
            <input
                type="email"
                id="contact_email"
                name="contact_email"
                value="{{ old('contact_email', $currentTeam?->contact_email) }}"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('contact_email')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="contact_phone" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Teléfono de contacto') }}
            </label>
            <input
                type="text"
                id="contact_phone"
                name="contact_phone"
                value="{{ old('contact_phone', $currentTeam?->contact_phone) }}"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('contact_phone')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="space-y-2 md:col-span-2">
        <label for="logo" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
            {{ __('Escudo del equipo') }}
        </label>
        <input
            type="file"
            id="logo"
            name="logo"
            accept="image/*"
            class="w-full rounded-xl border border-dashed border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 dark:border-neutral-600 dark:bg-zinc-900 dark:text-neutral-50"
        />
        <p class="text-xs text-neutral-500">{{ __('Formatos permitidos: JPG, PNG, SVG. Máx 2 MB.') }}</p>
        @error('logo')
            <p class="text-sm text-rose-500">{{ $message }}</p>
        @enderror

        @if ($currentTeam?->logo_path)
                <div class="mt-3 flex items-center gap-3 rounded-xl border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-700 dark:bg-neutral-800/40">
                    <div style="width:120px; height:120px; overflow:hidden; border-radius:8px;">
                        <img src="{{ asset('storage/'.$currentTeam->logo_path) }}" alt="{{ $currentTeam->name }}" class="object-cover" style="width:100%; height:100%; object-fit:cover; display:block;" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ __('Escudo actual') }}</p>
                        <p class="text-xs text-neutral-500">{{ __('Al subir uno nuevo sustituirá a este archivo.') }}</p>
                    </div>
                </div>
        @endif
    </div>

    <div class="space-y-2 md:col-span-2">
        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Estatus del equipo') }}</span>
        <label class="flex items-center gap-3 text-sm text-neutral-600 dark:text-neutral-300">
            <input type="hidden" name="is_active" value="0" />
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-600"
                @checked(old('is_active', $currentTeam?->is_active ?? true))
            />
            {{ __('Disponible para torneos y asignación de jugadores') }}
        </label>
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Nombre del equipo') }}
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $currentTeam?->name) }}"
                required
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="short_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Abreviatura') }}
            </label>
            <input
                type="text"
                id="short_name"
                name="short_name"
                value="{{ old('short_name', $currentTeam?->short_name) }}"
                placeholder="Ej. TOR"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('short_name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="owner_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Dueño del equipo') }}
            </label>
            <input
                type="text"
                id="owner_name"
                name="owner_name"
                value="{{ old('owner_name', $currentTeam?->owner_name) }}"
                required
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('owner_name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="coach_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Director técnico') }}
            </label>
            <input
                type="text"
                id="coach_name"
                name="coach_name"
                value="{{ old('coach_name', $currentTeam?->coach_name) }}"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('coach_name')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="contact_email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Correo de contacto') }}
            </label>
            <input
                type="email"
                id="contact_email"
                name="contact_email"
                value="{{ old('contact_email', $currentTeam?->contact_email) }}"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('contact_email')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="contact_phone" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Teléfono de contacto') }}
            </label>
            <input
                type="text"
                id="contact_phone"
                name="contact_phone"
                value="{{ old('contact_phone', $currentTeam?->contact_phone) }}"
                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-900 dark:text-neutral-50"
            />
            @error('contact_phone')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2 md:col-span-2">
            <label for="logo" class="block text-sm font-medium text-neutral-700 dark:text-neutral-200">
                {{ __('Escudo del equipo') }}
            </label>
            <input
                type="file"
                id="logo"
                name="logo"
                accept="image/*"
                class="w-full rounded-xl border border-dashed border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 dark:border-neutral-600 dark:bg-zinc-900 dark:text-neutral-50"
            />
            <p class="text-xs text-neutral-500">{{ __('Formatos permitidos: JPG, PNG, SVG. Máx 2 MB.') }}</p>
            @error('logo')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror

            @if ($currentTeam?->logo_path)
                    <div class="mt-3 flex items-center gap-3 rounded-xl border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-700 dark:bg-neutral-800/40">
                        <div style="width:120px; height:120px; overflow:hidden; border-radius:8px;">
                            <img src="{{ asset('storage/'.$currentTeam->logo_path) }}" alt="{{ $currentTeam->name }}" class="object-cover" style="width:100%; height:100%; object-fit:cover; display:block;" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ __('Escudo actual') }}</p>
                            <p class="text-xs text-neutral-500">{{ __('Al subir uno nuevo sustituirá a este archivo.') }}</p>
                        </div>
                    </div>
            @endif
        </div>

        <div class="space-y-2 md:col-span-2">
            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('Estatus del equipo') }}</span>
            <label class="flex items-center gap-3 text-sm text-neutral-600 dark:text-neutral-300">
                <input type="hidden" name="is_active" value="0" />
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-600"
                    @checked(old('is_active', $currentTeam?->is_active ?? true))
                />
                {{ __('Disponible para torneos y asignación de jugadores') }}
            </label>
        </div>

        @if($currentTeam?->id)
            <div class="md:col-span-2">
                @include('teams.partials.players', ['team' => $currentTeam])
            </div>
        @endif
    </div>
