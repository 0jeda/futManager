@php
    /** @var \App\Models\Tournament $tournament */
    $tournament = $tournament ?? null;
    $fields = $fields ?? collect();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
            {{ __('Nombre del torneo') }} <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name', $tournament->name ?? '') }}"
            required
            class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('name') border-red-500 @enderror"
        />
        @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="field_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
            {{ __('Cancha principal') }} <span class="text-red-500">*</span>
        </label>
        <select
            name="field_id"
            id="field_id"
            required
            class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('field_id') border-red-500 @enderror"
        >
            <option value="">{{ __('Seleccionar cancha') }}</option>
            @foreach($fields as $field)
                <option value="{{ $field->id }}" {{ old('field_id', $tournament->field_id ?? '') == $field->id ? 'selected' : '' }}>
                    {{ $field->name }} ({{ $field->location }})
                </option>
            @endforeach
        </select>
        @error('field_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="category" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
            {{ __('Categoría') }}
        </label>
        <input
            type="text"
            name="category"
            id="category"
            value="{{ old('category', $tournament->category ?? '') }}"
            placeholder="{{ __('Ej: Libre, Sub-20, Veteranos') }}"
            class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('category') border-red-500 @enderror"
        />
        @error('category')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
            {{ __('Estado') }} <span class="text-red-500">*</span>
        </label>
        <select
            name="status"
            id="status"
            required
            class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('status') border-red-500 @enderror"
        >
            <option value="draft" {{ old('status', $tournament->status ?? 'draft') == 'draft' ? 'selected' : '' }}>{{ __('Borrador') }}</option>
            <option value="active" {{ old('status', $tournament->status ?? '') == 'active' ? 'selected' : '' }}>{{ __('Activo') }}</option>
            <option value="completed" {{ old('status', $tournament->status ?? '') == 'completed' ? 'selected' : '' }}>{{ __('Finalizado') }}</option>
            <option value="cancelled" {{ old('status', $tournament->status ?? '') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelado') }}</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="start_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
            {{ __('Fecha de inicio') }} <span class="text-red-500">*</span>
        </label>
        <input
            type="date"
            name="start_date"
            id="start_date"
            value="{{ old('start_date', $tournament->start_date?->format('Y-m-d') ?? '') }}"
            required
            class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('start_date') border-red-500 @enderror"
        />
        @error('start_date')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="end_date" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
            {{ __('Fecha de finalización') }}
        </label>
        <input
            type="date"
            name="end_date"
            id="end_date"
            value="{{ old('end_date', $tournament->end_date?->format('Y-m-d') ?? '') }}"
            class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('end_date') border-red-500 @enderror"
        />
        @error('end_date')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label for="format" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
        {{ __('Formato de competición') }}
    </label>
    <input
        type="text"
        name="format"
        id="format"
        value="{{ old('format', $tournament->format ?? '') }}"
        placeholder="{{ __('Ej: Round Robin, Eliminación directa, Grupos + Final') }}"
        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('format') border-red-500 @enderror"
    />
    @error('format')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
        {{ __('Descripción') }}
    </label>
    <textarea
        name="description"
        id="description"
        rows="3"
        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('description') border-red-500 @enderror"
    >{{ old('description', $tournament->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

{{-- Configuración de Bracket - SOLO AL CREAR --}}
@if(!$tournament->exists)
<div class="border-t border-neutral-200 dark:border-neutral-700 pt-6 mt-6">
    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ __('Configuración de Bracket') }}</h3>
    
    <div class="space-y-4">
        <div class="flex items-center">
            <input
                type="checkbox"
                name="is_bracket"
                id="is_bracket"
                value="1"
                {{ old('is_bracket', $tournament->is_bracket ?? false) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800"
                onchange="document.getElementById('bracket_size_container').style.display = this.checked ? 'block' : 'none'"
            />
            <label for="is_bracket" class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">
                {{ __('Este es un torneo de eliminación directa (Bracket)') }}
            </label>
        </div>
        
        <div id="bracket_size_container" style="display: {{ old('is_bracket', $tournament->is_bracket ?? false) ? 'block' : 'none' }}">
            <label for="bracket_size" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                {{ __('Tamaño del bracket') }}
            </label>
            <select
                name="bracket_size"
                id="bracket_size"
                class="block w-full md:w-64 rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('bracket_size') border-red-500 @enderror"
            >
                <option value="">{{ __('Seleccionar tamaño') }}</option>
                <option value="4" {{ old('bracket_size', $tournament->bracket_size ?? '') == 4 ? 'selected' : '' }}>4 equipos (Semifinales)</option>
                <option value="8" {{ old('bracket_size', $tournament->bracket_size ?? '') == 8 ? 'selected' : '' }}>8 equipos (Cuartos de final)</option>
                <option value="16" {{ old('bracket_size', $tournament->bracket_size ?? '') == 16 ? 'selected' : '' }}>16 equipos (Octavos de final)</option>
                <option value="32" {{ old('bracket_size', $tournament->bracket_size ?? '') == 32 ? 'selected' : '' }}>32 equipos (Dieciseisavos)</option>
            </select>
            @error('bracket_size')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            
            <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                {{ __('El bracket se generará automáticamente cuando agregues equipos al torneo. Los equipos deben ser exactamente el número seleccionado.') }}
            </p>
        </div>
    </div>
</div>
@endif
