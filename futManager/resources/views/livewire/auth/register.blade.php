<x-layouts.auth>
    <div class="space-y-6 text-white">
        <div class="space-y-1">
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-emerald-100">Únete</p>
            <h1 class="text-2xl font-semibold">Crear cuenta</h1>
            <p class="text-sm text-gray-300">Registra tu club y comienza a organizar tus torneos.</p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
            @csrf

            <flux:input
                name="name"
                :label="__('Nombre completo')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Juan Pérez')"
            />

            <flux:input
                name="email"
                :label="__('Correo electrónico')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="tu@correo.com"
            />

            <flux:input
                name="password"
                :label="__('Contraseña')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('********')"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('Confirmar contraseña')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('********')"
                viewable
            />

            <div class="space-y-4">
                <flux:button type="submit" variant="primary" class="w-full !bg-emerald-500 hover:!bg-emerald-400 text-slate-950 font-semibold shadow-lg shadow-emerald-500/30">
                    {{ __('Crear cuenta') }}
                </flux:button>

                <div class="text-center text-sm text-gray-300">
                    <span>{{ __('¿Ya tienes cuenta?') }}</span>
                    <flux:link class="text-emerald-200 hover:text-emerald-100 font-semibold" :href="route('login')" wire:navigate>
                        {{ __('Inicia sesión') }}
                    </flux:link>
                </div>
            </div>
        </form>
    </div>
</x-layouts.auth>
