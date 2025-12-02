<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-950 text-white antialiased">
        <div class="min-h-screen flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="h-11 w-11 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-base font-bold text-white">FM</div>
                    <div class="text-center">
                        <p class="text-sm text-emerald-100 font-semibold">FutManager</p>
                        <p class="text-xs text-gray-300">Torneos sin ruido</p>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-8 shadow-xl shadow-black/20">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
