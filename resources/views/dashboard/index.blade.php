<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Forms — Dashboard | {{ config('app.name', 'CustomFormBuilder') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .jotform-orange { background-color: #ff6100; }
        .jotform-orange-hover:hover { background-color: #e55700; }
        .jotform-dark { background-color: #0a1551; }
        .canvas-bg { background-color: #f8fafc; }
    </style>
</head>
<body class="canvas-bg min-h-screen flex flex-col selection:bg-orange-100 selection:text-orange-900">

    <!-- ================================================================= -->
    <!-- 1. TOP NAVIGATION BAR (Header Jotform "My Forms" Style)          -->
    <!-- ================================================================= -->
    <header class="jotform-dark text-white h-16 flex items-center justify-between px-6 shadow-md z-30 shrink-0">
        <!-- Left: Logo & My Forms Brand -->
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl jotform-orange flex items-center justify-center font-black text-white text-base shadow-sm group-hover:scale-105 transition transform">
                    F
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-sm tracking-tight text-white block leading-tight">CustomFormBuilder</span>
                    <span class="text-white/30 hidden sm:inline">|</span>
                    <span class="text-xs text-orange-400 font-bold uppercase tracking-wider hidden sm:inline">My Forms</span>
                </div>
            </a>
        </div>

        <!-- Right: Prominent "+ Create Form" Button, User Profile, & Logout -->
        <div class="flex items-center gap-4">
            <!-- Create Form Button -->
            <form action="{{ route('forms.store_web') }}" method="POST" class="inline">
                @csrf
                <button
                    type="submit"
                    class="jotform-orange jotform-orange-hover text-white px-4 py-2 rounded-xl text-xs font-bold tracking-wide shadow-md transition transform hover:scale-105 flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>+ Create Form</span>
                </button>
            </form>

            <span class="text-white/20">|</span>

            <!-- User Info Dropdown / Pill -->
            <div class="flex items-center gap-3">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full border border-white/20 object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-orange-500/30 text-orange-300 font-bold text-xs flex items-center justify-center border border-orange-400/30">
                        {{ strtoupper(substr($user->name ?: $user->email, 0, 2)) }}
                    </div>
                @endif
                <div class="hidden md:block text-left">
                    <div class="text-xs font-bold text-white leading-tight truncate max-w-[140px]">{{ $user->name }}</div>
                    <div class="text-[10px] text-white/60 font-mono truncate max-w-[140px]">{{ $user->email }}</div>
                </div>
            </div>

            <!-- Logout Button Form -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button
                    type="submit"
                    title="Keluar dari akun"
                    class="p-2 rounded-lg bg-white/10 hover:bg-rose-600/80 text-white/80 hover:text-white transition text-xs font-semibold flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </header>

    <!-- ================================================================= -->
    <!-- 2. MAIN DASHBOARD CONTENT AREA                                    -->
    <!-- ================================================================= -->
    <main class="flex-1 p-6 md:p-10 max-w-7xl w-full mx-auto">

        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Flash Error Notification -->
        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Summary Stats Ribbon -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Formulir</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalForms }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Formulir Aktif</div>
                    <div class="text-2xl font-black text-emerald-600 mt-0.5">{{ $activeForms }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Respon Masuk</div>
                    <div class="text-2xl font-black text-blue-600 mt-0.5">{{ $totalSubmissions }}</div>
                </div>
            </div>
        </div>

        <!-- Section Header & Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Daftar Formulir Anda</h2>
                <p class="text-xs text-slate-500 mt-0.5">Semua formulir yang Anda buat terdaftar di sini.</p>
            </div>

            <!-- Client-side Search Filter -->
            <div class="relative w-full sm:w-72">
                <input
                    type="text"
                    id="searchFormInput"
                    onkeyup="filterForms()"
                    placeholder="Cari formulir..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-orange-500 outline-none bg-white transition shadow-2xs"
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <!-- Forms Container -->
        @if($forms->isEmpty())
            <!-- Empty State Card -->
            <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center my-6">
                <div class="w-16 h-16 rounded-2xl bg-orange-50 border border-orange-100 text-orange-500 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Formulir yang Dibuat</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                    Mulai rancang formulir interaktif Anda dengan Jotform Canvas Builder dan hubungkan ke Google Sheets.
                </p>
                <form action="{{ route('forms.store_web') }}" method="POST" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="jotform-orange jotform-orange-hover text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md transition transform hover:scale-105 inline-flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Buat Formulir Pertama</span>
                    </button>
                </form>
            </div>
        @else
            <!-- Grid of Form Cards -->
            <div id="formsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($forms as $form)
                    <div
                        class="form-card bg-white rounded-2xl border border-slate-200 hover:border-orange-400 hover:shadow-lg transition-all duration-200 p-6 flex flex-col justify-between group"
                        data-title="{{ strtolower($form->title) }}"
                        data-slug="{{ strtolower($form->slug) }}"
                    >
                        <div>
                            <!-- Card Header: Status Badge & Quick Info -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $form->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $form->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="text-[11px] text-slate-400 flex items-center gap-1 font-mono">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $form->created_at ? $form->created_at->format('d M Y') : '-' }}
                                </span>
                            </div>

                            <!-- Form Title -->
                            <h3 class="font-extrabold text-slate-900 text-base group-hover:text-orange-600 transition tracking-tight line-clamp-1 mb-1">
                                <a href="{{ route('forms.edit', $form->id) }}">
                                    {{ $form->title }}
                                </a>
                            </h3>

                            <!-- Slug Link -->
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-mono mb-3 truncate">
                                <span>/f/{{ $form->slug }}</span>
                            </div>

                            <!-- Description -->
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                {{ $form->description ?: 'Tidak ada deskripsi.' }}
                            </p>
                        </div>

                        <!-- Card Footer & Actions -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <!-- Stats badges -->
                            <div class="flex items-center justify-between text-xs">
                                <a
                                    href="{{ route('forms.submissions', $form->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg text-xs transition"
                                >
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <span>{{ $form->submissions_count }} Respon</span>
                                </a>

                                <!-- Integration Badges (Sheets / Webhook icons) -->
                                <div class="flex items-center gap-1.5">
                                    @if($form->target_spreadsheet_id)
                                        <span title="Google Sheets Terhubung" class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 87.3 78" xmlns="http://www.w3.org/2000/svg"><path fill="#0F9D58" d="M6 0h45l21 21v51c0 3.3-2.7 6-6 6H6c-3.3 0-6-2.7-6-6V6C0 2.7 2.7 0 6 0z"/><path fill="#FFF" d="M19 32h34v6H19zm0 11h34v6H19z"/></svg>
                                        </span>
                                    @endif
                                    @if($form->webhook_url)
                                        <span title="Webhook Terhubung" class="w-6 h-6 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons Toolbar -->
                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <!-- 1. Edit Form -->
                                <a
                                    href="{{ route('forms.edit', $form->id) }}"
                                    class="py-2 px-2 text-center jotform-orange hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit</span>
                                </a>

                                <!-- 2. View Form Public -->
                                <a
                                    href="/f/{{ $form->slug }}"
                                    target="_blank"
                                    class="py-2 px-2 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>View</span>
                                </a>

                                <!-- 3. Submissions / Inbox -->
                                <a
                                    href="{{ route('forms.submissions', $form->id) }}"
                                    class="py-2 px-2 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Data</span>
                                </a>
                            </div>

                            <!-- Delete Form Button with Confirmation -->
                            <div class="pt-1 text-right">
                                <button
                                    type="button"
                                    onclick="confirmDeleteForm('{{ $form->id }}', '{{ addslashes($form->title) }}')"
                                    class="text-[11px] text-slate-400 hover:text-rose-600 transition flex items-center gap-1 ml-auto"
                                    title="Hapus Formulir Ini"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus Formulir</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </main>

    <!-- ================================================================= -->
    <!-- 3. CENTERED CONFIRMATION POPUP MODAL (MODERN POPUP DI TENGAH)     -->
    <!-- ================================================================= -->
    <div
        id="deleteConfirmModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200"
    >
        <div
            id="deleteModalCard"
            class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transform scale-95 transition-transform duration-200 text-center"
        >
            <!-- Danger Icon -->
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100 shadow-xs">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>

            <!-- Title & Details -->
            <h3 class="text-base font-extrabold text-slate-900 mb-1.5">Hapus Formulir?</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                Apakah Anda yakin ingin menghapus <span id="deleteFormTitle" class="font-bold text-slate-800"></span>? Seluruh pertanyaan dan respon yang masuk akan terhapus permanen.
            </p>

            <!-- Action Buttons -->
            <form id="deleteFormAction" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex items-center gap-2.5">
                    <button
                        type="button"
                        onclick="closeDeleteModal()"
                        class="w-1/2 py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs transition"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="w-1/2 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-md shadow-rose-600/20 flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Ya, Hapus</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto py-6 px-6 text-center text-xs text-slate-400 border-t border-slate-200 mt-12">
        CustomFormBuilder Platform &copy; {{ date('Y') }} — Institut Teknologi dan Bisnis Ahmad Dahlan Jakarta.
    </footer>

    <!-- Client-side filter script -->
    <script>
        function filterForms() {
            const query = document.getElementById('searchFormInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.form-card');
            cards.forEach(card => {
                const title = card.getAttribute('data-title') || '';
                const slug = card.getAttribute('data-slug') || '';
                if (title.includes(query) || slug.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Centered Delete Confirmation Popup Functions
        function confirmDeleteForm(formId, formTitle) {
            const modal = document.getElementById('deleteConfirmModal');
            const card = document.getElementById('deleteModalCard');
            const form = document.getElementById('deleteFormAction');
            const titleSpan = document.getElementById('deleteFormTitle');

            form.action = `/forms/${formId}`;
            titleSpan.textContent = `"${formTitle}"`;

            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            });
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmModal');
            const card = document.getElementById('deleteModalCard');

            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        // Close when clicking backdrop or pressing Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDeleteModal();
        });
        document.getElementById('deleteConfirmModal')?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) closeDeleteModal();
        });
    </script>
</body>
</html>

