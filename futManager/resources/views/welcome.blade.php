<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>FutManager - Sistema de gestión de torneos</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-white antialiased">
        <header class="max-w-6xl mx-auto px-6 py-8 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-base font-bold">FM</div>
                <div>
                    <p class="text-sm text-emerald-100 font-semibold">FutManager</p>
                    <p class="text-xs text-gray-300">Torneos sin estrés</p>
                </div>
            </a>

            @if (Route::has('login'))
                <div class="flex items-center gap-3">
                    <a class="text-sm text-gray-200 hover:text-white" href="{{ route('login') }}">Iniciar sesión</a>
                    @if (Route::has('register'))
                        <a class="px-4 py-2 rounded-xl bg-emerald-500 text-slate-950 font-semibold hover:bg-emerald-400 shadow-lg shadow-emerald-500/30" href="{{ route('register') }}">Crear cuenta</a>
                    @endif
                </div>
            @endif
        </header>

        <main class="max-w-6xl mx-auto px-6 pb-16 space-y-14">
            <section class="max-w-3xl space-y-6">
                <div class="badge-pill">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Listo para la temporada
                </div>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">Organiza torneos sin adornos.</h1>
                <p class="text-lg text-gray-200/85">Calendarios, resultados y plantillas en un flujo sencillo para admins y técnicos.</p>
                <div class="flex flex-wrap gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-3 rounded-xl bg-emerald-500 text-slate-950 font-semibold hover:bg-emerald-400">
                            Empezar ahora
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="px-5 py-3 rounded-xl border border-white/15 text-white hover:border-white/30">
                        Ver tablero
                    </a>
                </div>
            </section>

            <section class="grid md:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5 space-y-2">
                    <p class="text-sm text-gray-300">Programación simple</p>
                    <h3 class="text-xl font-semibold">Fixtures en minutos</h3>
                    <p class="text-sm text-gray-200/85">Publica calendarios y evita choques de horarios con validaciones básicas.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5 space-y-2">
                    <p class="text-sm text-gray-300">Equipos y plantillas</p>
                    <h3 class="text-xl font-semibold">Todo en un tablero</h3>
                    <p class="text-sm text-gray-200/85">Resultados, sanciones y listas de jugadores visibles para tu staff.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5 space-y-2">
                    <p class="text-sm text-gray-300">Reportes</p>
                    <h3 class="text-xl font-semibold">Compartir en un clic</h3>
                    <p class="text-sm text-gray-200/85">Genera PDFs y comparte enlaces con árbitros y capitanes.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5 space-y-2">
                    <p class="text-sm text-gray-300">Enfoque</p>
                    <h3 class="text-xl font-semibold">Menos ruido</h3>
                    <p class="text-sm text-gray-200/85">Solo lo esencial: agenda, equipos, resultados y reportes.</p>
                </div>
            </section>
        </main>
    </body>
</html>
