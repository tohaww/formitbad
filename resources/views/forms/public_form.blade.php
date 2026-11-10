<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} | {{ config('app.name', 'CustomFormBuilder') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- TomSelect Autocomplete Library -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        html, body {
            overflow-y: auto !important;
            height: auto !important;
            min-height: 100% !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ts-control {
            border-radius: 0.5rem !important;
            padding: 0.625rem 0.875rem !important;
            border-color: #cbd5e1 !important;
            font-size: 0.875rem !important;
        }
        .ts-control.focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2) !important;
        }
        .ts-dropdown {
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border-color: #e2e8f0 !important;
            overflow: hidden !important;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-10 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header Card (Jotform Visual Style) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="h-3 bg-gradient-to-r from-orange-500 via-amber-500 to-emerald-500"></div>
            <div class="p-8">
                <span class="inline-block px-3 py-1 bg-orange-50 text-orange-700 text-xs font-bold rounded-full mb-3 uppercase tracking-wider">
                    Formulir Resmi
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">{{ $form->title }}</h1>
                @if($form->description)
                    <p class="text-slate-600 leading-relaxed text-sm">{{ $form->description }}</p>
                @endif
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs text-rose-600 font-semibold">
                    <span>* Menunjukkan pertanyaan yang wajib diisi</span>
                </div>
            </div>
        </div>

        <!-- Submission Status Alert -->
        <div id="alertBox" class="hidden rounded-xl p-5 mb-6 text-sm"></div>

        <!-- Dynamic Form -->
        <form id="publicForm" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @foreach($form->fields as $field)
                @php
                    $fieldKey = \Illuminate\Support\Str::slug($field->label, '_');
                    $cfg = $field->config ?: [];
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 transition-all hover:border-slate-300">
                    <label for="{{ $fieldKey }}" class="block font-bold text-slate-800 text-sm mb-1">
                        {{ $field->label }}
                        @if($field->is_required)
                            <span class="text-rose-500 font-bold">*</span>
                        @endif
                    </label>

                    @switch($field->field_type->value)
                        @case('textarea')
                            <textarea
                                id="{{ $fieldKey }}"
                                name="{{ $fieldKey }}"
                                rows="{{ $cfg['rows'] ?? 3 }}"
                                placeholder="{{ $field->placeholder }}"
                                class="w-full mt-2 px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-slate-900 placeholder:text-slate-400 text-sm outline-none transition"
                                {{ $field->is_required ? 'required' : '' }}
                            ></textarea>
                            @break

                        @case('select')
                            <select
                                id="{{ $fieldKey }}"
                                name="{{ $fieldKey }}"
                                class="w-full mt-2 px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-slate-900 text-sm outline-none transition bg-white"
                                {{ $field->is_required ? 'required' : '' }}
                            >
                                <option value="">{{ $field->placeholder ?: '-- Pilih Opsi --' }}</option>
                                @foreach($cfg['options'] ?? [] as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('radio')
                            <div class="mt-3 space-y-2.5">
                                @foreach($cfg['options'] ?? [] as $opt)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input
                                            type="radio"
                                            name="{{ $fieldKey }}"
                                            value="{{ $opt }}"
                                            class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-slate-300"
                                            {{ $field->is_required ? 'required' : '' }}
                                        >
                                        <span class="text-sm text-slate-700 group-hover:text-slate-900 font-medium">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @break

                        @case('checkbox')
                            <div class="mt-3 space-y-2.5">
                                @foreach($cfg['options'] ?? [] as $opt)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            name="{{ $fieldKey }}[]"
                                            value="{{ $opt }}"
                                            class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 border-slate-300"
                                        >
                                        <span class="text-sm text-slate-700 group-hover:text-slate-900 font-medium">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @break

                        @case('rating')
                            @php $scale = $cfg['scale'] ?? 5; @endphp
                            <div class="mt-3 flex items-center gap-3">
                                @if(!empty($cfg['min_label']))
                                    <span class="text-xs text-slate-400 font-medium">{{ $cfg['min_label'] }}</span>
                                @endif
                                <div class="flex items-center gap-2">
                                    @for($i = 1; $i <= $scale; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="{{ $fieldKey }}" value="{{ $i }}" class="sr-only peer" {{ $field->is_required ? 'required' : '' }}>
                                            <div class="w-10 h-10 rounded-lg border border-slate-200 peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 flex items-center justify-center font-bold text-slate-700 text-sm hover:border-slate-300 transition">
                                                {{ $i }}
                                            </div>
                                        </label>
                                    @endfor
                                </div>
                                @if(!empty($cfg['max_label']))
                                    <span class="text-xs text-slate-400 font-medium">{{ $cfg['max_label'] }}</span>
                                @endif
                            </div>
                            @break

                        @case('file')
                            <input
                                type="file"
                                id="{{ $fieldKey }}"
                                name="{{ $fieldKey }}"
                                class="w-full mt-2 text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer"
                                {{ $field->is_required ? 'required' : '' }}
                            >
                            @break

                        @case('remote_data_dropdown')
                            <div class="mt-2">
                                <select
                                    id="{{ $fieldKey }}"
                                    name="{{ $fieldKey }}"
                                    class="tom-select-remote w-full"
                                    data-field-id="{{ $field->id }}"
                                    data-placeholder="{{ $field->placeholder ?: 'Ketik untuk mencari data dari Google Sheets...' }}"
                                    data-min-chars="{{ $cfg['min_search_chars'] ?? 2 }}"
                                    {{ $field->is_required ? 'required' : '' }}
                                >
                                    <option value="">{{ $field->placeholder ?: 'Ketik untuk mencari data...' }}</option>
                                </select>
                            </div>
                            @break

                        @case('remote_data_dropdown')
                            <div class="mt-2">
                                <select
                                    id="{{ $fieldKey }}"
                                    name="{{ $fieldKey }}"
                                    class="tom-select-remote w-full"
                                    data-field-id="{{ $field->id }}"
                                    data-placeholder="{{ $field->placeholder ?: 'Ketik untuk mencari data dari Google Sheets...' }}"
                                    data-min-chars="{{ $cfg['min_search_chars'] ?? 2 }}"
                                    {{ $field->is_required ? 'required' : '' }}
                                >
                                    <option value="">{{ $field->placeholder ?: 'Ketik untuk mencari data...' }}</option>
                                </select>
                            </div>
                            @break

                        @default
                            <input
                                type="{{ $field->field_type->value === 'number' ? 'number' : ($field->field_type->value === 'email' ? 'email' : ($field->field_type->value === 'date' ? 'date' : 'text')) }}"
                                id="{{ $fieldKey }}"
                                name="{{ $fieldKey }}"
                                placeholder="{{ $field->placeholder }}"
                                class="w-full mt-2 px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-slate-900 placeholder:text-slate-400 text-sm outline-none transition"
                                {{ $field->is_required ? 'required' : '' }}
                            >
                    @endswitch

                    <div id="err_{{ $fieldKey }}" class="text-rose-600 text-xs mt-1.5 hidden font-medium"></div>
                </div>
            @endforeach

            <!-- Action Buttons with Double-Submit Protection -->
            <div class="flex items-center justify-between pt-4">
                <button
                    type="submit"
                    id="submitBtn"
                    class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-sm hover:shadow transition flex items-center gap-2.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="btnText">Kirim Jawaban</span>
                    <svg id="btnSpinner" class="hidden animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>

                <span class="text-xs text-slate-400 font-medium">Didukung oleh {{ config('app.name', 'CustomFormBuilder') }}</span>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('publicForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const alertBox = document.getElementById('alertBox');

        // ==========================================
        // 1. INITIALIZE TOMSELECT FOR REMOTE DROPDOWNS
        // ==========================================
        document.querySelectorAll('.tom-select-remote').forEach(selectEl => {
            const fieldId = selectEl.dataset.fieldId;
            const placeholder = selectEl.dataset.placeholder;

            new TomSelect(selectEl, {
                valueField: 'value',
                labelField: 'label',
                searchField: ['label', 'subtext'],
                placeholder: placeholder,
                maxItems: 1,
                loadThrottle: 300,
                preload: 'focus', // Load initial options when focused
                load: function(query, callback) {
                    const url = `/api/public/forms/{{ $form->slug }}/fields/${fieldId}/remote-options?q=${encodeURIComponent(query)}&limit=20`;
                    fetch(url)
                        .then(res => res.json())
                        .then(json => {
                            if (json.status === 'success' && json.data) {
                                callback(json.data);
                            } else {
                                callback();
                            }
                        })
                        .catch(() => callback());
                },
                render: {
                    option: function(item, escape) {
                        return `<div class="py-2 px-3 hover:bg-orange-50 transition cursor-pointer">
                            <div class="font-semibold text-slate-900 text-sm">${escape(item.label)}</div>
                            ${item.subtext ? `<div class="text-xs text-slate-400 mt-0.5">${escape(item.subtext)}</div>` : ''}
                        </div>`;
                    },
                    item: function(item, escape) {
                        return `<div class="font-semibold text-slate-800 text-sm">${escape(item.label)}</div>`;
                    },
                    no_results: function() {
                        return `<div class="py-2.5 px-3 text-xs text-slate-400">Tidak ada data ditemukan di spreadsheet.</div>`;
                    },
                    loading: function() {
                        return `<div class="py-2.5 px-3 text-xs text-orange-600 font-semibold flex items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Memuat data Google Sheets...
                        </div>`;
                    }
                }
            });
        });

        // ==========================================
        // 2. DOUBLE-SUBMIT PROTECTION & AJAX SUBMIT
        // ==========================================
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Clear previous error messages
            document.querySelectorAll('[id^="err_"]').forEach(el => {
                el.innerText = '';
                el.classList.add('hidden');
            });
            alertBox.classList.add('hidden');

            // DOUBLE-SUBMIT PROTECTION: Disable button & show spinner
            submitBtn.disabled = true;
            btnText.innerText = 'Mengirimkan Jawaban...';
            btnSpinner.classList.remove('hidden');

            const formData = new FormData(form);

            try {
                const response = await fetch('/api/public/forms/{{ $form->slug }}/submit', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    @php
                        $thankYouTitle = $form->settings['thank_you_title'] ?? 'Formulir Berhasil Terkirim!';
                        $thankYouMsg = $form->settings['thank_you_message'] ?? 'Terima kasih telah meluangkan waktu untuk mengisi formulir ini.';
                    @endphp
                    alertBox.className = 'rounded-xl p-6 mb-6 text-sm bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-xs';
                    alertBox.innerHTML = `
                        <div class="flex items-center gap-2.5 font-bold text-base mb-1.5 text-emerald-800">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ addslashes($thankYouTitle) }}
                        </div>
                        <p class="text-emerald-700 mb-2">{{ addslashes($thankYouMsg) }}</p>
                        <p class="text-emerald-700 text-xs">ID Pengiriman: <code class="font-mono text-xs bg-emerald-100 px-2 py-0.5 rounded font-bold">${data.submission_id}</code></p>
                    `;
                    alertBox.classList.remove('hidden');
                    form.reset();
                    form.classList.add('hidden');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alertBox.className = 'rounded-xl p-5 mb-6 text-sm bg-rose-50 border border-rose-200 text-rose-800';
                    alertBox.innerHTML = `<strong>Pengiriman gagal:</strong> ${data.message || 'Mohon periksa kembali input formulir Anda.'}`;
                    alertBox.classList.remove('hidden');

                    if (data.errors) {
                        for (const [key, msgs] of Object.entries(data.errors)) {
                            const errEl = document.getElementById('err_' + key);
                            if (errEl) {
                                errEl.innerText = msgs.join(', ');
                                errEl.classList.remove('hidden');
                            }
                        }
                    }
                }
            } catch (err) {
                alertBox.className = 'rounded-xl p-5 mb-6 text-sm bg-rose-50 border border-rose-200 text-rose-800';
                alertBox.innerText = 'Terjadi kesalahan jaringan atau server saat mengirimkan formulir. Silakan coba kembali.';
                alertBox.classList.remove('hidden');
            } finally {
                // Re-enable button
                submitBtn.disabled = false;
                btnText.innerText = 'Kirim Jawaban';
                btnSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
