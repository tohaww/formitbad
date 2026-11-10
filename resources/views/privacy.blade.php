<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi (Privacy Policy) — Formnis | ITB Ahmad Dahlan</title>
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
            <div class="absolute top-0 inset-x-0 h-1.5 bg-[#ff6100]"></div>

            <div class="mb-8 border-b border-slate-100 pb-6">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-600 uppercase tracking-wider">Kebijakan Resmi</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">Kebijakan Privasi (Privacy Policy)</h1>
                <p class="text-xs text-slate-500 mt-1">Terakhir diperbarui: 10 November 2026 &bull; Formnis ITB Ahmad Dahlan Jakarta</p>
            </div>

            <div class="prose prose-slate max-w-none text-sm leading-relaxed space-y-6 text-slate-700">
                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">1. Pendahuluan</h2>
                    <p>
                        Aplikasi <strong>Formnis ITB Ahmad Dahlan Jakarta</strong> (dapat diakses melalui <a href="https://formnis.itb-ad.org" class="text-orange-600 underline">https://formnis.itb-ad.org</a>) dikembangkan untuk memfasilitasi sivitas akademika, staf, dosen, dan mahasiswa Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta dalam pembuatan, pengelolaan, dan pengumpulan formulir digital secara mandiri dan aman.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">2. Informasi yang Kami Kumpulkan</h2>
                    <p>Dalam pengoperasian platform, kami mengumpulkan data terbatas yang diperlukan untuk layanan autentikasi dan integrasi formulir:</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-2">
                        <li><strong>Informasi Akun Pengguna:</strong> Nama lengkap, alamat email resmi institusi, dan foto profil yang diperoleh melalui layanan Google Sign-In (OAuth 2.0).</li>
                        <li><strong>Data Tanggapan Formulir:</strong> Isian jawaban yang dikirimkan oleh responden formulir yang dibuat oleh pengguna.</li>
                        <li><strong>Token Akses Integrasi Google:</strong> Token otorisasi OAuth untuk menghubungkan formulir dengan Google Sheets dan Google Drive pengguna.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">3. Penggunaan Izin Google OAuth (Google API Services User Data Policy)</h2>
                    <p>
                        Formnis menggunakan izin API Google khusus (seperti <code>https://www.googleapis.com/auth/spreadsheets</code> dan <code>https://www.googleapis.com/auth/drive.file</code>) semata-mata untuk:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 pl-2">
                        <li>Membuat spreadsheet Google Sheets baru atas persetujuan pengguna untuk menyimpan tanggapan formulir.</li>
                        <li>Mengirimkan (sinkronisasi) data baris tanggapan formulir langsung ke spreadsheet Google Sheets yang telah dipilih.</li>
                    </ul>
                    <p class="mt-2">
                        Penggunaan dan transfer data yang diterima dari Google APIs oleh Formnis tunduk pada <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener noreferrer" class="text-orange-600 underline font-semibold">Kebijakan Data Pengguna Layanan Google API</a>, termasuk persyaratan <em>Limited Use</em>.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">4. Perlindungan dan Penyimpanan Data</h2>
                    <p>
                        Kami menerapkan standar enkripsi modern (HTTPS/TLS) untuk seluruh transmisi data antara peramban Anda dan server. Token otorisasi disimpan secara terenkripsi di lingkungan basis data yang aman. Kami tidak menjual, memperdagangkan, atau membagikan data pribadi Anda kepada pihak ketiga mana pun.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">5. Kontak Kami</h2>
                    <p>
                        Apabila Anda memiliki pertanyaan atau kendala terkait privasi data pada platform ini, Anda dapat menghubungi tim pengelola sistem di:
                    </p>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                        <p class="font-bold text-slate-800">Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta</p>
                        <p class="text-slate-600">Website: <a href="https://itb-ad.org" class="text-orange-600 underline">https://itb-ad.org</a></p>
                        <p class="text-slate-600">Domain Layanan: <a href="https://formnis.itb-ad.org" class="text-orange-600 underline">https://formnis.itb-ad.org</a></p>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer class="w-full text-center py-6 text-xs text-slate-400">
        &copy; {{ date('Y') }} Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta. All rights reserved.
    </footer>
</body>
</html>
