@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    @include('admin-landing._komponen._repeater-gaya')
    <style>
        /* === Kartu persyaratan: pola collapse (accordion) === */
        .lp-req-card { padding: 0; }
        .lp-req-card .lp-rep-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1d4ed8;
        }
        .lp-req-card.is-new .lp-rep-icon {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0369a1;
        }

        /* Header clickable + ringkasan + chevron */
        .lp-req-head {
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
        .lp-req-card.is-collapsed .lp-req-head {
            border-bottom: none;
            border-radius: .85rem;
        }
        .lp-req-card.is-open .lp-req-head {
            border-radius: .85rem .85rem 0 0;
        }
        .lp-req-head-title {
            font-weight: 700;
            font-size: .98rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
        }
        .lp-req-head-key {
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .lp-req-head-meta {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .35rem;
            margin-left: .5rem;
        }
        .lp-req-head-meta .lp-req-pill {
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
        .lp-req-head-meta .lp-req-pill .material-symbols-rounded { font-size: 13px; }
        .lp-req-head-meta .lp-req-pill.is-empty { color: #94a3b8; font-weight: 500; }

        .lp-req-status { margin-left: auto; flex-shrink: 0; display: inline-flex; align-items: center; gap: .5rem; }

        .lp-req-chev {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: inline-flex; align-items: center; justify-content: center;
            color: #475569;
            transition: transform .2s ease, background .15s ease;
        }
        .lp-req-card.is-open .lp-req-chev {
            transform: rotate(180deg);
            background: rgba(29, 78, 216, .12);
            color: #1d4ed8;
            border-color: rgba(29, 78, 216, .25);
        }

        /* Body collapse */
        .lp-req-body-wrap {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows .25s ease;
        }
        .lp-req-card.is-open .lp-req-body-wrap { grid-template-rows: 1fr; }
        .lp-req-body-inner { overflow: hidden; }

        /* Pakai kelas .lp-rep-body dari shared (padding, grid 1fr/2fr, gap). */
        .lp-req-body .lp-req-full { grid-column: 1 / -1; }

        /* Textarea items: tinggi lebih besar, font monospace ringan */
        .lp-req-body textarea.lp-req-items {
            font-family: inherit;
            line-height: 1.55;
            resize: vertical;
            min-height: 140px;
        }

        /* Toolbar: padding bawah lebih tipis, atas tetap standar */
        .lp-req-toolbar {
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
        $titleSlot = '<p class="text-muted small mb-0">Kelola daftar persyaratan PPDB per gelombang / jenjang. Setiap entry adalah grup persyaratan dengan daftar item di dalamnya. Klik kartu untuk membuka form edit.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'titleSlot' => $titleSlot,
    ])

    <div id="lpReqList" class="lp-rep-stack">
        @forelse ($items as $row)
            @php
                $itemsList = $row->items_list ?? [];
                $itemsCount = count($itemsList);
                $itemsPreview = $itemsCount > 0
                    ? ($itemsList[0] . ($itemsCount > 1 ? ' (+' . ($itemsCount - 1) . ' item lain)' : ''))
                    : '';
                $itemsText = implode("\n", $itemsList);
                $rowIndex = $loop->iteration;
                $rowId = (int) $row->id;
                $titleVal = $row->title ?? '';
                $groupVal = $row->group ?? '';
                $sortVal = (int) ($row->sort_order ?: 0);
                $pub = (bool) $row->is_published;
            @endphp
            <div class="lp-rep-card lp-req-card is-collapsed" data-id="{{ $rowId }}" data-row-index="{{ $rowIndex }}">
                <div class="lp-req-head" data-role="toggle">
                    <div class="lp-rep-icon"><span class="material-symbols-rounded">fact_check</span></div>
                    <div class="min-w-0">
                        <h6 class="lp-req-head-title">{{ $titleVal !== '' ? $titleVal : 'Grup #' . $rowIndex }}</h6>
                        <div class="lp-req-head-key">
                            grup #{{ $rowIndex }}
                            @if ($rowId) · id {{ $rowId }} @endif
                        </div>
                    </div>
                    <div class="lp-req-head-meta">
                        <span class="lp-req-pill">
                            <span class="material-symbols-rounded">workspaces</span>
                            {{ $groupVal !== '' ? $groupVal : 'umum' }}
                        </span>
                        <span class="lp-req-pill {{ $itemsCount > 0 ? '' : 'is-empty' }}">
                            <span class="material-symbols-rounded">format_list_bulleted</span>
                            {{ $itemsCount > 0 ? $itemsCount . ' item' : 'Belum ada item' }}
                        </span>
                    </div>
                    <div class="lp-req-status">
                        <label class="lp-rep-toggle {{ $pub ? 'is-on' : 'is-off' }}" for="lp_req_pub_{{ $rowId }}">
                            <input type="hidden" name="rows[{{ $rowIndex }}][is_published]" value="0">
                            <input type="checkbox" name="rows[{{ $rowIndex }}][is_published]" id="lp_req_pub_{{ $rowId }}" value="1" data-role="publish" {{ $pub ? 'checked' : '' }}>
                            <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                            <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi {{ $pub ? 'bi-check-lg' : 'bi-x-lg' }}"></i></span>
                            <span class="lp-rep-toggle-text">{{ $pub ? 'Aktif' : 'Non-aktif' }}</span>
                        </label>
                        <span class="lp-req-chev" aria-hidden="true">
                            <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                        </span>
                    </div>
                </div>

                <div class="lp-req-body-wrap" data-role="body-wrap">
                    <div class="lp-req-body-inner">
                        <div class="lp-rep-body lp-req-body">
                            <div class="input-group input-group-outline mb-0 @if ($groupVal) is-filled @endif">
                                <label class="form-label">Grup (opsional: umum / jenjang)</label>
                                <input type="text" name="rows[{{ $rowIndex }}][group]" class="form-control" maxlength="50" value="{{ $groupVal }}">
                            </div>
                            <div class="input-group input-group-outline mb-0 @if ($titleVal) is-filled @endif">
                                <label class="form-label">Judul grup <span class="text-danger">*</span></label>
                                <input type="text" name="rows[{{ $rowIndex }}][title]" class="form-control" required maxlength="200" value="{{ $titleVal }}">
                            </div>

                            <div class="lp-req-full input-group input-group-outline mb-0 @if ($itemsText !== '') is-filled @endif">
                                <label class="form-label">Daftar item (satu item per baris)</label>
                                <textarea name="rows[{{ $rowIndex }}][items]" class="form-control lp-req-items" rows="6" placeholder="Fotokopi ijazah&#10;Akta kelahiran&#10;Kartu keluarga">{{ $itemsText }}</textarea>
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
                                    'action' => route('app.admin-landing.ppdb.requirements.destroy', $rowId),
                                    'confirm' => 'Hapus grup persyaratan ini?',
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
            <div class="lp-rep-card lp-rep-empty" id="lpReqEmpty">
                <span class="material-symbols-rounded">fact_check</span>
                <div class="mt-2 mb-2">Belum ada persyaratan.</div>
                <div class="small text-muted">Tambah baris pertama di bawah untuk mulai.</div>
            </div>
        @endforelse
    </div>

    @if ($items instanceof \Illuminate\Contracts\Pagination\Paginator && $items->hasPages())
        <div class="d-flex justify-content-end mt-2">{{ $items->links() }}</div>
    @endif

    <div class="lp-rep-toolbar mt-3 lp-req-toolbar">
        <div class="lp-rep-toolbar-info">
            <span class="material-symbols-rounded" style="font-size:16px;">tips_and_updates</span>
            Tambah baris baru lalu klik <strong>Simpan Semua</strong> untuk mengirim perubahan.
        </div>
        <div class="lp-rep-toolbar-actions">
            <button type="button" class="btn btn-sm btn-outline-primary" id="lpReqAddBtn">
                <span class="material-symbols-rounded" style="font-size:16px;">add</span>
                Tambah Baris
            </button>
            <button type="button" class="btn btn-sm btn-info" id="lpReqSaveAll">
                <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                Simpan Semua
            </button>
        </div>
    </div>

    <template id="lpReqRowTemplate">
        <div class="lp-rep-card lp-req-card is-new is-collapsed" data-id="">
            <div class="lp-req-head" data-role="toggle">
                <div class="lp-rep-icon"><span class="material-symbols-rounded">post_add</span></div>
                <div class="min-w-0">
                    <h6 class="lp-req-head-title">Grup Baru</h6>
                    <div class="lp-req-head-key">grup baru · akan tersimpan sebagai draf</div>
                </div>
                <div class="lp-req-head-meta">
                    <span class="lp-req-pill">
                        <span class="material-symbols-rounded">workspaces</span>
                        umum
                    </span>
                    <span class="lp-req-pill is-empty">
                        <span class="material-symbols-rounded">format_list_bulleted</span>
                        Belum ada item
                    </span>
                </div>
                <div class="lp-req-status">
                    <label class="lp-rep-toggle is-on" for="lp_req_pub_NEW">
                        <input type="hidden" name="rows[__INDEX__][is_published]" value="0">
                        <input type="checkbox" name="rows[__INDEX__][is_published]" id="lp_req_pub_NEW" value="1" data-role="publish" checked>
                        <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                        <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                        <span class="lp-rep-toggle-text">Aktif</span>
                    </label>
                    <span class="lp-req-chev" aria-hidden="true">
                        <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                    </span>
                </div>
            </div>

            <div class="lp-req-body-wrap" data-role="body-wrap">
                <div class="lp-req-body-inner">
                    <div class="lp-rep-body lp-req-body">
                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">Grup (opsional: umum / jenjang)</label>
                            <input type="text" name="rows[__INDEX__][group]" class="form-control" maxlength="50" value="">
                        </div>
                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">Judul grup <span class="text-danger">*</span></label>
                            <input type="text" name="rows[__INDEX__][title]" class="form-control" required maxlength="200" value="">
                        </div>

                        <div class="lp-req-full input-group input-group-outline mb-0">
                            <label class="form-label">Daftar item (satu item per baris)</label>
                            <textarea name="rows[__INDEX__][items]" class="form-control lp-req-items" rows="6" placeholder="Fotokopi ijazah&#10;Akta kelahiran&#10;Kartu keluarga"></textarea>
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
        function bindReqToggle(card) {
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
        document.querySelectorAll('#lpReqList .lp-req-card').forEach(function (card) {
            if (!card.classList.contains('is-open')) {
                card.classList.add('is-collapsed');
            }
            bindReqToggle(card);
        });

        lpRep.init({
            listId: 'lpReqList',
            addBtnId: 'lpReqAddBtn',
            saveBtnId: 'lpReqSaveAll',
            emptyId: 'lpReqEmpty',
            templateId: 'lpReqRowTemplate',
            storeUrl: @json(route('app.admin-landing.ppdb.requirements.store')),
            updateUrlTpl: @json(route('app.admin-landing.ppdb.requirements.update', ['item' => '__ID__'])),
            cardClass: 'lp-req-card',
            removeBtnSelector: '[data-role="remove"], .btn-remove-row',
            wysiwyg: false,
            afterAppend: function (row) {
                // Baris baru langsung terbuka agar user bisa langsung mengetik
                row.classList.remove('is-collapsed');
                row.classList.add('is-open');
                bindReqToggle(row);
            },
            gatherPayload: function (row) {
                var title = row.querySelector('input[name*="[title]"]');
                if (!title || !title.value.trim()) return null;
                return {
                    fd: lpRep.buildFormData(row, [
                        { name: 'group' },
                        { name: 'title' },
                        { name: 'items' },
                        { name: 'sort_order' },
                        { name: 'is_published', type: 'checkbox' },
                    ])
                };
            },
        });
    });
    </script>
@endsection
