/**
 * Extensible Widget Registry for CustomFormBuilder (Jotform-style Workspace)
 *
 * Each element defines:
 * - type: Unique identifier corresponding to FieldType enum
 * - label: Human readable name
 * - category: 'basic' | 'widgets'
 * - icon: SVG markup
 * - badge: Optional badge tag (e.g. 'Google Sheets')
 * - defaultProps: Default values for label, placeholder, is_required, config
 * - configSchema: Fields shown in the Right Properties Drawer
 * - renderCanvas(field): HTML representation on the white paper form canvas
 */
window.WidgetRegistry = (function () {
    const registry = {};

    function register(definition) {
        if (!definition.type) {
            throw new Error("Widget definition must include a 'type' property.");
        }
        registry[definition.type] = definition;
    }

    function get(type) {
        return registry[type] || registry['text'];
    }

    function getAll() {
        return Object.values(registry);
    }

    function getByCategory(category) {
        return Object.values(registry).filter(item => item.category === category);
    }

    // ==========================================
    // 1. BASIC ELEMENTS
    // ==========================================

    register({
        type: 'text',
        label: 'Short Text',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7"/></svg>`,
        defaultProps: {
            label: 'Teks Singkat',
            placeholder: 'Masukkan jawaban Anda...',
            is_required: false,
            config: { max_length: 255 }
        },
        configSchema: [
            { key: 'max_length', label: 'Maksimal Karakter', type: 'number', default: 255 }
        ],
        renderCanvas: (field) => `
            <input type="text" disabled placeholder="${field.placeholder || 'Ketik teks singkat...'}" class="w-full mt-2 px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
        `
    });

    register({
        type: 'textarea',
        label: 'Long Text',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h10"/></svg>`,
        defaultProps: {
            label: 'Teks Panjang',
            placeholder: 'Tuliskan deskripsi lengkap atau keterangan...',
            is_required: false,
            config: { rows: 4 }
        },
        configSchema: [
            { key: 'rows', label: 'Jumlah Baris (Tinggi)', type: 'number', default: 4 }
        ],
        renderCanvas: (field) => `
            <textarea disabled rows="${field.config?.rows || 3}" placeholder="${field.placeholder || 'Tuliskan teks panjang...'}" class="w-full mt-2 px-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed"></textarea>
        `
    });

    register({
        type: 'email',
        label: 'Email',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`,
        defaultProps: {
            label: 'Alamat Email',
            placeholder: 'contoh@domain.com',
            is_required: true,
            config: { domain_restriction: '' }
        },
        configSchema: [
            { key: 'domain_restriction', label: 'Batasi Domain Email (Opsional)', type: 'text', placeholder: 'itbad.ac.id', hint: 'Hanya izinkan email berakhiran domain ini' }
        ],
        renderCanvas: (field) => `
            <div class="relative mt-2">
                <input type="email" disabled placeholder="${field.placeholder || 'nama@domain.com'}" class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                <span class="absolute left-3 top-3 text-slate-400">@</span>
            </div>
        `
    });

    register({
        type: 'number',
        label: 'Number',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>`,
        defaultProps: {
            label: 'Angka / Nilai Numerik',
            placeholder: '0',
            is_required: false,
            config: { min: null, max: null }
        },
        configSchema: [
            { key: 'min', label: 'Nilai Minimum', type: 'number', default: null },
            { key: 'max', label: 'Nilai Maksimum', type: 'number', default: null }
        ],
        renderCanvas: (field) => `
            <input type="number" disabled placeholder="${field.placeholder || '0'}" class="w-full mt-2 px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
        `
    });

    register({
        type: 'phone',
        label: 'Phone Number',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>`,
        defaultProps: {
            label: 'Nomor WhatsApp / Telepon',
            placeholder: '081234567890',
            is_required: true,
            config: { country_code: '+62' }
        },
        configSchema: [
            { key: 'country_code', label: 'Kode Negara', type: 'text', default: '+62' }
        ],
        renderCanvas: (field) => `
            <input type="tel" disabled placeholder="${field.placeholder || '081234567890'}" class="w-full mt-2 px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
        `
    });

    register({
        type: 'select',
        label: 'Dropdown',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>`,
        defaultProps: {
            label: 'Pilihan Dropdown',
            placeholder: '-- Pilih Opsi --',
            is_required: false,
            config: { options: ['Opsi 1', 'Opsi 2', 'Opsi 3'] }
        },
        configSchema: [
            { key: 'options', label: 'Daftar Opsi (Pisahkan dengan baris baru)', type: 'textarea_list', default: ['Opsi 1', 'Opsi 2', 'Opsi 3'] }
        ],
        renderCanvas: (field) => `
            <select disabled class="w-full mt-2 px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                <option>${field.placeholder || '-- Pilih Opsi --'}</option>
                ${(field.config?.options || ['Opsi 1', 'Opsi 2']).map(opt => `<option>${opt}</option>`).join('')}
            </select>
        `
    });

    register({
        type: 'radio',
        label: 'Single Choice',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="currentColor"/></svg>`,
        defaultProps: {
            label: 'Pilihan Tunggal',
            is_required: false,
            config: { options: ['Pilihan A', 'Pilihan B', 'Pilihan C'] }
        },
        configSchema: [
            { key: 'options', label: 'Daftar Pilihan (Pisahkan dengan baris baru)', type: 'textarea_list', default: ['Pilihan A', 'Pilihan B', 'Pilihan C'] }
        ],
        renderCanvas: (field) => `
            <div class="mt-2.5 space-y-2">
                ${(field.config?.options || ['Pilihan A', 'Pilihan B']).map(opt => `
                    <label class="flex items-center gap-2.5 text-sm text-slate-600">
                        <input type="radio" disabled class="w-4 h-4 text-orange-500">
                        <span>${opt}</span>
                    </label>
                `).join('')}
            </div>
        `
    });

    register({
        type: 'checkbox',
        label: 'Multiple Choice',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        defaultProps: {
            label: 'Pilihan Ganda',
            is_required: false,
            config: { options: ['Centang 1', 'Centang 2', 'Centang 3'] }
        },
        configSchema: [
            { key: 'options', label: 'Daftar Opsi (Pisahkan dengan baris baru)', type: 'textarea_list', default: ['Centang 1', 'Centang 2', 'Centang 3'] }
        ],
        renderCanvas: (field) => `
            <div class="mt-2.5 space-y-2">
                ${(field.config?.options || ['Centang 1', 'Centang 2']).map(opt => `
                    <label class="flex items-center gap-2.5 text-sm text-slate-600">
                        <input type="checkbox" disabled class="w-4 h-4 rounded text-orange-500">
                        <span>${opt}</span>
                    </label>
                `).join('')}
            </div>
        `
    });

    register({
        type: 'date',
        label: 'Date Picker',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
        defaultProps: {
            label: 'Pilih Tanggal',
            is_required: false,
            config: {}
        },
        configSchema: [],
        renderCanvas: (field) => `
            <input type="date" disabled class="w-full mt-2 px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
        `
    });

    register({
        type: 'file',
        label: 'File Upload',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>`,
        defaultProps: {
            label: 'Unggah Berkas / Dokumen',
            is_required: false,
            config: { max_size_mb: 10 }
        },
        configSchema: [
            { key: 'max_size_mb', label: 'Batas Ukuran Maksimal (MB)', type: 'number', default: 10 }
        ],
        renderCanvas: (field) => `
            <div class="mt-2 border-2 border-dashed border-slate-200 rounded-lg p-5 text-center bg-slate-50">
                <svg class="mx-auto h-7 w-7 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span class="text-xs text-slate-500 font-medium">Klik atau geser file ke area ini (Maks. ${field.config?.max_size_mb || 10} MB)</span>
            </div>
        `
    });

    register({
        type: 'rating',
        label: 'Star Rating',
        category: 'basic',
        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>`,
        defaultProps: {
            label: 'Tingkat Kepuasan / Rating',
            is_required: false,
            config: { scale: 5, min_label: 'Sangat Tidak Puas', max_label: 'Sangat Puas' }
        },
        configSchema: [
            { key: 'scale', label: 'Skala Nilai (Default: 5)', type: 'number', default: 5 },
            { key: 'min_label', label: 'Label Nilai Terendah', type: 'text', default: 'Sangat Tidak Puas' },
            { key: 'max_label', label: 'Label Nilai Tertinggi', type: 'text', default: 'Sangat Puas' }
        ],
        renderCanvas: (field) => {
            const scale = field.config?.scale || 5;
            let stars = '';
            for (let i = 1; i <= scale; i++) {
                stars += `<div class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center font-bold text-slate-500 text-sm">${i}</div>`;
            }
            return `
                <div class="mt-2.5 flex items-center gap-2">
                    ${stars}
                </div>
            `;
        }
    });

    // ==========================================
    // 2. WIDGETS (Extensible Plugins)
    // ==========================================

    register({
        type: 'remote_data_dropdown',
        label: 'Remote Data Dropdown',
        category: 'widgets',
        badge: 'Google Sheets',
        icon: `<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
        defaultProps: {
            label: 'Data Dropdown Terhubung (Google Sheets)',
            placeholder: 'Ketik untuk mencari data dari Spreadsheet...',
            is_required: false,
            config: {
                spreadsheet_id: '',
                sheet_name: 'Sheet1',
                range: 'A2:C',
                column_value: 'A',
                column_label: 'B',
                column_subtext: 'C',
                min_search_chars: 2
            }
        },
        configSchema: [
            {
                key: 'spreadsheet_id',
                label: 'Spreadsheet ID Sumber',
                type: 'text',
                placeholder: '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms',
                hint: 'ID unik spreadsheet Google (dapatkan dari URL di antara /d/ dan /edit)'
            },
            {
                key: 'sheet_name',
                label: 'Nama Tab Sheet',
                type: 'text',
                placeholder: 'Sheet1',
                default: 'Sheet1',
                hint: 'Nama tab sheet tempat data berada'
            },
            {
                key: 'range',
                label: 'Rentang Data (Range)',
                type: 'text',
                placeholder: 'A2:C',
                default: 'A2:C',
                hint: 'Misal A2:C untuk melewati baris header pertama'
            },
            {
                key: 'column_value',
                label: 'Kolom Value (Tersimpan)',
                type: 'text',
                placeholder: 'A',
                default: 'A',
                hint: 'Huruf kolom misal A (NIDN/NIM/ID)'
            },
            {
                key: 'column_label',
                label: 'Kolom Label (Tampilan Utama)',
                type: 'text',
                placeholder: 'B',
                default: 'B',
                hint: 'Huruf kolom misal B (Nama Lengkap / Judul)'
            },
            {
                key: 'column_subtext',
                label: 'Kolom Subtext (Keterangan Tambahan)',
                type: 'text',
                placeholder: 'C',
                default: 'C',
                hint: 'Huruf kolom misal C (Program Studi / Unit Kerja)'
            },
            {
                key: 'min_search_chars',
                label: 'Minimal Karakter Pencarian',
                type: 'number',
                placeholder: '2',
                default: 2,
                hint: 'Jumlah huruf minimal sebelum query request dikirim'
            }
        ],
        renderCanvas: (field) => `
            <div class="relative mt-2">
                <div class="w-full px-3.5 py-2.5 rounded-lg border border-emerald-300 bg-emerald-50/40 text-slate-600 text-sm flex items-center justify-between cursor-not-allowed">
                    <span class="flex items-center gap-2 text-slate-400">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        ${field.placeholder || 'Ketik untuk mencari data dari spreadsheet...'}
                    </span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded uppercase tracking-wider">
                        Google Sheets Live
                    </span>
                </div>
                ${field.config?.spreadsheet_id ? `
                    <div class="mt-1 text-[11px] text-slate-400 flex items-center gap-1">
                        <span>Sumber: Tab <strong>${field.config.sheet_name || 'Sheet1'}</strong> (${field.config.range || 'A2:C'})</span>
                    </div>
                ` : `
                    <div class="mt-1 text-[11px] text-amber-600 flex items-center gap-1 font-medium">
                        ⚠️ Klik ikon gear Properties untuk mengatur Spreadsheet ID & kolom data.
                    </div>
                `}
            </div>
        `
    });

    return {
        register,
        get,
        getAll,
        getByCategory
    };
})();

