<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formnis — Platform Formulir Digital | ITB Ahmad Dahlan Jakarta</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .canvas-bg {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="canvas-bg min-h-screen text-slate-800 antialiased selection:bg-orange-100 selection:text-orange-900 flex flex-col justify-between">

    <!-- Navbar -->
    <header class="w-full bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo ITB Ahmad Dahlan" class="w-10 h-10 rounded-xl object-contain shadow-xs group-hover:scale-105 transition transform">
                <div>
                    <span class="font-extrabold text-base tracking-tight text-slate-900 block leading-tight">Formnis</span>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase block">ITB Ahmad Dahlan Jakarta</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600">
                <a href="#fitur" class="hover:text-orange-600 transition">Fitur</a>
                <a href="#integrasi" class="hover:text-orange-600 transition">Integrasi Google</a>
                <a href="{{ route('privacy') }}" class="hover:text-orange-600 transition">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="hover:text-orange-600 transition">Syarat & Ketentuan</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition shadow-sm">
                        Ke Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-[#ff6100] hover:bg-[#e55700] text-white font-bold text-xs transition shadow-sm hover:shadow-md flex items-center gap-2">
                        <span>Masuk / Login</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow">
        <section class="max-w-5xl mx-auto px-4 sm:px-6 pt-16 pb-20 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-50 border border-orange-200/80 text-orange-700 text-xs font-bold uppercase tracking-wider mb-6">
                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                Platform Formulir Digital Resmi ITB Ahmad Dahlan
            </div>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight max-w-4xl mx-auto">
                Kelola Formulir &amp; Survei Kampus Lebih Cepat, Terintegrasi Langsung.
            </h1>

            <p class="mt-6 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                <strong>Formnis</strong> memudahkan dosen, staf, dan sivitas akademika Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta untuk merancang formulir pendaftaran, kuesioner akademik, serta mengumpulkan data secara aman.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-[#ff6100] hover:bg-[#e55700] text-white font-extrabold text-sm shadow-md hover:shadow-xl transition transform active:scale-[0.99] flex items-center justify-center gap-3">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#ffffff" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z"/>
                        <path fill="#ffffff" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.15C3.34 21.46 7.39 24 12 24z"/>
                        <path fill="#ffffff" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.27C.46 8.2 0 10.04 0 12s.46 3.8 1.27 5.42l4.01-3.15z"/>
                        <path fill="#ffffff" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.39 0 3.34 2.54 1.27 6.58l4.01 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Masuk dengan Akun Google</span>
                </a>
                <a href="{{ route('privacy') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm shadow-xs transition">
                    Lihat Kebijakan Privasi
                </a>
            </div>

            <!-- ITB-AD Seal Branding -->
            <div class="mt-12 flex items-center justify-center gap-3 text-xs text-slate-400">
                <img src="{{ asset('images/itbad-seal.png') }}" alt="ITB Ahmad Dahlan Seal" class="w-8 h-8 object-contain opacity-80">
                <span>Dikelola secara resmi untuk lingkungan kampus ITB Ahmad Dahlan Jakarta</span>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="fitur" class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dirancang untuk Kebutuhan Akademik &amp; Administrasi</h2>
                <p class="text-sm text-slate-500 mt-2">Segala kemudahan pembuatan formulir dalam satu ekosistem terpadu.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Form Builder Intuitif</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Susun formulir custom mulai dari teks singkat, pilihan ganda, dropdown, hingga upload berkas bukti pendaftaran tanpa batasan teknis.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Sinkronisasi Google Sheets</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Data respon responden otomatis tersinkronisasi langsung ke Google Sheets akun Anda secara *real-time* untuk analisis data lebih fleksibel.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Aman &amp; Terverifikasi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Autentikasi akun Google resmi dengan enkripsi SSL. Data responden dikelola sesuai standar perlindungan privasi data institusi.
                    </p>
                </div>
            </div>
        </section>

        <!-- Google OAuth Transparency Section (Crucial for Google Review) -->
        <section id="integrasi" class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1.5 bg-[#0a1551]"></div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-700 font-bold text-xs">
                        OAuth
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900">Penggunaan Akun Google &amp; Privasi Data</h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Aplikasi ini menggunakan layanan <strong>Google OAuth 2.0</strong> semata-mata untuk mempermudah otorisasi pengguna dan otomatisasi penyimpanan berkas:
                </p>
                <ul class="mt-4 space-y-2.5 text-xs text-slate-600">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Autentikasi (Identitas Pengguna):</strong> Mengambil alamat email dan nama profil untuk pembuatan akun serta sesi masuk pengelola formulir.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Integrasi Google Sheets (Opsional):</strong> Izin akses hanya digunakan untuk menulis dan memperbarui baris data respon formulir pada dokumen spreadsheet yang Anda tentukan sendiri.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Kerahasiaan Terjamin:</strong> Kami tidak pernah menjual, mentransfer, atau memanfaatkan data akun Google Anda untuk keperluan periklanan pihak ketiga.</span>
                    </li>
                </ul>
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap gap-4 text-xs font-semibold text-slate-500">
                    <span>Baca selengkapnya di:</span>
                    <a href="{{ route('privacy') }}" class="text-orange-600 hover:underline">Kebijakan Privasi (Privacy Policy)</a>
                    <span>&bull;</span>
                    <a href="{{ route('terms') }}" class="text-orange-600 hover:underline">Syarat &amp; Ketentuan Layanan (Terms of Service)</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                <span>&copy; {{ date('Y') }} <strong>Formnis</strong> — Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('privacy') }}" class="hover:text-slate-800 transition">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="hover:text-slate-800 transition">Syarat &amp; Ketentuan</a>
                <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-700 transition">Masuk Akun</a>
            </div>
        </div>
    </footer>

</body>
</html>

