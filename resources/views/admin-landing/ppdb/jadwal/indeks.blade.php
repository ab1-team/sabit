@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    @include('admin-landing._komponen._repeater-gaya')
    <style>
        /* === Kartu jadwal: pola collapse (accordion) === */
        .lp-jad-card { padding: 0; }
        .lp-jad-card .lp-rep-icon {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #15803d;
        }
        .lp-jad-card.is-new .lp-rep-icon {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0369a1;
        }

        /* Header clickable + ringkasan + chevron */
        .lp-jad-head {
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
        .lp-jad-card.is-collapsed .lp-jad-head {
            border-bottom: none;
            border-radius: .85rem;
        }
        .lp-jad-card.is-open .lp-jad-head {
            border-radius: .85rem .85rem 0 0;
        }
        .lp-jad-head-title {
            font-weight: 700;
            font-size: .98rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
        }
        .lp-jad-head-key {
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .lp-jad-head-meta {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .35rem;
            margin-left: .5rem;
        }
        .lp-jad-head-meta .lp-jad-pill {
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
        .lp-jad-head-meta .lp-jad-pill .material-symbols-rounded { font-size: 13px; }
        .lp-jad-head-meta .lp-jad-pill.is-empty { color: #94a3b8; font-weight: 500; }
        .lp-jad-head-meta .lp-jad-pill.is-draft { background: #fef3c7; color: #92400e; }
        .lp-jad-head-meta .lp-jad-pill.is-pub   { background: #dcfce7; color: #15803d; }

        .lp-jad-status { margin-left: auto; flex-shrink: 0; display: inline-flex; align-items: center; gap: .5rem; }

        .lp-jad-chev {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: inline-flex; align-items: center; justify-content: center;
            color: #475569;
            transition: transform .2s ease, background .15s ease;
        }
        .lp-jad-card.is-open .lp-jad-chev {
            transform: rotate(180deg);
            background: rgba(25, 135, 84, .12);
            color: #15803d;
            border-color: rgba(25, 135, 84, .25);
        }

        /* Body collapse */
        .lp-jad-body-wrap {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows .25s ease;
        }
        .lp-jad-card.is-open .lp-jad-body-wrap { grid-template-rows: 1fr; }
        .lp-jad-body-inner { overflow: hidden; }

        /* Pakai kelas .lp-rep-body dari shared (padding, grid 1fr/2fr, gap).
           Di sini cukup override untuk full-width row & half-row. */
        .lp-jad-body .lp-jad-full { grid-column: 1 / -1; }
        .lp-jad-body .lp-jad-half-row {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem 1rem;
        }

        /* Footer */
        .lp-jad-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .65rem;
            flex-wrap: wrap;
            padding: .65rem 1.1rem;
            border-top: 1px dashed #e2e8f0;
            background: #fafbfc;
        }
        .lp-jad-foot-info {
            font-size: .72rem;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .lp-jad-foot-info .material-symbols-rounded { font-size: 13px; }
        .lp-jad-foot .btn .material-symbols-rounded { font-size: 16px; line-height: 1; }

        /* Toolbar: stack rapi di mobile, row di desktop.
           Pakai padding tipis + tombol ringkas + font info kecil
           agar wrapper tidak terlalu tinggi. */
        .lp-rep-toolbar {
            align-items: center;
            flex-direction: column;
            row-gap: .5rem;
            padding-top: .85rem;
            padding-right: 1.1rem;
            padding-bottom: .2rem;
            padding-left: 1.1rem;
            border-color: rgba(25, 135, 84, .2);
            min-height: 0;
        }
        .lp-rep-toolbar .lp-rep-toolbar-info {
            flex: 0 0 auto;
            text-align: center;
            color: #475569;
            font-size: .75rem;
            line-height: 1.25;
            gap: .25rem;
        }
        .lp-rep-toolbar .lp-rep-toolbar-info .material-symbols-rounded {
            color: #15803d;
            font-size: 14px !important;
        }
        .lp-rep-toolbar .lp-rep-toolbar-actions {
            flex: 0 0 auto;
            justify-content: center;
            width: 100%;
            gap: .4rem;
        }
        .lp-rep-toolbar .btn {
            border-radius: .55rem;
        }
        .lp-rep-toolbar .btn .material-symbols-rounded {
            font-size: 16px !important;
        }
        @media (min-width: 768px) {
            .lp-rep-toolbar {
                flex-direction: row;
                justify-content: space-between;
            }
            .lp-rep-toolbar .lp-rep-toolbar-info { text-align: left; }
            .lp-rep-toolbar .lp-rep-toolbar-actions {
                width: auto;
                justify-content: flex-end;
            }
        }

        /* Tombol Simpan Semua: pakai biru primer (override Material Kit hijau) */
        .lp-btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #2563eb;
            --bs-btn-border-color: #2563eb;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #1d4ed8;
            --bs-btn-hover-border-color: #1d4ed8;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #1e40af;
            --bs-btn-active-border-color: #1e40af;
            background-color: var(--bs-btn-bg);
            border-color: var(--bs-btn-border-color);
            color: var(--bs-btn-color);
        }
        .lp-btn-primary:hover,
        .lp-btn-primary:focus {
            background-color: var(--bs-btn-hover-bg);
            border-color: var(--bs-btn-hover-border-color);
            color: var(--bs-btn-hover-color);
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @php
        $titleSlot = '<p class="text-muted small mb-0">Kelola gelombang / jadwal pendaftaran PPDB beserta tanggal, biaya daftar, dan SPP. Klik kartu untuk membuka form edit. Kartu aktif akan tampil di section Jadwal halaman PPDB publik.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'titleSlot' => $titleSlot,
    ])

    <div id="lpJadList" class="lp-rep-stack">
        @forelse ($items as $row)
            @php
                $rowIndex = $loop->iteration;
                $rowId = (int) $row->id;
                $gelVal = $row->gelombang ?? '';
                $startVal = $row->start_date?->format('Y-m-d') ?? '';
                $endVal = $row->end_date?->format('Y-m-d') ?? '';
                $biayaRaw = $row->biaya_daftar ?? '';
                $sppRaw = $row->spp_bulanan ?? '';
                $biayaNum = is_numeric($biayaRaw) ? (int) $biayaRaw : (preg_match('/\d/', $biayaRaw) ? (int) preg_replace('/[^\d]/', '', $biayaRaw) : 0);
                $sppNum = is_numeric($sppRaw) ? (int) $sppRaw : (preg_match('/\d/', $sppRaw) ? (int) preg_replace('/[^\d]/', '', $sppRaw) : 0);
                $biayaVal = $biayaNum > 0 ? number_format($biayaNum, 0, ',', '.') : '';
                $sppVal = $sppNum > 0 ? number_format($sppNum, 0, ',', '.') : '';
                $sortVal = (int) ($row->sort_order ?: 0);
                $pub = (bool) $row->is_published;
                $startLabel = $startVal ? \Carbon\Carbon::parse($startVal)->translatedFormat('d M Y') : '—';
                $endLabel = $endVal ? \Carbon\Carbon::parse($endVal)->translatedFormat('d M Y') : null;
            @endphp
            <div class="lp-rep-card lp-jad-card is-collapsed" data-id="{{ $rowId }}" data-row-index="{{ $rowIndex }}">
                <div class="lp-jad-head" data-role="toggle">
                    <div class="lp-rep-icon"><span class="material-symbols-rounded">event</span></div>
                    <div class="min-w-0">
                        <h6 class="lp-jad-head-title">{{ $gelVal ?: 'Gelombang #' . $rowIndex }}</h6>
                        <div class="lp-jad-head-key">
                            gelombang #{{ $rowIndex }}
                            @if ($rowId) · id {{ $rowId }} @endif
                        </div>
                    </div>
                    <div class="lp-jad-head-meta">
                        <span class="lp-jad-pill">
                            <span class="material-symbols-rounded">calendar_today</span>
                            {{ $startLabel }}@if ($endLabel) — {{ $endLabel }} @endif
                        </span>
                        <span class="lp-jad-pill {{ $biayaVal ? '' : 'is-empty' }}">
                            <span class="material-symbols-rounded">payments</span>
                            {{ $biayaVal !== '' ? $biayaVal : 'Belum ada biaya' }}
                        </span>
                    </div>
                    <div class="lp-jad-status">
                        <label class="lp-rep-toggle {{ $pub ? 'is-on' : 'is-off' }}" for="lp_jad_pub_{{ $rowId ?: 'NEW_' . $rowIndex }}">
                            <input type="hidden" name="rows[{{ $rowIndex }}][is_published]" value="0">
                            <input type="checkbox" name="rows[{{ $rowIndex }}][is_published]" id="lp_jad_pub_{{ $rowId ?: 'NEW_' . $rowIndex }}" value="1" data-role="publish" {{ $pub ? 'checked' : '' }}>
                            <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                            <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi {{ $pub ? 'bi-check-lg' : 'bi-x-lg' }}"></i></span>
                            <span class="lp-rep-toggle-text">{{ $pub ? 'Aktif' : 'Draft' }}</span>
                        </label>
                        <span class="lp-jad-chev" aria-hidden="true">
                            <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                        </span>
                    </div>
                </div>

                <div class="lp-jad-body-wrap" data-role="body-wrap">
                    <div class="lp-jad-body-inner">
                        <div class="lp-rep-body lp-jad-body">
                            <div class="lp-jad-half-row">
                                <div class="input-group input-group-outline mb-0 @if ($gelVal) is-filled @endif">
                                    <label class="form-label">Gelombang <span class="text-danger">*</span></label>
                                    <input type="text" name="rows[{{ $rowIndex }}][gelombang]" class="form-control" required maxlength="100" value="{{ $gelVal }}">
                                </div>
                                <div class="input-group input-group-outline mb-0 @if ($sortVal > 0) is-filled @endif">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="rows[{{ $rowIndex }}][sort_order]" class="form-control" min="0" value="{{ $sortVal }}">
                                </div>
                            </div>

                            <div class="lp-jad-half-row">
                                <div class="input-group input-group-outline mb-0 @if ($startVal) is-filled @endif">
                                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="text" name="rows[{{ $rowIndex }}][start_date]" class="form-control lp-date-only" required value="{{ $startVal }}" placeholder="Pilih tanggal…" autocomplete="off">
                                </div>
                                <div class="input-group input-group-outline mb-0 @if ($endVal) is-filled @endif">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="text" name="rows[{{ $rowIndex }}][end_date]" class="form-control lp-date-only" value="{{ $endVal }}" placeholder="Pilih tanggal…" autocomplete="off">
                                </div>
                            </div>

                            <div class="input-group input-group-outline mb-0 @if ($biayaVal) is-filled @endif">
                                <label class="form-label">Biaya Pendaftaran</label>
                                <input type="text" inputmode="numeric" name="rows[{{ $rowIndex }}][biaya_daftar]" class="form-control lp-money-norp" maxlength="20" value="{{ $biayaVal }}">
                            </div>
                            <div class="input-group input-group-outline mb-0 @if ($sppVal) is-filled @endif">
                                <label class="form-label">SPP Bulanan</label>
                                <input type="text" inputmode="numeric" name="rows[{{ $rowIndex }}][spp_bulanan]" class="form-control lp-money-norp" maxlength="20" value="{{ $sppVal }}">
                            </div>
                        </div>

                        <div class="lp-jad-foot">
                            <div class="lp-jad-foot-info">
                                <span class="material-symbols-rounded">info</span>
                                Status tampil di halaman publik ditentukan oleh toggle di header.
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @include('admin-landing._komponen.formulir-hapus', [
                                    'action' => route('app.admin-landing.ppdb.schedules.destroy', $rowId),
                                    'confirm' => 'Hapus gelombang ' . ($gelVal ?: 'ini') . '?',
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
            <div class="lp-rep-card lp-rep-empty" id="lpJadEmpty">
                <span class="material-symbols-rounded">event_busy</span>
                <div class="mt-2 mb-2">Belum ada jadwal / gelombang.</div>
                <div class="small text-muted">Tambah baris pertama di bawah untuk mulai.</div>
            </div>
        @endforelse
    </div>

    @if ($items instanceof \Illuminate\Contracts\Pagination\Paginator && $items->hasPages())
        <div class="d-flex justify-content-end mt-2">{{ $items->links() }}</div>
    @endif

    <div class="lp-rep-toolbar mt-3">
        <div class="lp-rep-toolbar-info">
            <span class="material-symbols-rounded">tips_and_updates</span>
            Tambah baris baru lalu klik <strong>Simpan Semua</strong> untuk mengirim perubahan.
        </div>
        <div class="lp-rep-toolbar-actions">
            <button type="button" class="btn btn-sm btn-outline-success" id="lpJadAddBtn">
                <span class="material-symbols-rounded">add</span>
                Tambah Baris
            </button>
            <button type="button" class="btn btn-sm lp-btn-primary" id="lpJadSaveAll">
                <span class="material-symbols-rounded">save</span>
                Simpan Semua
            </button>
        </div>
    </div>

    <template id="lpJadRowTemplate">
        <div class="lp-rep-card lp-jad-card is-new is-collapsed" data-id="">
            <div class="lp-jad-head" data-role="toggle">
                <div class="lp-rep-icon"><span class="material-symbols-rounded">post_add</span></div>
                <div class="min-w-0">
                    <h6 class="lp-jad-head-title">Gelombang Baru</h6>
                    <div class="lp-jad-head-key">gelombang baru · akan tersimpan sebagai draf</div>
                </div>
                <div class="lp-jad-head-meta">
                    <span class="lp-jad-pill is-empty">
                        <span class="material-symbols-rounded">calendar_today</span>
                        —
                    </span>
                    <span class="lp-jad-pill is-empty">
                        <span class="material-symbols-rounded">payments</span>
                        Belum ada biaya
                    </span>
                </div>
                <div class="lp-jad-status">
                    <label class="lp-rep-toggle is-off" for="lp_jad_pub_NEW">
                        <input type="hidden" name="rows[__INDEX__][is_published]" value="0">
                        <input type="checkbox" name="rows[__INDEX__][is_published]" id="lp_jad_pub_NEW" value="1" data-role="publish">
                        <span class="lp-rep-toggle-track" aria-hidden="true"></span>
                        <span class="lp-rep-toggle-icon" aria-hidden="true"><i class="bi bi-x-lg"></i></span>
                        <span class="lp-rep-toggle-text">Draft</span>
                    </label>
                    <span class="lp-jad-chev" aria-hidden="true">
                        <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                    </span>
                </div>
            </div>

            <div class="lp-jad-body-wrap" data-role="body-wrap">
                <div class="lp-jad-body-inner">
                    <div class="lp-rep-body lp-jad-body">
                        <div class="lp-jad-half-row">
                            <div class="input-group input-group-outline mb-0">
                                <label class="form-label">Gelombang <span class="text-danger">*</span></label>
                                <input type="text" name="rows[__INDEX__][gelombang]" class="form-control" required maxlength="100" value="">
                            </div>
                            <div class="input-group input-group-outline mb-0">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="rows[__INDEX__][sort_order]" class="form-control" min="0" value="__SORT__">
                            </div>
                        </div>

                        <div class="lp-jad-half-row">
                            <div class="input-group input-group-outline mb-0">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="text" name="rows[__INDEX__][start_date]" class="form-control lp-date-only" required value="" placeholder="Pilih tanggal…" autocomplete="off">
                            </div>
                            <div class="input-group input-group-outline mb-0">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="text" name="rows[__INDEX__][end_date]" class="form-control lp-date-only" value="" placeholder="Pilih tanggal…" autocomplete="off">
                            </div>
                        </div>

                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">Biaya Pendaftaran</label>
                            <input type="text" inputmode="numeric" name="rows[__INDEX__][biaya_daftar]" class="form-control lp-money-norp" maxlength="20" value="">
                        </div>
                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">SPP Bulanan</label>
                            <input type="text" inputmode="numeric" name="rows[__INDEX__][spp_bulanan]" class="form-control lp-money-norp" maxlength="20" value="">
                        </div>
                    </div>

                    <div class="lp-jad-foot">
                        <div class="lp-jad-foot-info">
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
        function bindJadToggle(card) {
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
        document.querySelectorAll('#lpJadList .lp-jad-card').forEach(function (card) {
            if (!card.classList.contains('is-open')) {
                card.classList.add('is-collapsed');
            }
            bindJadToggle(card);
        });

        lpRep.init({
            listId: 'lpJadList',
            addBtnId: 'lpJadAddBtn',
            saveBtnId: 'lpJadSaveAll',
            emptyId: 'lpJadEmpty',
            templateId: 'lpJadRowTemplate',
            storeUrl: @json(route('app.admin-landing.ppdb.schedules.store')),
            updateUrlTpl: @json(route('app.admin-landing.ppdb.schedules.update', ['item' => '__ID__'])),
            cardClass: 'lp-jad-card',
            removeBtnSelector: '[data-role="remove"], .btn-remove-row',
            wysiwyg: false,
            afterAppend: function (row) {
                // Baris baru tetap tertutup — user klik header untuk membuka
                row.classList.remove('is-open');
                row.classList.add('is-collapsed');
                bindJadToggle(row);

                // Inisialisasi plugin untuk baris baru: flatpickr & maskMoney
                if (window.flatpickr) {
                    row.querySelectorAll('input.lp-date-only').forEach(function (el) {
                        if (el._flatpickr) return;
                        el._flatpickr = flatpickr(el, {
                            dateFormat: 'Y-m-d',
                            allowInput: true,
                            locale: (window.flatpickr && window.flatpickr.l10ns && window.flatpickr.l10ns.id) ? 'id' : 'default',
                        });
                    });
                }
                if (window.jQuery && jQuery.fn.maskMoney) {
                    row.querySelectorAll('input.lp-money-norp').forEach(function (el) {
                        var $el = jQuery(el);
                        if ($el.data('maskMoney')) return;
                        $el.maskMoney({
                            prefix: '',
                            suffix: '',
                            thousands: '.',
                            decimal: ',',
                            precision: 0,
                            allowZero: true,
                            allowNegative: false,
                        });
                    });
                }

                // Sync is-filled: setelah plugin init, pastikan label naik jika ada value
                row.querySelectorAll('.input-group-outline input, .input-group-outline textarea, .input-group-outline select').forEach(function (el) {
                    if (el.type === 'checkbox' || el.type === 'radio' || el.type === 'file') return;
                    var wrap = el.closest('.input-group-outline');
                    if (!wrap) return;
                    var hasValue = el.value !== null && el.value !== '';
                    wrap.classList.toggle('is-filled', hasValue);
                    // Re-binding listener agar trigger saat maskMoney/flatpickr format value
                    if (!el.__lpFilledBound) {
                        el.__lpFilledBound = true;
                        var syncFn = function () {
                            var v = el.value;
                            wrap.classList.toggle('is-filled', v !== null && v !== '');
                        };
                        el.addEventListener('input', syncFn);
                        el.addEventListener('change', syncFn);
                        el.addEventListener('blur', syncFn);
                        // maskMoney kadang update .value tanpa trigger event "input".
                        // Pakai observer atribut sebagai fallback.
                        if (window.MutationObserver && el.classList.contains('lp-money-norp')) {
                            var prev = el.value;
                            new MutationObserver(function () {
                                if (el.value !== prev) {
                                    prev = el.value;
                                    syncFn();
                                }
                            }).observe(el, { attributes: true, attributeFilter: ['value'] });
                        }
                    }
                });
            },
            gatherPayload: function (row) {
                var gelombang = row.querySelector('input[name*="[gelombang]"]');
                var start = row.querySelector('input[name*="[start_date]"]');
                if (!gelombang || !gelombang.value.trim()) return null;
                if (!start || !start.value.trim()) {
                    if (typeof Swal !== 'undefined' && Swal.fire) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanggal mulai wajib',
                            text: 'Isi tanggal mulai untuk gelombang "' + gelombang.value.trim() + '".',
                            timer: 3000,
                            showConfirmButton: false,
                        });
                    }
                    return null;
                }
                return {
                    fd: lpRep.buildFormData(row, [
                        { name: 'gelombang' },
                        { name: 'start_date' },
                        { name: 'end_date' },
                        { name: 'biaya_daftar' },
                        { name: 'spp_bulanan' },
                        { name: 'sort_order' },
                        { name: 'is_published', type: 'checkbox' },
                    ])
                };
            },
        });
    });
    </script>
@endsection
