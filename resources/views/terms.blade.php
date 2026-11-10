<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat &amp; Ketentuan Layanan (Terms of Service) — Formnis | ITB Ahmad Dahlan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .canvas-bg {
            background-color: #f3f3fe;
            background-image: radial-gradient(#e0e7ff 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="canvas-bg min-h-screen text-slate-800 antialiased selection:bg-orange-100 selection:text-orange-900">

    <header class="w-full max-w-4xl mx-auto py-6 px-4 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5 group">
            <img src="{{ asset('images/logo.png') }}" alt="Logo ITB Ahmad Dahlan" class="w-10 h-10 rounded-xl object-contain shadow-xs group-hover:scale-105 transition transform">
            <div>
                <span class="font-extrabold text-sm tracking-tight text-slate-900 block leading-tight">Formnis</span>
                <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase block">ITB Ahmad Dahlan</span>
            </div>
        </a>
        <a href="/" class="text-xs font-bold text-orange-600 hover:text-orange-700 transition">&larr; Kembali ke Beranda</a>
    </header>

    <main class="w-full max-w-4xl mx-auto px-4 pb-16">
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8 sm:p-12 relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-[#0a1551]"></div>

            <div class="mb-8 border-b border-slate-100 pb-6">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wider">Ketentuan Penggunaan</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">Syarat &amp; Ketentuan Layanan (Terms of Service)</h1>
                <p class="text-xs text-slate-500 mt-1">Terakhir diperbarui: 10 November 2026 &bull; Formnis ITB Ahmad Dahlan Jakarta</p>
            </div>

            <div class="prose prose-slate max-w-none text-sm leading-relaxed space-y-6 text-slate-700">
                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">1. Ketentuan Umum</h2>
                    <p>
                        Selamat datang di <strong>Formnis ITB Ahmad Dahlan Jakarta</strong> (<a href="https://formnis.itb-ad.org" class="text-blue-600 underline">https://formnis.itb-ad.org</a>). Dengan mengakses dan menggunakan platform ini, Anda setuju untuk terikat oleh Syarat dan Ketentuan berikut.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">2. Penggunaan Layanan</h2>
                    <p>
                        Pengguna bertanggung jawab penuh atas segala konten, pertanyaan, dan data formulir yang dibuat. Pengguna dilarang memanfaatkan platform ini untuk mengumpulkan data kredensial rahasia secara ilegal (phishing), konten yang melanggar hukum, atau aktivitas yang merugikan institusi maupun pihak ketiga.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">3. Batasan Tanggung Jawab</h2>
                    <p>
                        Layanan disediakan sebagaimana adanya ("as is"). Pihak pengelola berupaya menjaga ketersediaan sistem dan keamanan data secara optimal, namun tidak bertanggung jawab atas kerugian tidak langsung akibat gangguan jaringan eksternal pihak ketiga atau kelalaian pengguna dalam mengelola akun.
                    </p>
                </section>
            </div>
        </div>
    </main>

    <footer class="w-full text-center py-6 text-xs text-slate-400">
        &copy; {{ date('Y') }} Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta. All rights reserved.
    </footer>
</body>
</html>
