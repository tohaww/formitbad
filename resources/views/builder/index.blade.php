@php
    $theme = array_merge([
        'page_bg' => '#f3f3fe',
        'card_bg' => '#ffffff',
        'primary_color' => '#059669',
        'font_family' => 'Plus Jakarta Sans',
        'card_radius' => 'rounded-2xl',
        'card_width' => 'max-w-2xl',
        'logo_url' => '',
        'logo_align' => 'center',
        'logo_height' => '56',
    ], $form->settings['theme'] ?? []);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder — {{ $form->title }} | {{ config('app.name', 'CustomFormBuilder') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .jotform-orange { background-color: #ff6100; }
        .jotform-orange-hover:hover { background-color: #e55700; }
        .jotform-dark { background-color: #0a1551; }
        .canvas-bg { background-color: {{ $theme['page_bg'] }}; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="canvas-bg h-screen flex flex-col overflow-hidden select-none">

    <!-- ================================================================= -->
    <!-- 1. TOP NAVIGATION BAR (Header Oranye khas Jotform)                -->
    <!-- ================================================================= -->
    <header class="jotform-orange text-white h-14 flex items-center justify-between px-5 shadow-md z-30 shrink-0">
        <!-- Left: Back to My Forms, Brand & Form Title Quick Edit -->
        <div class="flex items-center gap-2.5 w-1/3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-black/20 hover:bg-black/35 text-white rounded-xl text-xs font-bold transition shrink-0" title="Kembali ke Dashboard Formulir">
                <svg class="w-4 h-4 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="hidden sm:inline">My Forms</span>
            </a>
            <span class="text-white/30 hidden sm:inline">|</span>
            <div class="truncate max-w-[200px] flex items-center gap-1.5 group cursor-pointer" onclick="switchTab('settings')" title="Klik untuk edit judul form">
                <span id="navFormTitle" class="text-xs font-bold truncate hover:underline text-white">{{ $form->title }}</span>
                <svg class="w-3.5 h-3.5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
        </div>

        <!-- Center: 3 Navigation Tabs (BUILD, SETTINGS, PUBLISH) -->
        <nav class="flex items-center justify-center h-full gap-2">
            <button id="tabBtnBuild" onclick="switchTab('build')" class="px-5 h-full flex items-center gap-2 text-xs font-bold uppercase tracking-wider border-b-4 border-white text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>BUILD</span>
            </button>
            <button id="tabBtnSettings" onclick="switchTab('settings')" class="px-5 h-full flex items-center gap-2 text-xs font-bold uppercase tracking-wider border-b-4 border-transparent text-white/80 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>SETTINGS</span>
            </button>
            <button id="tabBtnPublish" onclick="switchTab('publish')" class="px-5 h-full flex items-center gap-2 text-xs font-bold uppercase tracking-wider border-b-4 border-transparent text-white/80 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                <span>PUBLISH</span>
            </button>
        </nav>

        <!-- Right: Submissions Link, Status, Save Button & Preview Switch -->
        <div class="flex items-center justify-end gap-3 w-1/3">
            <!-- Submissions Inbox Link -->
            <a
                href="{{ route('forms.submissions', $form->id) }}"
                class="px-3 py-1.5 bg-white/15 hover:bg-white/25 text-white rounded-lg text-xs font-bold transition hidden lg:flex items-center gap-1.5"
                title="Lihat Kotak Masuk Respon"
            >
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Respon</span>
                <span class="px-1.5 py-0.2 bg-white text-orange-600 rounded-full text-[10px] font-black">{{ $form->submissions()->count() }}</span>
            </a>

            <!-- Sync / Saved Indicator -->
            <div id="saveStatus" class="flex items-center gap-1.5 text-xs text-white/80">
                <svg id="saveStatusIcon" class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span id="saveStatusText" class="hidden sm:inline">Tersimpan</span>
            </div>

            <button onclick="saveFormFields(true)" class="px-3.5 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                Simpan
            </button>

            <!-- Preview Toggle Switch -->
            <div class="flex items-center gap-2 bg-black/20 px-3 py-1.5 rounded-full">
                <span class="text-xs font-semibold text-white/90">Preview</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="previewToggle" class="sr-only peer" onchange="togglePreviewMode(this.checked)">
                    <div class="w-8 h-4 bg-white/30 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-400"></div>
                </label>
            </div>
        </div>
    </header>

    <!-- ================================================================= -->
    <!-- 2. MAIN WORKSPACE AREA                                            -->
    <!-- ================================================================= -->
    <div class="flex-1 flex overflow-hidden relative">

        <!-- ------------------------------------------------------------- -->
        <!-- FLOATING BUTTON ("+ Add Form Element")                        -->
        <!-- ------------------------------------------------------------- -->
        <div class="absolute left-4 top-4 z-20">
            <button
                id="btnToggleLeftDrawer"
                onclick="toggleLeftDrawer()"
                class="jotform-dark hover:bg-slate-800 text-white px-4 py-2.5 rounded-full shadow-lg flex items-center gap-2 text-xs font-bold tracking-wide transition transform hover:scale-105"
            >
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Add Form Element</span>
            </button>
        </div>

        <!-- ------------------------------------------------------------- -->
        <!-- FLOATING BUTTON ("Form Designer")                             -->
        <!-- ------------------------------------------------------------- -->
        <div class="absolute right-4 top-4 z-20">
            <button
                id="btnToggleDesignerDrawer"
                type="button"
                onclick="toggleDesignerDrawer()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-full shadow-lg flex items-center gap-2 text-xs font-bold tracking-wide transition transform hover:scale-105"
                title="Kustomisasi Tema, Warna & Logo"
            >
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                <span class="hidden sm:inline">Form Designer</span>
            </button>
        </div>

        <!-- ------------------------------------------------------------- -->
        <!-- LEFT DRAWER (ELEMENTS PALETTE)                                -->
        <!-- ------------------------------------------------------------- -->
        <aside
            id="leftDrawer"
            class="w-80 bg-white border-r border-slate-200 flex flex-col z-20 absolute inset-y-0 left-0 shadow-2xl transition-transform duration-300 transform -translate-x-full"
        >
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                    Elemen Formulir
                </h3>
                <button onclick="toggleLeftDrawer(false)" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Drawer Tabs (BASIC vs WIDGETS) -->
            <div class="flex border-b border-slate-200 px-4 bg-slate-50 text-xs font-semibold">
                <button id="drawerTabBasic" onclick="switchDrawerCategory('basic')" class="py-2.5 px-3 border-b-2 border-orange-500 text-orange-600 font-bold transition flex-1 text-center">
                    Basic
                </button>
                <button id="drawerTabWidgets" onclick="switchDrawerCategory('widgets')" class="py-2.5 px-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex-1 text-center">
                    Widgets
                </button>
            </div>

            <!-- Elements List Container -->
            <div id="drawerElementsList" class="flex-1 overflow-y-auto p-4 space-y-2">
                <!-- Dynamically populated from WidgetRegistry -->
            </div>
        </aside>

        <!-- ------------------------------------------------------------- -->
        <!-- CENTER WORKSPACE / CANVAS AREA                                -->
        <!-- ------------------------------------------------------------- -->
        <main id="workspaceCanvas" class="flex-1 overflow-y-auto p-6 md:p-10 flex flex-col items-center transition-colors duration-300" style="background-color: {{ $theme['page_bg'] }};">

            <!-- TAB 1: BUILD CANVAS -->
            <div id="viewBuild" class="w-full {{ $theme['card_width'] }} flex flex-col items-center transition-all duration-300">
                <!-- White Paper Form Card -->
                <div id="canvasCard" class="w-full {{ $theme['card_radius'] }} shadow-sm border border-slate-200 overflow-hidden mb-12 transition-all" style="background-color: {{ $theme['card_bg'] }}; font-family: '{{ $theme['font_family'] }}', sans-serif;">

                    <!-- Form Header Area -->
                    <div id="canvasHeader" class="p-8 border-b border-slate-100 relative group">
                        <div id="canvasHeaderAlign" class="text-{{ $theme['logo_align'] }}">
                            <!-- Form Logo Container -->
                            <div id="canvasLogoContainer" class="mb-4">
                                <div id="canvasLogoImgWrapper" class="{{ empty($theme['logo_url']) ? 'hidden' : '' }} inline-flex items-center gap-2 group/logo relative">
                                    <img id="canvasLogoImg" src="{{ $theme['logo_url'] }}" alt="Form Logo" class="inline-block object-contain rounded-lg" style="height: {{ $theme['logo_height'] }}px;">
                                    <button type="button" onclick="openDesignerLogoTab()" class="ml-2 px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded text-[11px] font-semibold border border-slate-300 transition">
                                        Ganti Logo
                                    </button>
                                </div>
                                <button id="btnCanvasAddLogo" type="button" onclick="openDesignerLogoTab()" class="{{ !empty($theme['logo_url']) ? 'hidden' : '' }} inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold transition border border-dashed border-slate-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>+ Add Logo</span>
                                </button>
                            </div>
                            <h1 id="canvasFormTitle" class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight cursor-text" contenteditable="true" onblur="updateFormMeta('title', this.innerText)">{{ $form->title }}</h1>
                            <p id="canvasFormDesc" class="text-sm text-slate-500 mt-2 max-w-lg mx-auto cursor-text" contenteditable="true" onblur="updateFormMeta('description', this.innerText)">{{ $form->description ?: 'Klik di sini untuk menulis deskripsi formulir...' }}</p>
                        </div>
                    </div>

                    <!-- Canvas Form Fields Container (Drag-and-Drop Sortable) -->
                    <div id="formFieldsCanvas" class="p-6 md:p-8 space-y-4 min-h-[220px]">
                        <!-- Dynamically populated from form data -->
                    </div>

                    <!-- Form Submit Button Footer -->
                    <div class="p-8 pt-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <button id="canvasSubmitBtn" type="button" class="px-8 py-3 text-white font-bold text-sm {{ $theme['card_radius'] }} shadow-sm transition cursor-pointer" style="background-color: {{ $theme['primary_color'] }};">
                            Submit Form
                        </button>
                        <span class="text-xs text-slate-400">CustomFormBuilder Engine</span>
                    </div>
                </div>
            </div>

            <!-- TAB 2: SETTINGS PANEL (2-Column Jotform Style) -->
            <div id="viewSettings" class="w-full max-w-5xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hidden mb-12 flex flex-col md:flex-row min-h-[580px]">
                
                <!-- Left Sidebar Menu (Form Settings, Integrations, Thank You Page) -->
                <aside class="w-full md:w-64 bg-slate-50 border-r border-slate-200 p-4 shrink-0 flex flex-col justify-between">
                    <div>
                        <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Settings Menu</div>
                        <nav class="space-y-1">
                            <!-- Menu 1: Form Settings -->
                            <button
                                type="button"
                                id="btnSettingsMenuGeneral"
                                onclick="switchSettingsSubMenu('general')"
                                class="w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-bold transition text-left bg-orange-50 text-orange-600 border border-orange-200/80 shadow-xs"
                            >
                                <div class="p-2 rounded-lg bg-orange-100 text-orange-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold">Form Settings</div>
                                    <div class="text-[10px] text-slate-400 font-normal">Judul, status & url</div>
                                </div>
                            </button>

                            <!-- Menu 2: Integrations -->
                            <button
                                type="button"
                                id="btnSettingsMenuIntegrations"
                                onclick="switchSettingsSubMenu('integrations')"
                                class="w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition text-left"
                            >
                                <div class="p-2 rounded-lg bg-slate-200/80 text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold">Integrations</div>
                                    <div class="text-[10px] text-slate-400 font-normal">Sheets & Webhooks</div>
                                </div>
                            </button>

                            <!-- Menu 3: Thank You Page -->
                            <button
                                type="button"
                                id="btnSettingsMenuThankYou"
                                onclick="switchSettingsSubMenu('thankyou')"
                                class="w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition text-left"
                            >
                                <div class="p-2 rounded-lg bg-slate-200/80 text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold">Thank You Page</div>
                                    <div class="text-[10px] text-slate-400 font-normal">Pesan setelah submit</div>
                                </div>
                            </button>
                        </nav>
                    </div>

                    <div class="p-3 bg-white rounded-xl border border-slate-200/70 text-[11px] text-slate-500">
                        <span class="font-bold text-slate-700 block mb-0.5">CustomFormBuilder</span>
                        Integrasi tersimpan real-time ke database.
                    </div>
                </aside>

                <!-- Right Content Area -->
                <div class="flex-1 p-6 md:p-8 overflow-y-auto">

                    <!-- SUB-PANEL 1: FORM SETTINGS -->
                    <div id="subPanelGeneral" class="space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-lg font-bold text-slate-900">Form Settings</h2>
                            <p class="text-xs text-slate-500">Atur judul utama formulir, URL publik, dan status ketersediaan.</p>
                        </div>

                        <form onsubmit="saveFormSettings(event)" class="space-y-5 max-w-xl">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul Formulir</label>
                                <input type="text" id="setting_title" value="{{ $form->title }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Slug URL Unik</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-mono">/f/</span>
                                    <input type="text" id="setting_slug" value="{{ $form->slug }}" class="w-full px-4 py-2.5 rounded-r-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-orange-500 outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                                <textarea id="setting_description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-orange-500 outline-none">{{ $form->description }}</textarea>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Status Formulir</div>
                                    <div class="text-[11px] text-slate-500">Jika aktif, responden dapat mengakses dan mengirimkan jawaban</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="setting_is_active" {{ $form->is_active ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="px-6 py-2.5 jotform-orange hover:bg-orange-600 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- SUB-PANEL 2: INTEGRATIONS CATALOG -->
                    <div id="subPanelIntegrations" class="hidden space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-lg font-bold text-slate-900">Katalog Integrasi</h2>
                            <p class="text-xs text-slate-500">Hubungkan formulir Anda dengan Google Sheets dan Webhooks untuk sinkronisasi otomatis.</p>
                        </div>

                        <!-- Catalog Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            <!-- CARD 1: Google Sheets -->
                            <div
                                onclick="openGoogleSheetsModal()"
                                class="p-5 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-white hover:bg-emerald-50/20 cursor-pointer transition shadow-xs group flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center p-2 border border-emerald-100">
                                            <!-- Google Sheets SVG Icon -->
                                            <svg class="w-8 h-8" viewBox="0 0 87.3 78" xmlns="http://www.w3.org/2000/svg">
                                                <path fill="#0F9D58" d="M6 0h45l21 21v51c0 3.3-2.7 6-6 6H6c-3.3 0-6-2.7-6-6V6C0 2.7 2.7 0 6 0z"/>
                                                <path fill="#87CEAB" d="M51 0l21 21H51z"/>
                                                <path fill="#0B8043" d="M72 21L51 0v21z"/>
                                                <path fill="#FFF" d="M19 32h34v6H19zm0 11h34v6H19zm0 11h34v6H19z"/>
                                            </svg>
                                        </div>
                                        <span id="badgeGoogleSheets" class="px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide {{ !empty($form->target_spreadsheet_id) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ !empty($form->target_spreadsheet_id) ? 'Connected' : 'Not Connected' }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-sm group-hover:text-emerald-700 transition">Google Sheets</h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                        Simpan hasil submission secara otomatis ke baris spreadsheet Anda secara real-time.
                                    </p>
                                </div>
                                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-emerald-600">
                                    <span id="cardSheetsDetail" class="text-[11px] text-slate-400 font-mono truncate max-w-[180px]">
                                        {{ !empty($form->target_spreadsheet_id) ? 'ID: ' . substr($form->target_spreadsheet_id, 0, 10) . '...' : 'Belum terhubung' }}
                                    </span>
                                    <span class="group-hover:translate-x-1 transition flex items-center gap-1 font-bold">
                                        Konfigurasi &rarr;
                                    </span>
                                </div>
                            </div>

                            <!-- CARD 2: Webhooks -->
                            <div
                                onclick="openWebhooksModal()"
                                class="p-5 rounded-2xl border border-slate-200 hover:border-purple-400 bg-white hover:bg-purple-50/20 cursor-pointer transition shadow-xs group flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center p-2 border border-purple-100">
                                            <!-- Webhook SVG Icon -->
                                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <span id="badgeWebhooks" class="px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide {{ !empty($form->webhook_url) ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ !empty($form->webhook_url) ? 'Connected' : 'Not Connected' }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-sm group-hover:text-purple-700 transition">Webhooks</h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                        Kirim payload HTTP POST instan ke endpoint API sistem Anda dengan signature HMAC-SHA256.
                                    </p>
                                </div>
                                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-purple-600">
                                    <span id="cardWebhookDetail" class="text-[11px] text-slate-400 font-mono truncate max-w-[180px]">
                                        {{ !empty($form->webhook_url) ? $form->webhook_url : 'Belum terhubung' }}
                                    </span>
                                    <span class="group-hover:translate-x-1 transition flex items-center gap-1 font-bold">
                                        Konfigurasi &rarr;
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- SUB-PANEL 3: THANK YOU PAGE -->
                    <div id="subPanelThankYou" class="hidden space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-lg font-bold text-slate-900">Thank You Page</h2>
                            <p class="text-xs text-slate-500">Kustomisasi tampilan halaman konfirmasi yang dilihat responden setelah berhasil mengirim jawaban.</p>
                        </div>

                        <form onsubmit="saveThankYouSettings(event)" class="space-y-5 max-w-xl">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul Konfirmasi</label>
                                <input
                                    type="text"
                                    id="setting_thank_you_title"
                                    value="{{ $form->settings['thank_you_title'] ?? 'Terima Kasih!' }}"
                                    oninput="updateThankYouPreview()"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-orange-500 outline-none"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pesan / Instruksi Tambahan</label>
                                <textarea
                                    id="setting_thank_you_message"
                                    rows="4"
                                    oninput="updateThankYouPreview()"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-orange-500 outline-none"
                                >{{ $form->settings['thank_you_message'] ?? 'Jawaban Anda telah berhasil kami terima.' }}</textarea>
                            </div>

                            <!-- Live Card Preview -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pratinjau Responden</label>
                                <div class="p-6 rounded-2xl bg-emerald-50/60 border border-emerald-200 text-center">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <h4 id="previewThankYouTitle" class="font-bold text-base text-slate-900 mb-1">
                                        {{ $form->settings['thank_you_title'] ?? 'Terima Kasih!' }}
                                    </h4>
                                    <p id="previewThankYouMsg" class="text-xs text-slate-600 max-w-md mx-auto">
                                        {{ $form->settings['thank_you_message'] ?? 'Jawaban Anda telah berhasil kami terima.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="px-6 py-2.5 jotform-orange hover:bg-orange-600 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    Simpan Thank You Page
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- TAB 3: PUBLISH PANEL -->
            <div id="viewPublish" class="w-full max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 p-8 hidden mb-12">
                <h2 class="text-xl font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Publikasikan Formulir
                </h2>
                <p class="text-slate-500 text-xs mb-6">Formulir Anda siap menerima jawaban dari responden publik.</p>

                <!-- Share Link Box -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tautan Publik Shareable</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            readonly
                            id="publishUrlInput"
                            value="{{ url('/f/' . $form->slug) }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-xs font-mono text-slate-800"
                        >
                        <button onclick="copyPublishUrl()" class="px-5 py-2.5 jotform-dark text-white rounded-lg text-xs font-bold hover:bg-slate-800 transition shrink-0 flex items-center gap-1.5">
                            <span id="btnCopyText">Salin Tautan</span>
                        </button>
                        <a href="/f/{{ $form->slug }}" target="_blank" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold transition shrink-0 flex items-center gap-1">
                            Buka
                        </a>
                    </div>
                </div>

                <!-- Embed Code Box -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Sematan (Embed iFrame)</label>
                    <textarea readonly rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-300 bg-white text-xs font-mono text-slate-600"><iframe src="{{ url('/f/' . $form->slug) }}" width="100%" height="800" frameborder="0"></iframe></textarea>
                </div>
            </div>

            <!-- PREVIEW MODAL / OVERLAY IFRAME (JOTFORM STYLE) -->
            <div id="previewContainer" class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl border border-slate-300 flex flex-col hidden my-2 overflow-hidden" style="height: calc(100vh - 110px);">
                <!-- Top Preview Header Bar -->
                <div class="bg-slate-900 text-white px-5 py-2.5 text-xs flex items-center justify-between shrink-0 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-2 font-bold">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Mode Preview Responden
                        </span>
                        <span class="text-slate-600 hidden sm:inline">|</span>
                        <!-- Device Viewport Switcher -->
                        <div class="hidden sm:flex items-center bg-slate-800 rounded-lg p-0.5 border border-slate-700">
                            <button type="button" onclick="setPreviewDevice('desktop')" id="prevBtnDesktop" class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-orange-500 text-white transition flex items-center gap-1.5" title="Tampilan Desktop">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Desktop</span>
                            </button>
                            <button type="button" onclick="setPreviewDevice('tablet')" id="prevBtnTablet" class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5" title="Tampilan Tablet">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Tablet</span>
                            </button>
                            <button type="button" onclick="setPreviewDevice('mobile')" id="prevBtnMobile" class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5" title="Tampilan Ponsel / Mobile">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Mobile</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <a id="btnOpenNewTabPreview" href="#" target="_blank" class="text-slate-300 hover:text-white flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg transition" title="Buka di tab browser baru">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span class="hidden sm:inline">Buka Tab Baru</span>
                        </a>
                        <button type="button" onclick="togglePreviewMode(false)" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Tutup</span>
                        </button>
                    </div>
                </div>

                <!-- Iframe Viewport Container (Auto-scrollable) -->
                <div class="flex-1 w-full bg-slate-100 flex justify-center items-stretch overflow-hidden p-2 sm:p-4">
                    <iframe id="previewIframe" src="" class="w-full h-full border-0 rounded-xl shadow-sm bg-white transition-all duration-300" style="max-width: 100%;" scrolling="yes"></iframe>
                </div>
            </div>
        </main>

        <!-- ------------------------------------------------------------- -->
        <!-- 3. RIGHT PROPERTIES DRAWER                                     -->
        <!-- ------------------------------------------------------------- -->
        <aside
            id="rightDrawer"
            class="w-88 bg-white border-l border-slate-200 flex flex-col z-20 absolute inset-y-0 right-0 shadow-2xl transition-transform duration-300 transform translate-x-full overflow-hidden"
            style="width: 360px;"
        >
            <div class="p-4 border-b border-slate-100 flex items-center justify-between jotform-dark text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span id="rightDrawerTitle" class="text-xs font-bold uppercase tracking-wider">Field Properties</span>
                </div>
                <button onclick="toggleRightDrawer(false)" class="text-white/60 hover:text-white p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Properties Body Form -->
            <div id="rightDrawerBody" class="flex-1 overflow-y-auto p-5 space-y-5 text-xs">
                <!-- Dynamically populated when field is selected -->
            </div>
        </aside>

        <!-- ------------------------------------------------------------- -->
        <!-- 4. RIGHT FORM DESIGNER DRAWER (JOTFORM STYLE)                 -->
        <!-- ------------------------------------------------------------- -->
        <aside
            id="designerDrawer"
            class="w-96 bg-white border-l border-slate-200 flex flex-col z-20 absolute inset-y-0 right-0 shadow-2xl transition-transform duration-300 transform translate-x-full overflow-hidden"
            style="width: 380px;"
        >
            <div class="p-4 border-b border-slate-100 flex items-center justify-between jotform-dark text-white">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 bg-blue-500 rounded-lg text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </span>
                    <div>
                        <h3 class="font-extrabold text-xs uppercase tracking-wider text-white">Form Designer</h3>
                        <p class="text-[10px] text-slate-400">Warna, Font & Layout Formulir</p>
                    </div>
                </div>
                <button onclick="toggleDesignerDrawer(false)" class="text-white/60 hover:text-white p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Designer Sub-Tabs -->
            <div class="flex border-b border-slate-200 px-2 bg-slate-50 text-[11px] font-bold">
                <button id="desTabColors" type="button" onclick="switchDesignerTab('colors')" class="py-2.5 px-2 border-b-2 border-orange-500 text-orange-600 font-bold transition flex-1 text-center">Warna</button>
                <button id="desTabStyles" type="button" onclick="switchDesignerTab('styles')" class="py-2.5 px-2 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex-1 text-center">Layout</button>
                <button id="desTabFonts" type="button" onclick="switchDesignerTab('fonts')" class="py-2.5 px-2 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex-1 text-center">Font</button>
                <button id="desTabLogo" type="button" onclick="switchDesignerTab('logo')" class="py-2.5 px-2 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex-1 text-center">Logo</button>
            </div>

            <!-- Designer Body Content -->
            <div id="designerBody" class="flex-1 overflow-y-auto p-5 space-y-6 text-xs">
                <!-- Tab 1: Colors -->
                <div id="desSecColors" class="space-y-5">
                    <div>
                        <label class="block font-bold text-slate-800 mb-2">Warna Latar Kanvas (Page Background)</label>
                        <div class="flex items-center gap-2 mb-2">
                            <button type="button" onclick="setThemeProperty('page_bg', '#f3f3fe')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #f3f3fe;" title="Soft Purple"></button>
                            <button type="button" onclick="setThemeProperty('page_bg', '#f8fafc')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #f8fafc;" title="Slate Clean"></button>
                            <button type="button" onclick="setThemeProperty('page_bg', '#f0fdf4')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #f0fdf4;" title="Pastel Mint"></button>
                            <button type="button" onclick="setThemeProperty('page_bg', '#fffbeb')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #fffbeb;" title="Warm Sand"></button>
                            <button type="button" onclick="setThemeProperty('page_bg', '#0f172a')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #0f172a;" title="Dark Slate"></button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="color" id="inputThemePageBg" value="{{ $theme['page_bg'] }}" onchange="setThemeProperty('page_bg', this.value)" class="w-8 h-8 rounded-lg cursor-pointer border border-slate-300 p-0.5">
                            <span class="text-[11px] text-slate-500">Pilih warna kustom</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <label class="block font-bold text-slate-800 mb-2">Warna Kartu Formulir (Card Background)</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="setThemeProperty('card_bg', '#ffffff')" class="py-2 px-2 text-center border rounded-xl hover:border-orange-500 transition font-semibold text-[11px] bg-white text-slate-800">Putih</button>
                            <button type="button" onclick="setThemeProperty('card_bg', '#f8fafc')" class="py-2 px-2 text-center border rounded-xl hover:border-orange-500 transition font-semibold text-[11px] bg-slate-50 text-slate-800">Slate</button>
                            <button type="button" onclick="setThemeProperty('card_bg', '#1e293b')" class="py-2 px-2 text-center border rounded-xl hover:border-orange-500 transition font-semibold text-[11px] bg-slate-800 text-white">Dark Card</button>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <label class="block font-bold text-slate-800 mb-2">Warna Tombol Submit (Primary Accent)</label>
                        <div class="flex items-center gap-2 mb-2">
                            <button type="button" onclick="setThemeProperty('primary_color', '#059669')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #059669;" title="Emerald Green"></button>
                            <button type="button" onclick="setThemeProperty('primary_color', '#ff6100')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #ff6100;" title="Jotform Orange"></button>
                            <button type="button" onclick="setThemeProperty('primary_color', '#4f46e5')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #4f46e5;" title="Royal Indigo"></button>
                            <button type="button" onclick="setThemeProperty('primary_color', '#0284c7')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #0284c7;" title="Sky Blue"></button>
                            <button type="button" onclick="setThemeProperty('primary_color', '#e11d48')" class="w-7 h-7 rounded-full border-2 border-slate-300 shadow-xs hover:scale-110 transition" style="background-color: #e11d48;" title="Crimson Rose"></button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="color" id="inputThemePrimary" value="{{ $theme['primary_color'] }}" onchange="setThemeProperty('primary_color', this.value)" class="w-8 h-8 rounded-lg cursor-pointer border border-slate-300 p-0.5">
                            <span class="text-[11px] text-slate-500">Pilih warna kustom</span>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Layout & Styles -->
                <div id="desSecStyles" class="space-y-5 hidden">
                    <div>
                        <label class="block font-bold text-slate-800 mb-2">Lebar Formulir (Card Width)</label>
                        <div class="space-y-1.5">
                            <button type="button" onclick="setThemeProperty('card_width', 'max-w-xl')" class="w-full py-2 px-3 text-left border rounded-xl hover:border-orange-500 transition font-medium flex items-center justify-between">
                                <span>Ringkas (Compact)</span>
                                <span class="text-[10px] text-slate-400 font-mono">580px</span>
                            </button>
                            <button type="button" onclick="setThemeProperty('card_width', 'max-w-2xl')" class="w-full py-2 px-3 text-left border rounded-xl hover:border-orange-500 transition font-medium flex items-center justify-between">
                                <span>Standar (Default)</span>
                                <span class="text-[10px] text-slate-400 font-mono">680px</span>
                            </button>
                            <button type="button" onclick="setThemeProperty('card_width', 'max-w-3xl')" class="w-full py-2 px-3 text-left border rounded-xl hover:border-orange-500 transition font-medium flex items-center justify-between">
                                <span>Lebar (Wide)</span>
                                <span class="text-[10px] text-slate-400 font-mono">820px</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <label class="block font-bold text-slate-800 mb-2">Lengkungan Sudut Kartu (Border Radius)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="setThemeProperty('card_radius', 'rounded-none')" class="py-2.5 px-2 text-center border rounded-none hover:border-orange-500 transition font-medium text-[11px]">Persegi (Sharp)</button>
                            <button type="button" onclick="setThemeProperty('card_radius', 'rounded-xl')" class="py-2.5 px-2 text-center border rounded-xl hover:border-orange-500 transition font-medium text-[11px]">Sedang (12px)</button>
                            <button type="button" onclick="setThemeProperty('card_radius', 'rounded-2xl')" class="py-2.5 px-2 text-center border rounded-2xl hover:border-orange-500 transition font-medium text-[11px]">Lembut (16px)</button>
                            <button type="button" onclick="setThemeProperty('card_radius', 'rounded-3xl')" class="py-2.5 px-2 text-center border rounded-3xl hover:border-orange-500 transition font-medium text-[11px]">Ekstra (24px)</button>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Fonts -->
                <div id="desSecFonts" class="space-y-3 hidden">
                    <label class="block font-bold text-slate-800 mb-2">Pilih Jenis Font Formulir</label>
                    <button type="button" onclick="setThemeProperty('font_family', 'Plus Jakarta Sans')" class="w-full p-3 border rounded-xl hover:border-orange-500 transition text-left flex items-center justify-between" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        <span class="font-bold text-sm">Plus Jakarta Sans</span>
                        <span class="text-[10px] text-slate-400">Modern Clean</span>
                    </button>
                    <button type="button" onclick="setThemeProperty('font_family', 'Inter')" class="w-full p-3 border rounded-xl hover:border-orange-500 transition text-left flex items-center justify-between" style="font-family: 'Inter', sans-serif;">
                        <span class="font-bold text-sm">Inter</span>
                        <span class="text-[10px] text-slate-400">Sleek Tech</span>
                    </button>
                    <button type="button" onclick="setThemeProperty('font_family', 'Roboto')" class="w-full p-3 border rounded-xl hover:border-orange-500 transition text-left flex items-center justify-between" style="font-family: 'Roboto', sans-serif;">
                        <span class="font-bold text-sm">Roboto</span>
                        <span class="text-[10px] text-slate-400">Classic Standard</span>
                    </button>
                    <button type="button" onclick="setThemeProperty('font_family', 'Poppins')" class="w-full p-3 border rounded-xl hover:border-orange-500 transition text-left flex items-center justify-between" style="font-family: 'Poppins', sans-serif;">
                        <span class="font-bold text-sm">Poppins</span>
                        <span class="text-[10px] text-slate-400">Geometric Bold</span>
                    </button>
                    <button type="button" onclick="setThemeProperty('font_family', 'Outfit')" class="w-full p-3 border rounded-xl hover:border-orange-500 transition text-left flex items-center justify-between" style="font-family: 'Outfit', sans-serif;">
                        <span class="font-bold text-sm">Outfit</span>
                        <span class="text-[10px] text-slate-400">Youthful Fresh</span>
                    </button>
                </div>

                <!-- Tab 4: Logo -->
                <div id="desSecLogo" class="space-y-4 hidden">
                    <div>
                        <label class="block font-bold text-slate-800 mb-1">URL Gambar Logo</label>
                        <input type="text" id="inputThemeLogoUrl" value="{{ $theme['logo_url'] }}" oninput="setThemeProperty('logo_url', this.value)" placeholder="https://domain.com/logo.png" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs outline-none focus:border-orange-500">
                        <p class="text-[10px] text-slate-400 mt-1">Masukkan URL gambar logo atau klik preset di bawah.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Preset Logo Resmi</label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="setThemeProperty('logo_url', '/images/itbad-seal.png')" class="flex-1 p-2 border rounded-xl hover:border-orange-500 transition flex items-center gap-2">
                                <img src="/images/itbad-seal.png" alt="Seal" class="w-6 h-6 object-contain">
                                <span class="font-semibold text-[11px]">Lambang ITB-AD</span>
                            </button>
                            <button type="button" onclick="setThemeProperty('logo_url', '/images/logo.png')" class="flex-1 p-2 border rounded-xl hover:border-orange-500 transition flex items-center gap-2">
                                <img src="/images/logo.png" alt="Icon" class="w-6 h-6 object-contain rounded">
                                <span class="font-semibold text-[11px]">Ikon Aplikasi</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <label class="block font-bold text-slate-800 mb-1.5">Posisi Logo</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="setThemeProperty('logo_align', 'left')" class="py-2 text-center border rounded-xl hover:border-orange-500 font-semibold text-[11px]">Kiri</button>
                            <button type="button" onclick="setThemeProperty('logo_align', 'center')" class="py-2 text-center border rounded-xl hover:border-orange-500 font-semibold text-[11px]">Tengah</button>
                            <button type="button" onclick="setThemeProperty('logo_align', 'right')" class="py-2 text-center border rounded-xl hover:border-orange-500 font-semibold text-[11px]">Kanan</button>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <label class="block font-bold text-slate-800 mb-1.5">Tinggi Logo</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="setThemeProperty('logo_height', '40')" class="py-2 text-center border rounded-xl hover:border-orange-500 font-semibold text-[11px]">Kecil (40px)</button>
                            <button type="button" onclick="setThemeProperty('logo_height', '56')" class="py-2 text-center border rounded-xl hover:border-orange-500 font-semibold text-[11px]">Sedang (56px)</button>
                            <button type="button" onclick="setThemeProperty('logo_height', '72')" class="py-2 text-center border rounded-xl hover:border-orange-500 font-semibold text-[11px]">Besar (72px)</button>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <button type="button" onclick="setThemeProperty('logo_url', '')" class="w-full py-2 px-3 border border-rose-200 text-rose-600 rounded-xl hover:bg-rose-50 text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Logo dari Formulir</span>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

    </div>

    <!-- ================================================================= -->
    <!-- 4. MODALS (GOOGLE SHEETS & WEBHOOKS)                              -->
    <!-- ================================================================= -->

    <!-- MODAL 1: GOOGLE SHEETS CONFIGURATION (JOTFORM OAUTH & SERVICE ACCOUNT) -->
    <div id="modalGoogleSheets" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Integrasi Google Sheets</h3>
                        <p class="text-[11px] text-slate-500">Sinkronkan submission formulir ke baris Google Sheet (Gaya Jotform)</p>
                    </div>
                </div>
                <button onclick="closeGoogleSheetsModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 text-xs overflow-y-auto flex-1">
                <!-- 1. Google OAuth Card (If NOT connected) -->
                <div id="sheetsOAuthCard" class="p-4 bg-orange-50/70 border border-orange-200 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                            <span class="font-extrabold text-slate-900 text-xs">Otorisasi Akun Google (User Consent)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-bold text-[10px]">Rekomendasi</span>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Hubungkan akun Google Anda untuk membuat & mengelola spreadsheet di Google Drive secara otomatis <strong>tanpa perlu share manual ke robot/email service account</strong>.
                    </p>
                    <a
                        href="{{ route('web.dashboard.integrations.sheets.connect', ['form_id' => $form->id]) }}"
                        class="w-full py-2.5 px-4 bg-white hover:bg-slate-50 text-slate-800 font-bold rounded-xl border border-slate-300 text-xs shadow-xs flex items-center justify-center gap-2 transition"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                        <span>Hubungkan dengan Google (OAuth)</span>
                    </a>
                </div>

                <!-- 2. Google OAuth Connected Box (If connected) -->
                <div id="sheetsOAuthConnectedBox" class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">G</div>
                        <div>
                            <div class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Google OAuth Terhubung</span>
                            </div>
                            <div id="userGoogleEmailDisplay" class="text-xs font-black text-slate-900">...</div>
                        </div>
                    </div>
                    <button type="button" onclick="disconnectUserGoogleAccount()" class="text-xs font-semibold text-rose-600 hover:underline">Putuskan Akun</button>
                </div>

                <!-- 3. Option A: Create Auto Spreadsheet (One-Click Jotform Mode) -->
                <div id="boxAutoCreateSpreadsheet" class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Opsi 1: Buat Spreadsheet Otomatis</span>
                        </div>
                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded-full">Satu Klik</span>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-tight">
                        Sistem otomatis membuat spreadsheet baru di Google Drive Anda bernama <strong>"{{ $form->title }} (Tanggapan)"</strong> beserta kolom header lengkap.
                    </p>
                    <button
                        type="button"
                        id="btnCreateAutoSheets"
                        onclick="createAutoSpreadsheet()"
                        class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs flex items-center justify-center gap-2 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Buat Spreadsheet Otomatis di Google Drive</span>
                    </button>
                </div>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-slate-200"></div>
                    <span class="flex-shrink mx-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Atau Hubungkan Spreadsheet yang Sudah Ada</span>
                    <div class="flex-grow border-t border-slate-200"></div>
                </div>

                <!-- 4. Option B: Use Existing Spreadsheet ID -->
                <div class="space-y-3">
                    <!-- Input Spreadsheet ID -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Target Spreadsheet ID</label>
                        <input
                            type="text"
                            id="modal_sheets_id"
                            placeholder="Contoh: 1Sjv7F-zZpJG1jEhoHQERL4ICjgb_cIYvQwnn3-AxJsM"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-xs focus:ring-2 focus:ring-emerald-500 outline-none"
                        >
                        <p class="text-[10px] text-slate-400 mt-1">ID dapat ditemukan pada URL spreadsheet di browser: /spreadsheets/d/<strong>{ID}</strong>/edit</p>
                    </div>

                    <!-- Input Sheet Name -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Target Sheet Name (Tab)</label>
                        <input
                            type="text"
                            id="modal_sheets_name"
                            placeholder="Sheet1"
                            value="Sheet1"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-xs focus:ring-2 focus:ring-emerald-500 outline-none"
                        >
                    </div>

                    <!-- Toggle Auto-create Header -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                        <div>
                            <div class="font-bold text-slate-800">Auto-create Header</div>
                            <div class="text-[10px] text-slate-500">Buat baris header otomatis sesuai label field jika sheet kosong</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="modal_sheets_auto_header" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Test Connection Button & Result Box -->
                    <div>
                        <button
                            type="button"
                            id="btnTestSheets"
                            onclick="testGoogleSheetsConnection()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-xs transition flex items-center gap-2"
                        >
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Test Connection</span>
                        </button>
                        <div id="modal_sheets_test_result" class="hidden mt-2 p-3 rounded-lg text-[11px] leading-tight"></div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <button
                        type="button"
                        id="btnDisconnectSheets"
                        onclick="disconnectGoogleSheets()"
                        class="px-3.5 py-2 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition border border-rose-200 hidden"
                    >
                        Disconnect Sheet
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="closeGoogleSheetsModal()"
                        class="px-4 py-2 text-slate-600 hover:bg-slate-200/60 rounded-lg text-xs font-semibold transition"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        id="btnSaveSheets"
                        onclick="saveGoogleSheetsIntegration()"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-xs"
                    >
                        Save & Connect
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: WEBHOOK CONFIGURATION -->
    <div id="modalWebhooks" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Konfigurasi Webhook Dispatcher</h3>
                        <p class="text-[11px] text-slate-500">Kirim HTTP POST instan setiap kali form di-submit</p>
                    </div>
                </div>
                <button onclick="closeWebhooksModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 text-xs">
                <!-- Info Alert -->
                <div class="p-3.5 bg-purple-50/70 border border-purple-200 rounded-xl text-purple-900 text-[11px] leading-relaxed flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-purple-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        Payload akan dikirim secara asynchronous via worker dengan header <code>Content-Type: application/json</code> dan verifikasi <code>X-Webhook-Signature: sha256={hash}</code>.
                    </div>
                </div>

                <!-- Input Webhook Target URL -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Webhook Target URL <span class="text-rose-500">*</span></label>
                    <input
                        type="url"
                        id="modal_webhook_url"
                        placeholder="https://api.domain.com/webhooks/catch"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-xs focus:ring-2 focus:ring-purple-500 outline-none"
                    >
                    <p class="text-[10px] text-slate-400 mt-1">Harus diawali dengan http:// atau https://</p>
                </div>

                <!-- Input Secret Key -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Webhook Secret Key (HMAC SHA-256)</label>
                    <input
                        type="text"
                        id="modal_webhook_secret"
                        placeholder="whsec_kunci_rahasia_opsional"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-xs focus:ring-2 focus:ring-purple-500 outline-none"
                    >
                    <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk menandatangani header <code>X-Webhook-Signature</code>.</p>
                </div>

                <!-- Test Connection Button & Result Box -->
                <div>
                    <button
                        type="button"
                        id="btnTestWebhook"
                        onclick="testWebhooksConnection()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-xs transition flex items-center gap-2"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>Send Test Webhook</span>
                    </button>
                    <div id="modal_webhook_test_result" class="hidden mt-2 p-3 rounded-lg text-[11px] leading-tight font-mono"></div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <button
                        type="button"
                        id="btnDisconnectWebhook"
                        onclick="disconnectWebhooks()"
                        class="px-3.5 py-2 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition border border-rose-200 hidden"
                    >
                        Disconnect
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="closeWebhooksModal()"
                        class="px-4 py-2 text-slate-600 hover:bg-slate-200/60 rounded-lg text-xs font-semibold transition"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        id="btnSaveWebhook"
                        onclick="saveWebhooksIntegration()"
                        class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold transition shadow-xs"
                    >
                        Save & Connect
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: CENTERED CONFIRMATION MODAL -->
    <div
        id="genericConfirmModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200"
    >
        <div
            id="genericModalCard"
            class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transform scale-95 transition-transform duration-200 text-center"
        >
            <div id="genericModalIconBox" class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100 shadow-xs">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 id="genericModalTitle" class="text-base font-extrabold text-slate-900 mb-1.5">Konfirmasi</h3>
            <p id="genericModalMessage" class="text-xs text-slate-500 mb-6 leading-relaxed">Apakah Anda yakin?</p>
            <div class="flex items-center gap-2.5">
                <button
                    type="button"
                    onclick="closeGenericConfirm(false)"
                    class="w-1/2 py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs transition"
                >
                    Batal
                </button>
                <button
                    type="button"
                    id="btnGenericConfirmSubmit"
                    onclick="closeGenericConfirm(true)"
                    class="w-1/2 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-md shadow-rose-600/20"
                >
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/widgetRegistry.js"></script>
    <script>
        // Form Data State
        const formId = "{{ $form->id }}";
        const formSlug = "{{ $form->slug }}";
        let fields = @json($form->fields);
        let selectedFieldId = null;
        let isSaving = false;
        let autoSaveTimer = null;

        // Init Form Builder
        document.addEventListener('DOMContentLoaded', () => {
            renderDrawerElements();
            renderCanvasFields();
            initSortable();
            loadIntegrationsStatus();
        });

        // ==========================================
        // DRAWER CONTROLS
        // ==========================================
        function toggleLeftDrawer(forceState) {
            const drawer = document.getElementById('leftDrawer');
            if (typeof forceState === 'boolean') {
                drawer.classList.toggle('-translate-x-full', !forceState);
            } else {
                drawer.classList.toggle('-translate-x-full');
            }
        }

        function toggleRightDrawer(forceState) {
            const drawer = document.getElementById('rightDrawer');
            if (!drawer) return;
            const willOpen = typeof forceState === 'boolean' ? forceState : drawer.classList.contains('translate-x-full');
            if (willOpen) {
                toggleDesignerDrawer(false);
                drawer.classList.remove('translate-x-full');
            } else {
                drawer.classList.add('translate-x-full');
            }
        }

        let currentCategory = 'basic';
        function switchDrawerCategory(cat) {
            currentCategory = cat;
            document.getElementById('drawerTabBasic').className = cat === 'basic'
                ? 'py-2.5 px-3 border-b-2 border-orange-500 text-orange-600 font-bold transition flex-1 text-center'
                : 'py-2.5 px-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex-1 text-center';
            document.getElementById('drawerTabWidgets').className = cat === 'widgets'
                ? 'py-2.5 px-3 border-b-2 border-orange-500 text-orange-600 font-bold transition flex-1 text-center'
                : 'py-2.5 px-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex-1 text-center';
            renderDrawerElements();
        }

        function renderDrawerElements() {
            const container = document.getElementById('drawerElementsList');
            const items = window.WidgetRegistry.getByCategory(currentCategory);

            container.innerHTML = items.map(item => `
                <div
                    onclick="addNewField('${item.type}')"
                    class="p-3 rounded-xl border border-slate-200 hover:border-orange-400 bg-white hover:bg-orange-50/40 cursor-pointer transition flex items-center justify-between group shadow-xs"
                >
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-slate-100 group-hover:bg-orange-100 text-slate-600 group-hover:text-orange-600 transition">
                            ${item.icon}
                        </div>
                        <div>
                            <div class="font-bold text-xs text-slate-800 group-hover:text-orange-900">${item.label}</div>
                            ${item.badge ? `<span class="inline-block px-1.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold rounded mt-0.5">${item.badge}</span>` : ''}
                        </div>
                    </div>
                    <button class="text-slate-300 group-hover:text-orange-500 font-bold text-base leading-none">+</button>
                </div>
            `).join('');
        }

        // ==========================================
        // CANVAS RENDERING & SORTABLE
        // ==========================================
        function initSortable() {
            const el = document.getElementById('formFieldsCanvas');
            new Sortable(el, {
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'opacity-40',
                onEnd: function () {
                    const fieldEls = el.querySelectorAll('.field-item');
                    const updatedFields = [];
                    fieldEls.forEach((domEl, idx) => {
                        const id = domEl.dataset.id;
                        const fieldObj = fields.find(f => f.id === id);
                        if (fieldObj) {
                            fieldObj.order_index = idx + 1;
                            updatedFields.push(fieldObj);
                        }
                    });
                    fields = updatedFields;
                    triggerAutoSave();
                }
            });
        }

        function renderCanvasFields() {
            const container = document.getElementById('formFieldsCanvas');

            if (fields.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
                        <p class="text-slate-400 text-xs font-semibold">Formulir masih kosong.</p>
                        <button onclick="toggleLeftDrawer(true)" class="mt-2 text-xs font-bold text-orange-600 hover:underline">
                            + Klik "Add Form Element" untuk menambahkan pertanyaan
                        </button>
                    </div>
                `;
                return;
            }

            container.innerHTML = fields.map((field, idx) => {
                const widget = window.WidgetRegistry.get(field.field_type);
                const isSelected = field.id === selectedFieldId;

                return `
                    <div
                        data-id="${field.id}"
                        onclick="selectField('${field.id}', event)"
                        class="field-item relative p-4 rounded-xl border transition-all cursor-pointer ${
                            isSelected
                                ? 'border-orange-500 ring-2 ring-orange-200 bg-orange-50/10'
                                : 'border-slate-200 hover:border-slate-300 bg-white'
                        }"
                    >
                        <!-- Action Toolbar (Visible on Active) -->
                        <div class="absolute right-3 top-3 flex items-center gap-1.5 ${isSelected ? 'opacity-100' : 'opacity-0 hover:opacity-100'} transition z-10">
                            <!-- Drag Handle -->
                            <button title="Geser Urutan" class="drag-handle p-1.5 rounded bg-slate-100 text-slate-500 hover:text-slate-800 cursor-grab">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                            </button>
                            <!-- Properties Gear Button -->
                            <button title="Pengaturan Field" onclick="openFieldProperties('${field.id}', event)" class="p-1.5 rounded jotform-orange text-white hover:bg-orange-600 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                            <!-- Duplicate Button -->
                            <button title="Duplikat" onclick="duplicateField('${field.id}', event)" class="p-1.5 rounded bg-slate-100 text-slate-500 hover:text-slate-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                            <!-- Delete Button -->
                            <button title="Hapus" onclick="deleteField('${field.id}', event)" class="p-1.5 rounded bg-rose-50 text-rose-600 hover:bg-rose-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        <!-- Field Label & Required Mark -->
                        <div class="pr-24">
                            <label class="block text-xs font-bold text-slate-800">
                                ${field.label}
                                ${field.is_required ? '<span class="text-rose-500 font-bold ml-0.5">*</span>' : ''}
                            </label>
                        </div>

                        <!-- Field Input Canvas Render -->
                        <div>
                            ${widget.renderCanvas(field)}
                        </div>
                    </div>
                `;
            }).join('');
        }

        // ==========================================
        // FIELD ACTIONS (ADD, SELECT, DUPLICATE, DELETE)
        // ==========================================
        function addNewField(type) {
            const widget = window.WidgetRegistry.get(type);
            const newField = {
                id: 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
                label: widget.defaultProps.label,
                field_type: type,
                order_index: fields.length + 1,
                is_required: !!widget.defaultProps.is_required,
                placeholder: widget.defaultProps.placeholder || '',
                config: JSON.parse(JSON.stringify(widget.defaultProps.config || {}))
            };

            fields.push(newField);
            renderCanvasFields();
            selectField(newField.id);
            toggleLeftDrawer(false);
            openFieldProperties(newField.id);
            triggerAutoSave();
        }

        function selectField(id, e) {
            if (e) e.stopPropagation();
            selectedFieldId = id;
            renderCanvasFields();
        }

        function openFieldProperties(id, e) {
            if (e) e.stopPropagation();
            selectedFieldId = id;
            renderCanvasFields();
            populateRightDrawer(id);
            toggleRightDrawer(true);
        }

        function duplicateField(id, e) {
            if (e) e.stopPropagation();
            const idx = fields.findIndex(f => f.id === id);
            if (idx === -1) return;

            const original = fields[idx];
            const clone = JSON.parse(JSON.stringify(original));
            clone.id = 'temp_' + Date.now();
            clone.label = original.label + ' (Salinan)';
            clone.order_index = original.order_index + 1;

            fields.splice(idx + 1, 0, clone);
            fields.forEach((f, i) => f.order_index = i + 1);
            renderCanvasFields();
            selectField(clone.id);
            triggerAutoSave();
        }

        async function deleteField(id, e) {
            if (e) e.stopPropagation();
            const confirmed = await showCenterConfirm({
                title: 'Hapus Elemen Formulir?',
                message: 'Apakah Anda yakin ingin menghapus pertanyaan ini? Perubahan akan disimpan otomatis.',
                confirmText: 'Ya, Hapus'
            });
            if (!confirmed) return;

            fields = fields.filter(f => f.id !== id);
            fields.forEach((f, i) => f.order_index = i + 1);

            if (selectedFieldId === id) {
                selectedFieldId = null;
                toggleRightDrawer(false);
            }

            renderCanvasFields();
            triggerAutoSave();
        }

        // ==========================================
        // RIGHT PROPERTIES DRAWER POPULATION
        // ==========================================
        function populateRightDrawer(fieldId) {
            const field = fields.find(f => f.id === fieldId);
            if (!field) return;

            const widget = window.WidgetRegistry.get(field.field_type);
            document.getElementById('rightDrawerTitle').innerText = widget.label + ' Properties';

            const body = document.getElementById('rightDrawerBody');

            let html = `
                <!-- Field Label -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Pertanyaan / Label</label>
                    <input
                        type="text"
                        value="${escapeHtml(field.label)}"
                        oninput="updateSelectedField('label', this.value)"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-orange-500 outline-none"
                    >
                </div>

                <!-- Placeholder -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Placeholder Text</label>
                    <input
                        type="text"
                        value="${escapeHtml(field.placeholder || '')}"
                        oninput="updateSelectedField('placeholder', this.value)"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-orange-500 outline-none"
                    >
                </div>

                <!-- Required Toggle Switch -->
                <div class="flex items-center justify-between py-2 border-y border-slate-100">
                    <div>
                        <div class="font-bold text-slate-800">Wajib Diisi (Required)</div>
                        <div class="text-[11px] text-slate-400">Responden wajib menjawab field ini</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            ${field.is_required ? 'checked' : ''}
                            onchange="updateSelectedField('is_required', this.checked)"
                            class="sr-only peer"
                        >
                        <div class="w-8 h-4 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                </div>
            `;

            if (widget.configSchema && widget.configSchema.length > 0) {
                html += `
                    <div class="pt-2">
                        <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] mb-3 pb-1 border-b border-slate-200 flex items-center justify-between">
                            <span>Konfigurasi Khusus</span>
                            <span class="text-[10px] text-orange-600 font-extrabold uppercase">${widget.type}</span>
                        </h4>
                `;

                widget.configSchema.forEach(cfg => {
                    const currentVal = field.config?.[cfg.key] ?? cfg.default ?? '';

                    if (cfg.type === 'textarea_list') {
                        const listText = Array.isArray(currentVal) ? currentVal.join('\n') : '';
                        html += `
                            <div class="mb-3">
                                <label class="block font-semibold text-slate-700 mb-1">${cfg.label}</label>
                                <textarea
                                    rows="4"
                                    oninput="updateSelectedFieldConfigList('${cfg.key}', this.value)"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono text-xs focus:ring-2 focus:ring-orange-500 outline-none"
                                >${listText}</textarea>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="mb-3">
                                <label class="block font-semibold text-slate-700 mb-1">${cfg.label}</label>
                                <input
                                    type="${cfg.type}"
                                    value="${escapeHtml(currentVal)}"
                                    placeholder="${cfg.placeholder || ''}"
                                    oninput="updateSelectedFieldConfig('${cfg.key}', this.value)"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs ${cfg.key.includes('column') || cfg.key.includes('id') ? 'font-mono' : ''} focus:ring-2 focus:ring-orange-500 outline-none"
                                >
                                ${cfg.hint ? `<div class="text-[10px] text-slate-400 mt-1">${cfg.hint}</div>` : ''}
                            </div>
                        `;
                    }
                });

                html += `</div>`;
            }

            body.innerHTML = html;
        }

        function updateSelectedField(key, value) {
            const field = fields.find(f => f.id === selectedFieldId);
            if (!field) return;

            field[key] = value;
            renderCanvasFields();
            triggerAutoSave();
        }

        function updateSelectedFieldConfig(cfgKey, value) {
            const field = fields.find(f => f.id === selectedFieldId);
            if (!field) return;

            if (!field.config) field.config = {};
            field.config[cfgKey] = value;
            renderCanvasFields();
            triggerAutoSave();
        }

        function updateSelectedFieldConfigList(cfgKey, rawText) {
            const field = fields.find(f => f.id === selectedFieldId);
            if (!field) return;

            if (!field.config) field.config = {};
            field.config[cfgKey] = rawText.split('\n').map(s => s.trim()).filter(Boolean);
            renderCanvasFields();
            triggerAutoSave();
        }

        // ==========================================
        // PERSISTENCE & AUTO-SAVE
        // ==========================================
        function triggerAutoSave() {
            setSaveStatus('saving');
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveFormFields(false);
            }, 1000);
        }

        async function saveFormFields(isManual = false) {
            if (isSaving) return;
            isSaving = true;
            setSaveStatus('saving');

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/fields`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ fields: fields })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    if (data.data) {
                        fields = data.data;
                    }
                    setSaveStatus('saved');
                    if (isManual) {
                        alert('Susunan formulir berhasil disimpan!');
                    }
                } else {
                    setSaveStatus('error');
                    console.error('Save failed:', data);
                }
            } catch (err) {
                setSaveStatus('error');
                console.error('Save error:', err);
            } finally {
                isSaving = false;
            }
        }

        function setSaveStatus(state) {
            const icon = document.getElementById('saveStatusIcon');
            const text = document.getElementById('saveStatusText');

            if (state === 'saving') {
                icon.className = 'w-4 h-4 text-amber-300 animate-spin';
                icon.innerHTML = `<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>`;
                text.innerText = 'Menyimpan...';
            } else if (state === 'saved') {
                icon.className = 'w-4 h-4 text-emerald-300';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`;
                text.innerText = 'Tersimpan';
            } else if (state === 'error') {
                icon.className = 'w-4 h-4 text-rose-300';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`;
                text.innerText = 'Gagal menyimpan';
            }
        }

        // ==========================================
        // SETTINGS & METADATA SAVE
        // ==========================================
        async function updateFormMeta(field, val) {
            val = val.trim();
            if (!val) return;

            document.getElementById('navFormTitle').innerText = val;

            await fetch(`/api/dashboard/forms/${formId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ [field]: val })
            });
        }

        function switchSettingsSubMenu(menu) {
            const btnGeneral = document.getElementById('btnSettingsMenuGeneral');
            const btnIntegrations = document.getElementById('btnSettingsMenuIntegrations');
            const btnThankYou = document.getElementById('btnSettingsMenuThankYou');

            const subPanelGeneral = document.getElementById('subPanelGeneral');
            const subPanelIntegrations = document.getElementById('subPanelIntegrations');
            const subPanelThankYou = document.getElementById('subPanelThankYou');

            // Default inactive style
            const inactiveClass = 'w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition text-left';
            const activeClass = 'w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-bold transition text-left bg-orange-50 text-orange-600 border border-orange-200/80 shadow-xs';

            btnGeneral.className = menu === 'general' ? activeClass : inactiveClass;
            btnIntegrations.className = menu === 'integrations' ? activeClass : inactiveClass;
            btnThankYou.className = menu === 'thankyou' ? activeClass : inactiveClass;

            subPanelGeneral.classList.toggle('hidden', menu !== 'general');
            subPanelIntegrations.classList.toggle('hidden', menu !== 'integrations');
            subPanelThankYou.classList.toggle('hidden', menu !== 'thankyou');

            if (menu === 'integrations') {
                loadIntegrationsStatus();
            }
        }

        async function saveFormSettings(e) {
            e.preventDefault();

            const payload = {
                title: document.getElementById('setting_title').value,
                slug: document.getElementById('setting_slug').value,
                description: document.getElementById('setting_description').value,
                is_active: document.getElementById('setting_is_active').checked
            };

            const response = await fetch(`/api/dashboard/forms/${formId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            if (response.ok && data.status === 'success') {
                alert('Pengaturan form berhasil diperbarui!');
                document.getElementById('navFormTitle').innerText = payload.title;
                document.getElementById('canvasFormTitle').innerText = payload.title;
                document.getElementById('canvasFormDesc').innerText = payload.description;
            } else {
                alert('Gagal menyimpan pengaturan: ' + (data.message || 'Periksa kembali input Anda.'));
            }
        }

        async function saveThankYouSettings(e) {
            e.preventDefault();

            const payload = {
                settings: {
                    thank_you_title: document.getElementById('setting_thank_you_title').value,
                    thank_you_message: document.getElementById('setting_thank_you_message').value
                }
            };

            const response = await fetch(`/api/dashboard/forms/${formId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            if (response.ok && data.status === 'success') {
                alert('Halaman Thank You berhasil disimpan!');
            } else {
                alert('Gagal menyimpan Thank You Page: ' + (data.message || 'Error'));
            }
        }

        function updateThankYouPreview() {
            const title = document.getElementById('setting_thank_you_title').value || 'Terima Kasih!';
            const msg = document.getElementById('setting_thank_you_message').value || 'Jawaban Anda telah berhasil kami terima.';
            document.getElementById('previewThankYouTitle').innerText = title;
            document.getElementById('previewThankYouMsg').innerText = msg;
        }

        // ==========================================
        // INTEGRATIONS MANAGEMENT & MODALS
        // ==========================================
        let integrationState = {
            google_sheets: { connected: false },
            webhooks: { connected: false }
        };

        async function loadIntegrationsStatus() {
            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations`, {
                    headers: { 'Accept': 'application/json' }
                });
                const res = await response.json();
                if (response.ok && res.status === 'success') {
                    integrationState = res.data;
                    updateIntegrationsUI();
                }
            } catch (err) {
                console.error('Failed to load integrations:', err);
            }
        }

        function updateIntegrationsUI() {
            // Google Sheets Card
            const sheets = integrationState.google_sheets || {};
            const badgeSheets = document.getElementById('badgeGoogleSheets');
            const cardSheetsDetail = document.getElementById('cardSheetsDetail');

            if (sheets.connected) {
                badgeSheets.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide bg-emerald-100 text-emerald-800';
                badgeSheets.innerText = 'Connected';
                cardSheetsDetail.innerText = 'ID: ' + (sheets.spreadsheet_id ? sheets.spreadsheet_id.substring(0, 12) + '...' : '');
            } else {
                badgeSheets.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide bg-slate-100 text-slate-600';
                badgeSheets.innerText = 'Not Connected';
                cardSheetsDetail.innerText = 'Belum terhubung';
            }

            // Webhook Card
            const webhook = integrationState.webhooks || {};
            const badgeWebhooks = document.getElementById('badgeWebhooks');
            const cardWebhookDetail = document.getElementById('cardWebhookDetail');

            if (webhook.connected) {
                badgeWebhooks.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide bg-purple-100 text-purple-800';
                badgeWebhooks.innerText = 'Connected';
                cardWebhookDetail.innerText = webhook.webhook_url || '';
            } else {
                badgeWebhooks.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide bg-slate-100 text-slate-600';
                badgeWebhooks.innerText = 'Not Connected';
                cardWebhookDetail.innerText = 'Belum terhubung';
            }
        }

        // Google Sheets Modal
        function openGoogleSheetsModal() {
            const sheets = integrationState.google_sheets || {};
            document.getElementById('modal_sheets_id').value = sheets.spreadsheet_id || '';
            document.getElementById('modal_sheets_name').value = sheets.sheet_name || 'Sheet1';
            document.getElementById('modal_sheets_auto_header').checked = sheets.auto_create_header ?? true;
            document.getElementById('modal_sheets_test_result').classList.add('hidden');

            const btnDisconnect = document.getElementById('btnDisconnectSheets');
            if (sheets.connected) {
                btnDisconnect.classList.remove('hidden');
            } else {
                btnDisconnect.classList.add('hidden');
            }

            // Google OAuth User State
            const isOAuthConnected = sheets.user_oauth_connected;
            const oauthCard = document.getElementById('sheetsOAuthCard');
            const connectedBox = document.getElementById('sheetsOAuthConnectedBox');
            const autoCreateBox = document.getElementById('boxAutoCreateSpreadsheet');

            if (isOAuthConnected) {
                oauthCard.classList.add('hidden');
                connectedBox.classList.remove('hidden');
                autoCreateBox.classList.remove('hidden');
                document.getElementById('userGoogleEmailDisplay').innerText = sheets.user_oauth_email || '{{ auth()->user()->email ?? "Akun Google Terhubung" }}';
            } else {
                oauthCard.classList.remove('hidden');
                connectedBox.classList.add('hidden');
                autoCreateBox.classList.add('hidden');
            }

            document.getElementById('modalGoogleSheets').classList.remove('hidden');
        }

        function closeGoogleSheetsModal() {
            document.getElementById('modalGoogleSheets').classList.add('hidden');
        }

        async function createAutoSpreadsheet() {
            const btn = document.getElementById('btnCreateAutoSheets');
            btn.disabled = true;
            btn.innerHTML = '<span>Membuat spreadsheet di Google Drive...</span>';

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations/google-sheets/create-auto`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        title: '{{ addslashes($form->title) }} (Tanggapan)'
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    alert('Berhasil! ' + data.message);
                    await loadIntegrationsStatus();
                    closeGoogleSheetsModal();
                    if (data.data && data.data.spreadsheet_url) {
                        if (confirm('Buka Google Sheets yang baru dibuat sekarang?')) {
                            window.open(data.data.spreadsheet_url, '_blank');
                        }
                    }
                } else {
                    alert('Gagal membuat spreadsheet otomatis: ' + (data.message || 'Error'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg><span>Buat Spreadsheet Otomatis di Google Drive</span>`;
            }
        }

        async function disconnectUserGoogleAccount() {
            const confirmed = await showCenterConfirm({
                title: 'Putuskan Akun Google?',
                message: 'Apakah Anda yakin ingin memutuskan otorisasi Google OAuth dari akun Anda?',
                confirmText: 'Putuskan'
            });
            if (!confirmed) return;

            try {
                const response = await fetch(`/api/dashboard/integrations/google-sheets/disconnect-user`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (response.ok) {
                    alert('Koneksi akun Google berhasil diputuskan.');
                    await loadIntegrationsStatus();
                    openGoogleSheetsModal();
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        }

        async function testGoogleSheetsConnection() {
            const spreadsheetId = document.getElementById('modal_sheets_id').value.trim();
            const sheetName = document.getElementById('modal_sheets_name').value.trim() || 'Sheet1';
            const resultBox = document.getElementById('modal_sheets_test_result');
            const btn = document.getElementById('btnTestSheets');

            if (!spreadsheetId) {
                resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-rose-50 text-rose-800 border border-rose-200';
                resultBox.innerText = 'Silakan isi Spreadsheet ID terlebih dahulu.';
                resultBox.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span>Menguji koneksi...</span>';

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations/google-sheets/test`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        spreadsheet_id: spreadsheetId,
                        sheet_name: sheetName
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-emerald-50 text-emerald-800 border border-emerald-200';
                    resultBox.innerHTML = `<strong>Koneksi Berhasil!</strong> ${data.message} <br>Kolom header ditemukan: ${data.header_columns_count}`;
                } else {
                    resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-rose-50 text-rose-800 border border-rose-200';
                    resultBox.innerHTML = `<strong>Uji Koneksi Gagal:</strong> ${data.message} ${data.detail ? `<br><span class="text-[10px] text-rose-600">${data.detail}</span>` : ''}`;
                }
                resultBox.classList.remove('hidden');
            } catch (err) {
                resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-rose-50 text-rose-800 border border-rose-200';
                resultBox.innerText = 'Terjadi kesalahan jaringan saat menguji koneksi.';
                resultBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span>Test Connection</span>`;
            }
        }

        async function saveGoogleSheetsIntegration() {
            const spreadsheetId = document.getElementById('modal_sheets_id').value.trim();
            const sheetName = document.getElementById('modal_sheets_name').value.trim() || 'Sheet1';
            const autoHeader = document.getElementById('modal_sheets_auto_header').checked;
            const btn = document.getElementById('btnSaveSheets');

            if (!spreadsheetId) {
                alert('Spreadsheet ID wajib diisi.');
                return;
            }

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations/google-sheets`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        spreadsheet_id: spreadsheetId,
                        sheet_name: sheetName,
                        auto_create_header: autoHeader
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    alert('Integrasi Google Sheets berhasil disimpan!');
                    await loadIntegrationsStatus();
                    closeGoogleSheetsModal();
                } else {
                    alert('Gagal menyimpan: ' + (data.message || 'Periksa kembali data'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Save & Connect';
            }
        }

        async function disconnectGoogleSheets() {
            const confirmed = await showCenterConfirm({
                title: 'Putuskan Google Sheets?',
                message: 'Apakah Anda yakin ingin memutuskan integrasi Google Sheets? Submission baru tidak akan lagi dikirim ke spreadsheet ini.',
                confirmText: 'Putuskan'
            });
            if (!confirmed) return;

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations/google-sheets`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    alert(data.message);
                    await loadIntegrationsStatus();
                    closeGoogleSheetsModal();
                } else {
                    alert('Gagal memutuskan integrasi: ' + (data.message || 'Error'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        }

        // Webhook Modal
        function openWebhooksModal() {
            const webhook = integrationState.webhooks || {};
            document.getElementById('modal_webhook_url').value = webhook.webhook_url || '';
            document.getElementById('modal_webhook_secret').value = webhook.webhook_secret || '';
            document.getElementById('modal_webhook_test_result').classList.add('hidden');

            const btnDisconnect = document.getElementById('btnDisconnectWebhook');
            if (webhook.connected) {
                btnDisconnect.classList.remove('hidden');
            } else {
                btnDisconnect.classList.add('hidden');
            }

            document.getElementById('modalWebhooks').classList.remove('hidden');
        }

        function closeWebhooksModal() {
            document.getElementById('modalWebhooks').classList.add('hidden');
        }

        async function testWebhooksConnection() {
            const url = document.getElementById('modal_webhook_url').value.trim();
            const secret = document.getElementById('modal_webhook_secret').value.trim();
            const resultBox = document.getElementById('modal_webhook_test_result');
            const btn = document.getElementById('btnTestWebhook');

            if (!url) {
                resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-rose-50 text-rose-800 border border-rose-200';
                resultBox.innerText = 'Silakan isi Webhook Target URL terlebih dahulu.';
                resultBox.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span>Mengirim ping webhook...</span>';

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations/webhooks/test`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        webhook_url: url,
                        webhook_secret: secret
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-emerald-50 text-emerald-800 border border-emerald-200';
                    resultBox.innerHTML = `<strong>Ping Berhasil! (HTTP ${data.http_status})</strong><br>Latency: ${data.latency_ms} ms<br>Signature: ${data.signature_sent || 'None'}`;
                } else {
                    resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-rose-50 text-rose-800 border border-rose-200';
                    resultBox.innerHTML = `<strong>Ping Webhook Gagal:</strong> ${data.message || 'HTTP ' + data.http_status}`;
                }
                resultBox.classList.remove('hidden');
            } catch (err) {
                resultBox.className = 'mt-2 p-3 rounded-lg text-[11px] leading-tight bg-rose-50 text-rose-800 border border-rose-200';
                resultBox.innerText = 'Terjadi kesalahan jaringan saat menghubungi webhook target.';
                resultBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg><span>Send Test Webhook</span>`;
            }
        }

        async function saveWebhooksIntegration() {
            const url = document.getElementById('modal_webhook_url').value.trim();
            const secret = document.getElementById('modal_webhook_secret').value.trim();
            const btn = document.getElementById('btnSaveWebhook');

            if (!url) {
                alert('Webhook Target URL wajib diisi.');
                return;
            }

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations/webhooks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        webhook_url: url,
                        webhook_secret: secret
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    alert('Integrasi Webhook berhasil disimpan!');
                    await loadIntegrationsStatus();
                    closeWebhooksModal();
                } else {
                    alert('Gagal menyimpan: ' + (data.message || 'Periksa kembali data'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Save & Connect';
            }
        }

        async function disconnectWebhooks() {
            const confirmed = await showCenterConfirm({
                title: 'Putuskan Webhook?',
                message: 'Apakah Anda yakin ingin memutuskan integrasi Webhook? Submission baru tidak akan lagi dikirim ke URL ini.',
                confirmText: 'Putuskan'
            });
            if (!confirmed) return;

            try {
                const response = await fetch(`/api/dashboard/forms/${formId}/integrations/webhooks`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    alert(data.message);
                    await loadIntegrationsStatus();
                    closeWebhooksModal();
                } else {
                    alert('Gagal memutuskan integrasi: ' + (data.message || 'Error'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        }

        // ==========================================
        // CENTERED CONFIRMATION POPUP SYSTEM
        // ==========================================
        let genericConfirmCallback = null;

        function showCenterConfirm({ title, message, confirmText = 'Ya, Lanjutkan', isDanger = true }) {
            return new Promise((resolve) => {
                genericConfirmCallback = resolve;
                const modal = document.getElementById('genericConfirmModal');
                const card = document.getElementById('genericModalCard');
                document.getElementById('genericModalTitle').textContent = title;
                document.getElementById('genericModalMessage').textContent = message;
                const submitBtn = document.getElementById('btnGenericConfirmSubmit');
                submitBtn.textContent = confirmText;
                submitBtn.className = isDanger
                    ? 'w-1/2 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-md shadow-rose-600/20'
                    : 'w-1/2 py-2.5 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs transition shadow-md shadow-orange-500/20';

                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    modal.classList.remove('opacity-0');
                    card.classList.remove('scale-95');
                    card.classList.add('scale-100');
                });
            });
        }

        function closeGenericConfirm(result) {
            const modal = document.getElementById('genericConfirmModal');
            const card = document.getElementById('genericModalCard');
            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                if (genericConfirmCallback) {
                    genericConfirmCallback(result);
                    genericConfirmCallback = null;
                }
            }, 200);
        }

        document.getElementById('genericConfirmModal')?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) closeGenericConfirm(false);
        });

        // ==========================================
        // TAB SWITCHING (BUILD / SETTINGS / PUBLISH)
        // ==========================================
        function switchTab(tab) {
            document.getElementById('viewBuild').classList.toggle('hidden', tab !== 'build');
            document.getElementById('viewSettings').classList.toggle('hidden', tab !== 'settings');
            document.getElementById('viewPublish').classList.toggle('hidden', tab !== 'publish');

            // Floating Add button only visible on build tab
            document.getElementById('btnToggleLeftDrawer').style.display = tab === 'build' ? 'flex' : 'none';

            // Close drawers when leaving build
            if (tab !== 'build') {
                toggleLeftDrawer(false);
                toggleRightDrawer(false);
            }

            // Tab styles
            ['Build', 'Settings', 'Publish'].forEach(t => {
                const btn = document.getElementById('tabBtn' + t);
                if (t.toLowerCase() === tab) {
                    btn.className = 'px-5 h-full flex items-center gap-2 text-xs font-bold uppercase tracking-wider border-b-4 border-white text-white transition';
                } else {
                    btn.className = 'px-5 h-full flex items-center gap-2 text-xs font-bold uppercase tracking-wider border-b-4 border-transparent text-white/80 hover:text-white transition';
                }
            });

            if (tab === 'settings') {
                loadIntegrationsStatus();
            }
        }

        // ==========================================
        // PREVIEW MODE TOGGLE & DEVICE SWITCHER
        // ==========================================
        function togglePreviewMode(enable) {
            const previewToggle = document.getElementById('previewToggle');
            if (previewToggle) previewToggle.checked = enable;

            const previewContainer = document.getElementById('previewContainer');
            const previewIframe = document.getElementById('previewIframe');
            const viewBuild = document.getElementById('viewBuild');

            if (enable) {
                saveFormFields(false);
                const previewUrl = `/f/${formSlug}?t=` + Date.now();
                if (previewIframe) previewIframe.src = previewUrl;
                
                const newTabBtn = document.getElementById('btnOpenNewTabPreview');
                if (newTabBtn) newTabBtn.href = `/f/${formSlug}`;

                if (previewContainer) previewContainer.classList.remove('hidden');
                if (viewBuild) viewBuild.classList.add('hidden');
                toggleLeftDrawer(false);
                toggleRightDrawer(false);
                setPreviewDevice('desktop');
            } else {
                if (previewContainer) previewContainer.classList.add('hidden');
                if (viewBuild) viewBuild.classList.remove('hidden');
                if (previewIframe) previewIframe.src = '';
            }
        }

        function setPreviewDevice(device) {
            const iframe = document.getElementById('previewIframe');
            const btnDesktop = document.getElementById('prevBtnDesktop');
            const btnTablet = document.getElementById('prevBtnTablet');
            const btnMobile = document.getElementById('prevBtnMobile');

            [btnDesktop, btnTablet, btnMobile].forEach(btn => {
                if (btn) {
                    btn.classList.remove('bg-orange-500', 'text-white');
                    btn.classList.add('text-slate-300');
                }
            });

            if (!iframe) return;

            if (device === 'mobile') {
                iframe.style.maxWidth = '420px';
                if (btnMobile) {
                    btnMobile.classList.remove('text-slate-300');
                    btnMobile.classList.add('bg-orange-500', 'text-white');
                }
            } else if (device === 'tablet') {
                iframe.style.maxWidth = '768px';
                if (btnTablet) {
                    btnTablet.classList.remove('text-slate-300');
                    btnTablet.classList.add('bg-orange-500', 'text-white');
                }
            } else {
                iframe.style.maxWidth = '100%';
                if (btnDesktop) {
                    btnDesktop.classList.remove('text-slate-300');
                    btnDesktop.classList.add('bg-orange-500', 'text-white');
                }
            }
        }

        // ==========================================
        // FORM DESIGNER & THEME CUSTOMIZER ENGINE
        // ==========================================
        let currentTheme = @json($theme);
        let themeSaveDebounceTimer = null;

        function toggleDesignerDrawer(forceState) {
            const drawer = document.getElementById('designerDrawer');
            if (!drawer) return;

            const isCurrentlyOpen = !drawer.classList.contains('translate-x-full');
            const targetState = (typeof forceState === 'boolean') ? forceState : !isCurrentlyOpen;

            if (targetState) {
                // Close right field properties drawer if open
                if (typeof toggleRightDrawer === 'function') {
                    const propDrawer = document.getElementById('rightDrawer');
                    if (propDrawer) propDrawer.classList.add('translate-x-full');
                }
                drawer.classList.remove('translate-x-full');
            } else {
                drawer.classList.add('translate-x-full');
            }
        }

        function switchDesignerTab(tab) {
            const tabs = ['colors', 'styles', 'fonts', 'logo'];
            tabs.forEach(t => {
                const btn = document.getElementById('desTab' + t.charAt(0).toUpperCase() + t.slice(1));
                const sec = document.getElementById('desSec' + t.charAt(0).toUpperCase() + t.slice(1));
                if (btn && sec) {
                    if (t === tab) {
                        btn.className = 'py-2.5 px-2 border-b-2 border-orange-500 text-orange-600 font-bold transition flex-1 text-center';
                        sec.classList.remove('hidden');
                    } else {
                        btn.className = 'py-2.5 px-2 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex-1 text-center';
                        sec.classList.add('hidden');
                    }
                }
            });
        }

        function setThemeProperty(key, value) {
            currentTheme[key] = value;

            // Sync inputs if applicable
            if (key === 'page_bg') {
                const input = document.getElementById('inputThemePageBg');
                if (input) input.value = value;
            } else if (key === 'primary_color') {
                const input = document.getElementById('inputThemePrimary');
                if (input) input.value = value;
            } else if (key === 'logo_url') {
                const input = document.getElementById('inputThemeLogoUrl');
                if (input) input.value = value;
            }

            applyThemeLivePreview(currentTheme);
            saveThemeSettings();
        }

        function applyThemeLivePreview(theme) {
            // 1. Workspace canvas background
            const workspace = document.getElementById('workspaceCanvas');
            if (workspace && theme.page_bg) {
                workspace.style.backgroundColor = theme.page_bg;
            }

            // 2. Card width
            const viewBuild = document.getElementById('viewBuild');
            if (viewBuild) {
                viewBuild.className = `w-full ${theme.card_width || 'max-w-2xl'} flex flex-col items-center transition-all duration-300`;
            }

            // 3. Card styling (bg, radius, font)
            const card = document.getElementById('canvasCard');
            if (card) {
                card.style.backgroundColor = theme.card_bg || '#ffffff';
                card.style.fontFamily = `'${theme.font_family || 'Plus Jakarta Sans'}', sans-serif`;
                card.classList.remove('rounded-none', 'rounded-xl', 'rounded-2xl', 'rounded-3xl');
                card.classList.add(theme.card_radius || 'rounded-2xl');
            }

            // 4. Primary button styling
            const submitBtn = document.getElementById('canvasSubmitBtn');
            if (submitBtn) {
                if (theme.primary_color) submitBtn.style.backgroundColor = theme.primary_color;
                submitBtn.classList.remove('rounded-none', 'rounded-xl', 'rounded-2xl', 'rounded-3xl');
                submitBtn.classList.add(theme.card_radius || 'rounded-2xl');
            }

            // 5. Header Logo styling & positioning
            const headerAlign = document.getElementById('canvasHeaderAlign');
            if (headerAlign && theme.logo_align) {
                headerAlign.className = 'text-' + theme.logo_align;
            }

            const logoImgWrapper = document.getElementById('canvasLogoImgWrapper');
            const logoImg = document.getElementById('canvasLogoImg');
            const btnAddLogo = document.getElementById('btnCanvasAddLogo');

            if (theme.logo_url && theme.logo_url.trim() !== '') {
                if (logoImg) {
                    logoImg.src = theme.logo_url;
                    logoImg.style.height = (theme.logo_height || '56') + 'px';
                }
                if (logoImgWrapper) logoImgWrapper.classList.remove('hidden');
                if (btnAddLogo) btnAddLogo.classList.add('hidden');
            } else {
                if (logoImgWrapper) logoImgWrapper.classList.add('hidden');
                if (btnAddLogo) btnAddLogo.classList.remove('hidden');
            }
        }

        function saveThemeSettings() {
            clearTimeout(themeSaveDebounceTimer);
            const statusText = document.getElementById('saveStatusText');
            if (statusText) statusText.innerText = 'Menyimpan...';

            themeSaveDebounceTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/dashboard/forms/${formId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            settings: {
                                theme: currentTheme
                            }
                        })
                    });

                    if (response.ok) {
                        if (statusText) statusText.innerText = 'Tersimpan';
                    }
                } catch (err) {
                    console.error('Error saving theme settings:', err);
                }
            }, 500);
        }

        function openDesignerLogoTab() {
            toggleDesignerDrawer(true);
            switchDesignerTab('logo');
        }

        // ==========================================
        // UTILS
        // ==========================================
        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function copyPublishUrl() {
            const copyInput = document.getElementById('publishUrlInput');
            copyInput.select();
            navigator.clipboard.writeText(copyInput.value);
            const btnText = document.getElementById('btnCopyText');
            btnText.innerText = 'Disalin!';
            setTimeout(() => btnText.innerText = 'Salin Tautan', 2000);
        }

        // Auto-switch tab & open modal if redirected from OAuth consent
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'settings') {
                switchTab('settings');
                if (urlParams.get('integration') === 'sheets') {
                    setTimeout(() => {
                        openGoogleSheetsModal();
                    }, 400);
                }
            }
        });
    </script>
</body>
</html>
