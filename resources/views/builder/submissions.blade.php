<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respon Formulir — {{ $form->title }} | {{ config('app.name', 'CustomFormBuilder') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .jotform-orange { background-color: #ff6100; }
        .jotform-dark { background-color: #0a1551; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="jotform-dark text-white h-16 flex items-center justify-between px-6 shadow-md shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 text-xs font-bold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition" title="Kembali ke Beranda My Forms">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>My Forms</span>
            </a>
            <span class="text-white/20">|</span>
            <a href="{{ route('forms.edit', $form->id) }}" class="flex items-center gap-1.5 text-xs font-bold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition" title="Kembali ke Form Editor Workspace">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Form</span>
            </a>
            <span class="text-white/20">|</span>
            <div>
                <h1 class="font-bold text-sm text-white truncate max-w-xs md:max-w-md">{{ $form->title }}</h1>
                <div class="text-[11px] text-slate-400">Total: {{ $submissions->total() }} jawaban responden</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Export CSV Button -->
            <a href="/api/forms/{{ $form->id }}/export" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh CSV</span>
            </a>

            <!-- View Live Form -->
            <a href="/f/{{ $form->slug }}" target="_blank" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Form</span>
            </a>
        </div>
    </header>

    <!-- Main Table Container -->
    <main class="flex-1 p-6 md:p-8 flex flex-col max-w-[1600px] w-full mx-auto">
        <!-- Submissions Stats Ribbon -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
                <div class="text-xs text-slate-500 font-medium">Total Respon</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $submissions->total() }}</div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
                <div class="text-xs text-slate-500 font-medium">Tersinkron ke Google Sheets</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">
                    {{ $form->submissions()->where('sheet_synced', true)->count() }}
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
                <div class="text-xs text-slate-500 font-medium">Pending Sync Google Sheets</div>
                <div class="text-2xl font-black text-amber-500 mt-1">
                    {{ $form->submissions()->where('sheet_synced', false)->count() }}
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
                <div class="text-xs text-slate-500 font-medium">Webhook Terkirim</div>
                <div class="text-2xl font-black text-purple-600 mt-1">
                    {{ $form->submissions()->where('webhook_delivered', true)->count() }}
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col flex-1">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tabel Jawaban Responden</span>
                <span class="text-xs text-slate-400">Menampilkan {{ $submissions->count() }} data pada halaman ini</span>
            </div>

            @if($submissions->isEmpty())
                <div class="text-center py-16">
                    <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-slate-500 font-semibold text-sm">Belum ada respon yang dikirimkan.</p>
                    <p class="text-slate-400 text-xs mt-1">Bagikan tautan form untuk mulai mengumpulkan data jawaban.</p>
                </div>
            @else
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4 w-12">#</th>
                                <th class="py-3 px-4 min-w-[140px]">Waktu Submit</th>
                                <th class="py-3 px-4 min-w-[120px]">Google Sheets</th>
                                <th class="py-3 px-4 min-w-[120px]">Webhook</th>
                                @foreach($form->fields as $field)
                                    <th class="py-3 px-4 min-w-[180px]">{{ $field->label }}</th>
                                @endforeach
                                <th class="py-3 px-4 text-right min-w-[100px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($submissions as $idx => $sub)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                        {{ $sub->created_at->format('d M Y, H:i') }}
                                        <div class="text-[10px] text-slate-400 font-mono">{{ substr($sub->id, 0, 8) }}...</div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if($sub->sheet_synced)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Tersinkron
                                            </span>
                                        @else
                                            <button onclick="resyncSubmission('{{ $sub->id }}', 'sheet', this)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition cursor-pointer">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                Sync Ulang
                                            </button>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if(!$form->webhook_url)
                                            <span class="text-slate-400 text-[10px]">—</span>
                                        @elseif($sub->webhook_delivered)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Terkirim
                                            </span>
                                        @else
                                            <button onclick="resyncSubmission('{{ $sub->id }}', 'webhook', this)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition cursor-pointer">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                Kirim Ulang
                                            </button>
                                        @endif
                                    </td>
                                    @foreach($form->fields as $field)
                                        @php
                                            $key = \Illuminate\Support\Str::slug($field->label, '_');
                                            $val = $sub->payload[$key] ?? $sub->payload[$field->label] ?? '—';
                                        @endphp
                                        <td class="py-3 px-4 max-w-xs truncate" title="{{ is_array($val) ? json_encode($val) : $val }}">
                                            @if(is_array($val))
                                                @if(isset($val['url']))
                                                    <a href="{{ $val['url'] }}" target="_blank" class="text-indigo-600 hover:underline flex items-center gap-1 font-semibold">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        {{ $val['original_name'] ?? 'Lihat Berkas' }}
                                                    </a>
                                                @else
                                                    <span class="bg-slate-100 px-2 py-0.5 rounded text-[11px]">{{ implode(', ', $val) }}</span>
                                                @endif
                                            @else
                                                <span class="text-slate-800">{{ $val }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <button onclick='viewSubmissionDetails(@json($sub))' class="p-1.5 text-slate-400 hover:text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition" title="Lihat Detail JSON">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Detail JSON Modal -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full max-h-[85vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-sm text-slate-800">Detail Payload Respon</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <pre id="modalJsonContent" class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed"></pre>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                <button onclick="closeModal()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function viewSubmissionDetails(sub) {
            document.getElementById('modalJsonContent').innerText = JSON.stringify(sub, null, 2);
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        async function resyncSubmission(id, target, btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-3 w-3 text-current" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memproses...`;

            try {
                const res = await fetch(`/api/submissions/${id}/resync`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ target: target })
                });

                const data = await res.json();
                if (res.ok) {
                    alert(data.message || 'Sinkronisasi berhasil dijalankan.');
                    location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch(e) {
                alert('Gagal menghubungi server.');
            } finally {
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>

