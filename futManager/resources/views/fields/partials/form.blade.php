@php
    $currentField = $field ?? null;
@endphp

<div class="space-y-6">
    <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">
            {{ __('Nombre de la cancha') }}
        </label>
        <input
            type="text"
            id="name"
            name="name"
            required
            value="{{ old('name', $currentField?->name) }}"
            class="block w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-800 dark:text-neutral-50"
        />
        @error('name')
            <p class="text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="owner_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">
            {{ __('Dueño asignado') }}
        </label>
        <select
            id="owner_id"
            name="owner_id"
            required
            class="block w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-800 dark:text-neutral-50"
        >
            <option value="" disabled {{ old('owner_id', $currentField?->owner_id) ? '' : 'selected' }}>
                {{ __('Selecciona un dueño') }}
            </option>
            @foreach ($owners as $owner)
                <option value="{{ $owner->id }}" @selected((int) old('owner_id', $currentField?->owner_id) === $owner->id)>
                    {{ $owner->name }}
                </option>
            @endforeach
        </select>
        @error('owner_id')
            <p class="text-sm text-rose-500">{{ $message }}</p>
        @enderror
        @if ($owners->isEmpty())
            <p class="text-sm text-amber-600">{{ __('No hay dueños registrados. Da de alta uno antes de crear canchas.') }}</p>
        @endif
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label for="location" class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                {{ __('Ubicación') }}
            </label>
            <input
                type="text"
                id="location"
                name="location"
                value="{{ old('location', $currentField?->location) }}"
                class="block w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-800 dark:text-neutral-50"
            />
            @error('location')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="surface" class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                {{ __('Superficie') }}
            </label>
            <input
                type="text"
                id="surface"
                name="surface"
                value="{{ old('surface', $currentField?->surface) }}"
                placeholder="{{ __('Sintética, cemento, pasto, etc.') }}"
                class="block w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-neutral-700 dark:bg-zinc-800 dark:text-neutral-50"
            />
            @error('surface')
                <p class="text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="space-y-2">
        <span class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Disponibilidad') }}</span>
        <label class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-300">
            <input type="hidden" name="is_active" value="0">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-600"
                @checked(old('is_active', $currentField?->is_active ?? true))
            />
            {{ __('Disponible para reservar y programar partidos') }}
        </label>
        @error('is_active')
            <p class="text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>
