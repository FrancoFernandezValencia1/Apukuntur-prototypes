<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Base de diseños</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#f5f5f3] px-4 font-['DM_Sans'] text-[#1a1a18] antialiased">

    <main class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mx-auto h-50 w-auto">
            <h1 class="text-lg font-semibold tracking-[-0.45px]">Base de diseños y desarrollo de producto</h1>
            <p class="mt-1 text-xs text-[#a0a09c]"></p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="flex flex-col gap-4 rounded-xl border border-[#e2e2df] bg-white p-6 shadow-sm">
            @csrf

            <h2 class="text-base font-semibold">Iniciar sesión</h2>

            <div class="flex flex-col gap-1">
                <label for="correo" class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Correo</label>
                <input id="correo" type="email" name="correo" value="{{ old('correo') }}" required autofocus autocomplete="username"
                       class="w-full rounded-md border bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20 {{ $errors->has('correo') ? 'border-[#dc2626]' : 'border-[#e2e2df]' }}">
                @error('correo')
                    <p class="text-xs text-[#dc2626]">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label for="contrasena" class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Contraseña</label>
                <input id="contrasena" type="password" name="contrasena" required autocomplete="current-password"
                       class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">
                @error('contrasena')
                    <p class="text-xs text-[#dc2626]">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-[#6b6b67]">
                <input type="checkbox" name="remember" value="1" class="size-4 rounded border-[#e2e2df] text-[#1d4ed8] focus:ring-[#1d4ed8]/30">
                Mantener la sesión iniciada
            </label>

            <button type="submit" class="rounded-md bg-[#1d4ed8] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#1e40af]">
                Entrar
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-[#a0a09c]">Perfiles: Fábrica · EU</p>
    </main>
</body>
</html>
