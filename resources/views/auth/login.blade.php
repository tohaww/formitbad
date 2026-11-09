<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — CustomFormBuilder | ITB Ahmad Dahlan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .jotform-orange { background-color: #ff6100; }
        .jotform-dark { background-color: #0a1551; }
        .canvas-bg {
            background-color: #f3f3fe;
            background-image: radial-gradient(#e0e7ff 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="canvas-bg min-h-screen flex flex-col justify-between items-center p-4 selection:bg-orange-100 selection:text-orange-900">

    <!-- Top Minimal Navigation -->
    <header class="w-full max-w-5xl py-4 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5 group">
            <img src="{{ asset('images/logo.png') }}" alt="Logo ITB Ahmad Dahlan" class="w-10 h-10 rounded-xl object-contain shadow-xs group-hover:scale-105 transition transform">
            <div>
                <span class="font-extrabold text-sm tracking-tight text-slate-900 block leading-tight">CustomFormBuilder</span>
                <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase block">ITB Ahmad Dahlan</span>
            </div>
        </a>

        <div class="text-xs text-slate-500 font-medium">
            Platform Form Builder Mandiri
        </div>
    </header>

    <!-- Center Login Card -->
    <main class="w-full max-w-md my-auto">
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden">
            <!-- Decorative Accent Bar -->
            <div class="absolute top-0 inset-x-0 h-1.5 jotform-orange"></div>

            <!-- Header Content -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/itbad-seal.png') }}" alt="Logo Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta" class="w-24 h-24 mx-auto mb-4 object-contain drop-shadow-sm">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke Akun Anda</h1>
                <p class="text-xs text-slate-500 mt-2 max-w-xs mx-auto leading-relaxed">
                    Kelola formulir, pantau respon responden, dan hubungkan Google Sheets secara otomatis.
                </p>
            </div>

            <!-- Error Alert -->
            @if(session('error') || request('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <span class="font-bold block">Autentikasi Gagal:</span>
                        {{ session('error') ?: request('error') }}
                    </div>
                </div>
            @endif

            <!-- Info Message Alert -->
            @if(session('message'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <div>{{ session('message') }}</div>
                </div>
            @endif

            <!-- Main Google Social Login Button -->
            <div class="space-y-4">
                <a
                    href="{{ route('auth.google.redirect') }}"
                    class="w-full flex items-center justify-center gap-3.5 py-3.5 px-5 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm shadow-xs transition hover:shadow-md hover:border-slate-400 active:scale-[0.99] group cursor-pointer"
                >
                    <!-- Official 4-color Google G Icon -->
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.15C3.34 21.46 7.39 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.27C.46 8.2 0 10.04 0 12s.46 3.8 1.27 5.42l4.01-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.39 0 3.34 2.54 1.27 6.58l4.01 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span class="group-hover:text-slate-900">Sign in with Google</span>
                </a>



                @if(app()->environment('local'))
                    <!-- Local Developer Quick Login Option -->
                    <div class="relative my-4">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                        <div class="relative flex justify-center text-[10px] uppercase tracking-wider"><span class="bg-white px-2 text-slate-400 font-extrabold">Atau Mode Pengembang (Local Dev)</span></div>
                    </div>

                    <form action="{{ route('auth.dev_login') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="w-full py-3 px-4 rounded-xl border border-dashed border-amber-300 bg-amber-50 hover:bg-amber-100/80 text-amber-900 font-bold text-xs transition flex items-center justify-center gap-2 shadow-2xs"
                        >
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            <span>Masuk Cepat Pengembang (Bypass OAuth)</span>
                        </button>
                    </form>
                @endif
            </div>

            <!-- Privacy & Security Notice -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-center gap-2 text-[11px] text-slate-400">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>OAuth 2.0 SSL Encrypted Identity</span>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-5xl py-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} CustomFormBuilder — Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta.
    </footer>

</body>
</html>

