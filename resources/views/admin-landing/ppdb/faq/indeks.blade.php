@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    @include('admin-landing._komponen._repeater-gaya')
    <style>
        /* === Kartu FAQ: pola collapse (accordion) === */
        .lp-faq-card { padding: 0; }
        .lp-faq-card .lp-rep-icon {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            color: #6d28d9;
        }
        .lp-faq-card.is-new .lp-rep-icon {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0369a1;
        }

        /* Header clickable + ringkasan + chevron */
        .lp-faq-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .85rem 1.1rem;
            border-bottom: 1px dashed #e2e8f0;
            background: #fafbfc;
            cursor: pointer;
            user-select: none;
            border-radius: .85rem .85rem 0 0;
        }
        .lp-faq-card.is-collapsed .lp-faq-head {
            border-bottom: none;
            border-radius: .85rem;
        }
        .lp-faq-card.is-open .lp-faq-head {
            border-radius: .85rem .85rem 0 0;
        }
        .lp-faq-head-title {
            font-weight: 700;
            font-size: .98rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
        }
        .lp-faq-head-key {
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .lp-faq-head-meta {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .35rem;
            margin-left: .5rem;
        }
        .lp-faq-head-meta .lp-faq-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .55rem;
            background: #f1f5f9;
            border-radius: 999px;
            font-size: .72rem;
            color: #334155;
            font-weight: 600;
        }
        .lp-faq-head-meta .lp-faq-pill .material-symbols-rounded { font-size: 13px; }
        .lp-faq-head-meta .lp-faq-pill.is-empty { color: #94a3b8; font-weight: 500; }

        .lp-faq-status { margin-left: auto; flex-shrink: 0; display: inline-flex; align-items: center; gap: .5rem; }

        .lp-faq-chev {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: inline-flex; align-items: center; justify-content: center;
            color: #475569;
            transition: transform .2s ease, background .15s ease;
        }
        .lp-faq-card.is-open .lp-faq-chev {
            transform: rotate(180deg);
            background: rgba(109, 40, 217, .12);
            color: #6d28d9;
            border-color: rgba(109, 40, 217, .25);
        }

        /* Body collapse */
        .lp-faq-body-wrap {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows .25s ease;
        }
        .lp-faq-card.is-open .lp-faq-body-wrap { grid-template-rows: 1fr; }
        .lp-faq-body-inner { overflow: hidden; }

        /* Pakai kelas .lp-rep-body dari shared (padding, grid 1fr/2fr, gap).
           Override khusus untuk FAQ: jawaban butuh lebar penuh. */
        .lp-faq-body .lp-faq-full { grid-column: 1 / -1; }

        /* Toolbar: padding bawah lebih tipis, atas tetap standar */
        .lp-faq-toolbar {
            padding-top: .85rem;
            padding-bottom: .2rem;
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $titleSlot = '<p class="text-muted small mb-0">Pertanyaan yang sering diajukan (FAQ) di halaman PPDB. Klik kartu untuk membuka form edit. Kartu aktif akan tampil di section FAQ halaman PPDB publik.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'titleSlot' => $titleSlot,
    ])

    <div id="lpFaqList" class="lp-rep-stack">
        @forelse ($items as $row)
            @php
                $rowIndex = $loop->iteration;
                $rowId = (int) $row->id;
                $qVal = $row->question ?? '';
                $aVal = $row->answer ?? '';
                $sortVal = (int) ($row->sort_order ?: 0);
                $pub = (bool) $row->is_published;
                $answerHtml = old('answer', $aVal);
                $answerPlain = trim(strip_tags((string) $answerHtml));
                $answerPlain = preg_replace('/\s+/', ' ', $answerPlain);
                $answerSnippet = $answerPlain !== ''
                    ? (mb_strlen($answerPlain) > 80 ? mb_substr($answerPlain, 0, 80) . '…' : $answerPlain)
                    : '';
            @endphp
            <div class="lp-rep-card lp-faq-card is-collapsed" data-id="{{ $rowId }}" data-row-index="{{ $rowIndex }}">
                <div class="lp-faq-head" data-role="toggle">
                    <div class="lp-rep-icon"><span class="material-symbols-rounded">quiz</span></div>
                    <div class="min-w-0">
                        <h6 class="lp-faq-head-title">{{ $qVal !== '' ? $qVal : 'FAQ #' . $rowIndex }}</h6>
                        <div class="lp-faq-head-key">
                            faq #{{ $rowIndex }}
                            @if ($rowId) · id {{ $rowId }} @endif
                        </div>
                    </div>
                    <div class="lp-faq-status">
                        <label class="lp-rep-toggle {{ $pub ? 'is-on' : 'is-off' }}" for="lp_faq_pub_{{ $rowId }}">
                            <input type="hidden" name="rows[{{ $rowIndex }}][is_published]" value="0">
                            <input type="checkbox" name="rows[{{ $rowIndex }}][is_published]" id="lp_faq_pub_{{ $rowId }}" value="1" data-role="publish" {{ $pub ? 'checked' : '' }}>
                            <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                            <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi {{ $pub ? 'bi-check-lg' : 'bi-x-lg' }}"></i></span>
                            <span class="lp-rep-toggle-text">{{ $pub ? 'Aktif' : 'Non-aktif' }}</span>
                        </label>
                        <span class="lp-faq-chev" aria-hidden="true">
                            <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                        </span>
                    </div>
                </div>

                <div class="lp-faq-body-wrap" data-role="body-wrap">
                    <div class="lp-faq-body-inner">
                        <div class="lp-rep-body lp-faq-body">
                            <div class="lp-faq-full input-group input-group-outline mb-0 @if ($qVal) is-filled @endif">
                                <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                                <input type="text" name="rows[{{ $rowIndex }}][question]" class="form-control" required maxlength="300" value="{{ $qVal }}">
                            </div>
                            <div class="lp-faq-full input-group input-group-outline mb-0 @if ($aVal !== '') is-filled @endif">
                                <label class="form-label">Jawaban <span class="text-danger">*</span></label>
                                <textarea name="rows[{{ $rowIndex }}][answer]" class="form-control" rows="4" required>{{ $aVal }}</textarea>
                                <input type="hidden" name="rows[{{ $rowIndex }}][sort_order]" value="{{ $sortVal }}">
                            </div>
                        </div>

                        <div class="lp-rep-foot">
                            <div class="lp-rep-foot-info">
                                <span class="material-symbols-rounded">info</span>
                                Status tampil di halaman publik ditentukan oleh toggle di header.
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @include('admin-landing._komponen.formulir-hapus', [
                                    'action' => route('app.admin-landing.ppdb.faqs.destroy', $rowId),
                                    'confirm' => 'Hapus FAQ ini?',
                                    'btnClass' => 'btn btn-sm btn-outline-danger',
                                    'icon' => 'delete',
                                    'label' => 'Hapus',
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="lp-rep-card lp-rep-empty" id="lpFaqEmpty">
                <span class="material-symbols-rounded">quiz</span>
                <div class="mt-2 mb-2">Belum ada FAQ.</div>
                <div class="small text-muted">Tambah baris pertama di bawah untuk mulai.</div>
            </div>
        @endforelse
    </div>

    @if ($items instanceof \Illuminate\Contracts\Pagination\Paginator && $items->hasPages())
        <div class="d-flex justify-content-end mt-2">{{ $items->links() }}</div>
    @endif

    <div class="lp-rep-toolbar mt-3 lp-faq-toolbar">
        <div class="lp-rep-toolbar-info">
            <span class="material-symbols-rounded" style="font-size:16px;">tips_and_updates</span>
            Tambah baris baru lalu klik <strong>Simpan Semua</strong> untuk mengirim perubahan.
        </div>
        <div class="lp-rep-toolbar-actions">
            <button type="button" class="btn btn-sm btn-outline-primary" id="lpFaqAddBtn">
                <span class="material-symbols-rounded" style="font-size:16px;">add</span>
                Tambah Baris
            </button>
            <button type="button" class="btn btn-sm btn-info" id="lpFaqSaveAll">
                <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                Simpan Semua
            </button>
        </div>
    </div>

    <template id="lpFaqRowTemplate">
        <div class="lp-rep-card lp-faq-card is-new is-collapsed" data-id="">
            <div class="lp-faq-head" data-role="toggle">
                <div class="lp-rep-icon"><span class="material-symbols-rounded">post_add</span></div>
                <div class="min-w-0">
                    <h6 class="lp-faq-head-title">FAQ Baru</h6>
                    <div class="lp-faq-head-key">faq baru · akan tersimpan sebagai draf</div>
                </div>
                <div class="lp-faq-head-meta">
                    <span class="lp-faq-pill is-empty">
                        <span class="material-symbols-rounded">chat_bubble</span>
                        Belum ada jawaban
                    </span>
                </div>
                <div class="lp-faq-status">
                    <label class="lp-rep-toggle is-on" for="lp_faq_pub_NEW">
                        <input type="hidden" name="rows[__INDEX__][is_published]" value="0">
                        <input type="checkbox" name="rows[__INDEX__][is_published]" id="lp_faq_pub_NEW" value="1" data-role="publish" checked>
                        <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                        <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                        <span class="lp-rep-toggle-text">Aktif</span>
                    </label>
                    <span class="lp-faq-chev" aria-hidden="true">
                        <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                    </span>
                </div>
            </div>

            <div class="lp-faq-body-wrap" data-role="body-wrap">
                <div class="lp-faq-body-inner">
                    <div class="lp-rep-body lp-faq-body">
                        <div class="lp-faq-full input-group input-group-outline mb-0">
                            <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" name="rows[__INDEX__][question]" class="form-control" required maxlength="300" value="">
                        </div>
                        <div class="lp-faq-full input-group input-group-outline mb-0">
                            <label class="form-label">Jawaban <span class="text-danger">*</span></label>
                            <textarea name="rows[__INDEX__][answer]" class="form-control" rows="4" required></textarea>
                            <input type="hidden" name="rows[__INDEX__][sort_order]" value="__SORT__">
                        </div>
                    </div>

                    <div class="lp-rep-foot">
                        <div class="lp-rep-foot-info">
                            <span class="material-symbols-rounded">edit_note</span>
                            Baris baru — belum tersimpan.
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-remove-row" data-role="remove">
                                <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                                Hapus Baris
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@section('script')
    @include('admin-landing._skrip')
    @include('admin-landing._komponen._repeater-skrip')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle collapse pada header (klik di area non-interaktif akan buka/tutup)
        function bindFaqToggle(card) {
            var head = card.querySelector('[data-role="toggle"]');
            if (!head || head.__lpBound) return;
            head.__lpBound = true;
            head.addEventListener('click', function (e) {
                // Abaikan klik pada input/button/elemen interaktif di header
                if (e.target.closest('input, button, label, select, textarea, a')) return;
                card.classList.toggle('is-collapsed');
                card.classList.toggle('is-open');
            });
        }

        // Inisialisasi: baris existing = tertutup
        document.querySelectorAll('#lpFaqList .lp-faq-card').forEach(function (card) {
            if (!card.classList.contains('is-open')) {
                card.classList.add('is-collapsed');
            }
            bindFaqToggle(card);
        });

        lpRep.init({
            listId: 'lpFaqList',
            addBtnId: 'lpFaqAddBtn',
            saveBtnId: 'lpFaqSaveAll',
            emptyId: 'lpFaqEmpty',
            templateId: 'lpFaqRowTemplate',
            storeUrl: @json(route('app.admin-landing.ppdb.faqs.store')),
            updateUrlTpl: @json(route('app.admin-landing.ppdb.faqs.update', ['item' => '__ID__'])),
            cardClass: 'lp-faq-card',
            removeBtnSelector: '[data-role="remove"], .btn-remove-row',
            wysiwyg: false,
            afterAppend: function (row) {
                // Baris baru tetap tertutup — admin harus klik header untuk membuka & mengedit.
                row.classList.add('is-collapsed');
                row.classList.remove('is-open');
                bindFaqToggle(row);
            },
            gatherPayload: function (row) {
                var q = row.querySelector('input[name*="[question]"]');
                if (!q || !q.value.trim()) return null;
                var a = row.querySelector('textarea[name*="[answer]"]');
                if (!a || !a.value.trim()) {
                    if (typeof Swal !== 'undefined' && Swal.fire) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Jawaban wajib diisi',
                            timer: 2500,
                            showConfirmButton: false,
                        });
                    }
                    return null;
                }
                var fd = lpRep.buildFormData(row, [
                    { name: 'question' },
                    { name: 'answer' },
                    { name: 'sort_order' },
                    { name: 'is_published', type: 'checkbox' },
                ]);
                return { fd: fd };
            },
        });
    });
    </script>
@endsection
