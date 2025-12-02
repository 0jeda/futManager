<x-layouts.auth>
    <div class="space-y-6 text-white">
        <div class="space-y-1">
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-emerald-100">Bienvenido</p>
            <h1 class="text-2xl font-semibold">Inicia sesión</h1>
            <p class="text-sm text-gray-300">Accede a tu tablero y continúa donde te quedaste.</p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
            @csrf

            <flux:input
                name="email"
                :label="__('Correo electrónico')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="tu@correo.com"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Contraseña')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('********')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0 text-emerald-300 hover:text-emerald-200" :href="route('password.request')" wire:navigate>
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Recordarme')" :checked="old('remember')" />

            <div class="space-y-4">
                <flux:button variant="primary" type="submit" class="w-full !bg-emerald-500 hover:!bg-emerald-400 text-slate-950 font-semibold shadow-lg shadow-emerald-500/30" data-test="login-button">
                    {{ __('Iniciar sesión') }}
                </flux:button>

                @if (Route::has('register'))
                    <div class="text-center text-sm text-gray-300">
                        <span>{{ __('¿No tienes cuenta?') }}</span>
                        <flux:link class="text-emerald-200 hover:text-emerald-100 font-semibold" :href="route('register')" wire:navigate>
                            {{ __('Regístrate aquí') }}
                        </flux:link>
                    </div>
                @endif
            </div>
        </form>
    </div>
</x-layouts.auth>
