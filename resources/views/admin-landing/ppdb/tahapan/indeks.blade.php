@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    @include('admin-landing._komponen._repeater-gaya')
    <style>
        /* === Kartu tahapan: pola collapse (accordion) === */
        .lp-stage-card { padding: 0; }
        .lp-stage-card .lp-rep-icon {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #b45309;
        }
        .lp-stage-card.is-new .lp-rep-icon {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0369a1;
        }

        /* Header clickable + ringkasan + chevron */
        .lp-stage-head {
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
        .lp-stage-card.is-collapsed .lp-stage-head {
            border-bottom: none;
            border-radius: .85rem;
        }
        .lp-stage-card.is-open .lp-stage-head {
            border-radius: .85rem .85rem 0 0;
        }
        .lp-stage-head-title {
            font-weight: 700;
            font-size: .98rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
        }
        .lp-stage-head-key {
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .lp-stage-head-meta {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .35rem;
            margin-left: .5rem;
        }
        .lp-stage-head-meta .lp-stage-pill {
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
        .lp-stage-head-meta .lp-stage-pill .material-symbols-rounded { font-size: 13px; }
        .lp-stage-head-meta .lp-stage-pill.is-empty { color: #94a3b8; font-weight: 500; }

        .lp-stage-status { margin-left: auto; flex-shrink: 0; display: inline-flex; align-items: center; gap: .5rem; }

        .lp-stage-chev {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: inline-flex; align-items: center; justify-content: center;
            color: #475569;
            transition: transform .2s ease, background .15s ease;
        }
        .lp-stage-card.is-open .lp-stage-chev {
            transform: rotate(180deg);
            background: rgba(180, 83, 9, .12);
            color: #b45309;
            border-color: rgba(180, 83, 9, .25);
        }

        /* Body collapse */
        .lp-stage-body-wrap {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows .25s ease;
        }
        .lp-stage-card.is-open .lp-stage-body-wrap { grid-template-rows: 1fr; }
        .lp-stage-body-inner { overflow: hidden; }

        /* Pakai kelas .lp-rep-body dari shared (padding, grid 1fr/2fr, gap). */
        .lp-stage-body .lp-stage-full { grid-column: 1 / -1; }

        /* Toolbar: padding bawah lebih tipis, atas tetap standar */
        .lp-stage-toolbar {
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
        $titleSlot = '<p class="text-muted small mb-0">Tahapan alur pendaftaran PPDB yang tampil di section "Alur Pendaftaran". Klik kartu untuk membuka form edit. Kartu aktif akan tampil di halaman PPDB publik.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'titleSlot' => $titleSlot,
    ])

    <div id="lpStageList" class="lp-rep-stack">
        @forelse ($items as $row)
            @php
                $rowIndex = $loop->iteration;
                $rowId = (int) $row->id;
                $stepVal = $row->step_label ?? '';
                $titleVal = $row->title ?? '';
                $descVal = $row->description ?? '';
                $sortVal = (int) ($row->sort_order ?: 0);
                $pub = (bool) $row->is_published;
                $descPlain = trim(strip_tags((string) $descVal));
                $descPlain = preg_replace('/\s+/', ' ', $descPlain);
                $descSnippet = $descPlain !== ''
                    ? (mb_strlen($descPlain) > 80 ? mb_substr($descPlain, 0, 80) . '…' : $descPlain)
                    : '';
            @endphp
            <div class="lp-rep-card lp-stage-card is-collapsed" data-id="{{ $rowId }}" data-row-index="{{ $rowIndex }}">
                <div class="lp-stage-head" data-role="toggle">
                    <div class="lp-rep-icon"><span class="material-symbols-rounded">timeline</span></div>
                    <div class="min-w-0">
                        <h6 class="lp-stage-head-title">{{ $titleVal !== '' ? $titleVal : 'Tahap #' . $rowIndex }}</h6>
                        <div class="lp-stage-head-key">
                            tahap #{{ $rowIndex }}
                            @if ($rowId) · id {{ $rowId }} @endif
                        </div>
                    </div>
                    <div class="lp-stage-head-meta">
                        <span class="lp-stage-pill">
                            <span class="material-symbols-rounded">flag</span>
                            {{ $stepVal !== '' ? $stepVal : 'Tanpa label' }}
                        </span>
                        <span class="lp-stage-pill {{ $descSnippet === '' ? 'is-empty' : '' }}">
                            <span class="material-symbols-rounded">subject</span>
                            {{ $descSnippet !== '' ? $descSnippet : 'Belum ada deskripsi' }}
                        </span>
                    </div>
                    <div class="lp-stage-status">
                        <label class="lp-rep-toggle {{ $pub ? 'is-on' : 'is-off' }}" for="lp_stage_pub_{{ $rowId }}">
                            <input type="hidden" name="rows[{{ $rowIndex }}][is_published]" value="0">
                            <input type="checkbox" name="rows[{{ $rowIndex }}][is_published]" id="lp_stage_pub_{{ $rowId }}" value="1" data-role="publish" {{ $pub ? 'checked' : '' }}>
                            <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                            <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi {{ $pub ? 'bi-check-lg' : 'bi-x-lg' }}"></i></span>
                            <span class="lp-rep-toggle-text">{{ $pub ? 'Aktif' : 'Non-aktif' }}</span>
                        </label>
                        <span class="lp-stage-chev" aria-hidden="true">
                            <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                        </span>
                    </div>
                </div>

                <div class="lp-stage-body-wrap" data-role="body-wrap">
                    <div class="lp-stage-body-inner">
                        <div class="lp-rep-body lp-stage-body">
                            <div class="input-group input-group-outline mb-0 @if ($stepVal) is-filled @endif">
                                <label class="form-label">Label Tahap (mis. Step 1) <span class="text-danger">*</span></label>
                                <input type="text" name="rows[{{ $rowIndex }}][step_label]" class="form-control" required maxlength="30" value="{{ $stepVal }}">
                            </div>
                            <div class="input-group input-group-outline mb-0 @if ($titleVal) is-filled @endif">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="rows[{{ $rowIndex }}][title]" class="form-control" required maxlength="200" value="{{ $titleVal }}">
                            </div>

                            <div class="lp-stage-full input-group input-group-outline mb-0 @if ($descVal !== '') is-filled @endif">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="rows[{{ $rowIndex }}][description]" class="form-control" rows="3">{{ $descVal }}</textarea>
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
                                    'action' => route('app.admin-landing.ppdb.stages.destroy', $rowId),
                                    'confirm' => 'Hapus tahapan ini?',
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
            <div class="lp-rep-card lp-rep-empty" id="lpStageEmpty">
                <span class="material-symbols-rounded">timeline</span>
                <div class="mt-2 mb-2">Belum ada tahapan.</div>
                <div class="small text-muted">Tambah baris pertama di bawah untuk mulai.</div>
            </div>
        @endforelse
    </div>

    @if ($items instanceof \Illuminate\Contracts\Pagination\Paginator && $items->hasPages())
        <div class="d-flex justify-content-end mt-2">{{ $items->links() }}</div>
    @endif

    <div class="lp-rep-toolbar mt-3 lp-stage-toolbar">
        <div class="lp-rep-toolbar-info">
            <span class="material-symbols-rounded" style="font-size:16px;">tips_and_updates</span>
            Tambah baris baru lalu klik <strong>Simpan Semua</strong> untuk mengirim perubahan.
        </div>
        <div class="lp-rep-toolbar-actions">
            <button type="button" class="btn btn-sm btn-outline-primary" id="lpStageAddBtn">
                <span class="material-symbols-rounded" style="font-size:16px;">add</span>
                Tambah Baris
            </button>
            <button type="button" class="btn btn-sm btn-info" id="lpStageSaveAll">
                <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                Simpan Semua
            </button>
        </div>
    </div>

    <template id="lpStageRowTemplate">
        <div class="lp-rep-card lp-stage-card is-new is-collapsed" data-id="">
            <div class="lp-stage-head" data-role="toggle">
                <div class="lp-rep-icon"><span class="material-symbols-rounded">post_add</span></div>
                <div class="min-w-0">
                    <h6 class="lp-stage-head-title">Tahap Baru</h6>
                    <div class="lp-stage-head-key">tahap baru · akan tersimpan sebagai draf</div>
                </div>
                <div class="lp-stage-head-meta">
                    <span class="lp-stage-pill is-empty">
                        <span class="material-symbols-rounded">flag</span>
                        Tanpa label
                    </span>
                    <span class="lp-stage-pill is-empty">
                        <span class="material-symbols-rounded">subject</span>
                        Belum ada deskripsi
                    </span>
                </div>
                <div class="lp-stage-status">
                    <label class="lp-rep-toggle is-on" for="lp_stage_pub_NEW">
                        <input type="hidden" name="rows[__INDEX__][is_published]" value="0">
                        <input type="checkbox" name="rows[__INDEX__][is_published]" id="lp_stage_pub_NEW" value="1" data-role="publish" checked>
                        <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                        <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                        <span class="lp-rep-toggle-text">Aktif</span>
                    </label>
                    <span class="lp-stage-chev" aria-hidden="true">
                        <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                    </span>
                </div>
            </div>

            <div class="lp-stage-body-wrap" data-role="body-wrap">
                <div class="lp-stage-body-inner">
                    <div class="lp-rep-body lp-stage-body">
                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">Label Tahap (mis. Step 1) <span class="text-danger">*</span></label>
                            <input type="text" name="rows[__INDEX__][step_label]" class="form-control" required maxlength="30" value="">
                        </div>
                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="rows[__INDEX__][title]" class="form-control" required maxlength="200" value="">
                        </div>

                        <div class="lp-stage-full input-group input-group-outline mb-0">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="rows[__INDEX__][description]" class="form-control" rows="3"></textarea>
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
        // Toggle collapse pada header
        function bindStageToggle(card) {
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
        document.querySelectorAll('#lpStageList .lp-stage-card').forEach(function (card) {
            if (!card.classList.contains('is-open')) {
                card.classList.add('is-collapsed');
            }
            bindStageToggle(card);
        });

        lpRep.init({
            listId: 'lpStageList',
            addBtnId: 'lpStageAddBtn',
            saveBtnId: 'lpStageSaveAll',
            emptyId: 'lpStageEmpty',
            templateId: 'lpStageRowTemplate',
            storeUrl: @json(route('app.admin-landing.ppdb.stages.store')),
            updateUrlTpl: @json(route('app.admin-landing.ppdb.stages.update', ['item' => '__ID__'])),
            cardClass: 'lp-stage-card',
            removeBtnSelector: '[data-role="remove"], .btn-remove-row',
            wysiwyg: false,
            afterAppend: function (row) {
                // Baris baru tetap tertutup — admin harus klik header untuk membuka & mengedit.
                row.classList.add('is-collapsed');
                row.classList.remove('is-open');
                bindStageToggle(row);
            },
            gatherPayload: function (row) {
                var step = row.querySelector('input[name*="[step_label]"]');
                var title = row.querySelector('input[name*="[title]"]');
                if (!title || !title.value.trim()) return null;
                if (!step || !step.value.trim()) return null;
                return {
                    fd: lpRep.buildFormData(row, [
                        { name: 'step_label' },
                        { name: 'title' },
                        { name: 'description' },
                        { name: 'sort_order' },
                        { name: 'is_published', type: 'checkbox' },
                    ])
                };
            },
        });
    });
    </script>
@endsection
