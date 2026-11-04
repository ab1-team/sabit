@php
    $tenants = $tenants ?? [];
    $currentTenantId = $currentTenantId ?? null;
    $currentTenant = $currentTenant ?? null;
    // Filter status bersifat OPSIONAL. Halaman yang memakainyamisalkan:
    //   $statusFilterOptions = [['value' => '__all__', 'label' => 'Semua status'], ...]
    //   $statusFilterValue   = '__all__' | 'paid' | 'unpaid'
    //   $statusFilterLabel   = 'Status'
    $statusFilterOptions = $statusFilterOptions ?? [];
    $statusFilterValue = $statusFilterValue ?? '__all__';
    $statusFilterLabel = $statusFilterLabel ?? 'Status';
@endphp
@if (!empty($tenants))
    <section class="mt-4 mb-4 overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-purple-50 shadow-sm">
        <div class="flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Filter Sekolah</p>
                    @if (!empty($currentTenantId) && $currentTenant)
                        <h3 class="truncate text-sm font-bold text-slate-900 leading-tight">{{ $currentTenant->nama }}</h3>
                        <p class="truncate font-mono text-[11px] text-slate-600 leading-tight">{{ $currentTenant->domain }}</p>
                    @else
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">Semua Sekolah</h3>
                        <p class="text-[11px] text-slate-500 leading-tight">Data ditampilkan dari seluruh sekolah tenant.</p>
                    @endif
                </div>
            </div>

            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:justify-end">
                <form method="GET" action="{{ url()->current() }}" class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
                    @php
                        // Parameter yang diteruskan ke form sekolah.
                        // `status_filter` sengaja TIDAK ikut di sini karena
                        // nilainya disalin oleh JS dari <select id="filter-status">
                        // ke input tersembunyi di bawah (supaya filter tetap
                        // konsisten, bukan nilai URL yang basi).
                        $carryParams = request()->except(['tenant_id', 'page']);
                        unset($carryParams['status_filter']);
                    @endphp
                    @foreach ($carryParams as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    {{--
                        Nilai filter status disimpan di input tersembunyi supaya
                        pilihan status tetap ikut saat sekolah diganti.
                        Nilainya disalin dari <select id="filter-status"> oleh JS.
                    --}}
                    @if (!empty($statusFilterOptions))
                        <input type="hidden" id="status-filter-input" name="status_filter"
                               value="{{ $statusFilterValue === '__all__' ? '' : $statusFilterValue }}">
                    @endif
                    <label for="tenant-switcher-inline" class="sr-only">Pilih Sekolah</label>
                    {{--
                        CATATAN select2:
                        Opsi "Semua Sekolah" memakai nilai sentinel `__all__`, BUKAN
                        string kosong. Bila <select> diberi `placeholder`, select2
                        memperlakukan opsi bernilai kosong sebagai placeholder lalu
                        MENGHAPUSNYA dari daftar dropdown — sehingga pengguna tidak
                        bisa kembali ke "Semua Sekolah" setelah memilih sekolah lain.

                        Nilai sentinel diterjemahkan menjadi string kosong saat form
                        dikirim (lihat JS di bawah) karena string kosong berarti
                        "tanpa filter".
                    --}}
                    <select id="tenant-switcher-inline" name="tenant_id" class="tenant-switcher-select block w-full sm:min-w-[280px] sm:max-w-[420px]">
                        <option value="__all__">Semua Sekolah</option>
                        @foreach ($tenants as $t)
                            <option value="{{ $t->id }}" {{ ($currentTenantId ?? '') === $t->id ? 'selected' : '' }}>
                                {{ $t->nama }}{{ !empty($t->domain) ? ' · '.$t->domain : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if (!empty($statusFilterOptions))
                    {{--
                        Filter status (opsional). Sengaja DI LUAR form sekolah:
                        perubahannya tidak memuat ulang halaman, melainkan
                        difilter langsung oleh tabel di halaman ini.
                    --}}
                    <div class="flex w-full items-center gap-2 sm:w-auto">
                        <label for="filter-status" class="shrink-0 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $statusFilterLabel }}</label>
                        <select id="filter-status" class="filter-select status-filter-select select2"
                                data-search-placeholder="{{ $statusFilterLabel }}...">
                            @foreach ($statusFilterOptions as $opt)
                                <option value="{{ $opt['value'] }}" @selected((string) $statusFilterValue === (string) $opt['value'])>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @once
        <style>
            /* CATATAN: jangan memberi `width: 100% !important` pada
               `.tenant-switcher-select.select2-hidden-accessible`. Select2
               menyembunyikan <select> asli dengan `position:absolute;
               width:1px !important`; override itu membuat select tersembunyi
               selebar viewport dan halaman ikut ter-scroll horizontal. */
            @media (min-width: 640px) {
                .tenant-switcher-select + .select2-container { min-width: 280px !important; max-width: 420px !important; }
            }
            .tenant-switcher-select + .select2-container { width: 100% !important; }
            .tenant-switcher-select + .select2-container .select2-selection { min-height: 40px; }

            /* Filter status di panel sekolah */
            .status-filter-select + .select2-container { width: 100% !important; }
            .status-filter-select + .select2-container .select2-selection { min-height: 40px; }
            @media (min-width: 640px) {
                .status-filter-select + .select2-container { min-width: 170px !important; max-width: 260px !important; }
            }
        </style>
        <script>
            (function () {
                if (window.__tenantSwitcherInit) {
                    if (typeof window.jQuery !== 'undefined') window.jQuery(document).trigger('tenant-switcher:init');
                } else {
                    window.__tenantSwitcherInit = true;
                }

                // Nilai sentinel untuk opsi "Semua Sekolah" (lihat catatan pada
                // markup di atas). Dikirim sebagai string kosong karena string
                // kosong berarti "tanpa filter".
                var ALL = '__all__';

                function initTenantSwitcher() {
                    if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 === 'undefined') {
                        return setTimeout(initTenantSwitcher, 80);
                    }
                    var $ = window.jQuery;
                    $('.tenant-switcher-select').each(function () {
                        var $el = $(this);
                        if ($el.data('select2')) return;

                        // Jangan memakai `placeholder`: opsi bernilai kosong akan
                        // dianggap placeholder dan dihapus dari dropdown.
                        $el.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            allowClear: false,
                            minimumResultsForSearch: 0,
                        }).on('select2:open', function () {
                            // Select2 4.1 tidak punya opsi `searchPlaceholder`.
                            var $field = $('.select2-container--open .select2-search__field');
                            if ($field.length) {
                                $field.attr('placeholder', 'Cari sekolah...');
                            }
                        }).on('change', function () {
                            // Terjemahkan sentinel → string kosong sebelum submit.
                            if (this.value === ALL) {
                                this.value = '';
                            }
                            var form = this.form;
                            if (form) {
                                // Buang `status_filter` yang kosong supaya URL tetap rapi.
                                var $hidden = $(form).find('#status-filter-input');
                                if ($hidden.length && !$hidden.val()) {
                                    $hidden.removeAttr('name');
                                }
                                form.submit();
                            }
                        });
                    });
                }

                /*
                 * Filter status (opsional).
                 *
                 * Nilainya dicopy ke input tersembunyi di dalam form sekolah
                 * supaya pilihan status tidak hilang saat pengguna berganti
                 * sekolah (form sekolah di-submit ulang).
                 */
                function syncStatusFilter() {
                    if (typeof window.jQuery === 'undefined') return;
                    var $ = window.jQuery;
                    var $el = $('#filter-status');
                    var $hidden = $('#status-filter-input');
                    if (!$el.length || !$hidden.length) return;

                    var value = $el.val();
                    $hidden.attr('name', 'status_filter').val(value === ALL ? '' : (value || ''));
                }

                function initStatusFilter() {
                    if (typeof window.jQuery === 'undefined') {
                        return setTimeout(initStatusFilter, 80);
                    }
                    var $ = window.jQuery;

                    // Select2 pada filter status. Halaman yang memakainya
                    // (invoice) juga menginisialisasinya sendiri; cek `data('select2')`
                    // supaya tidak di-init dua kali.
                    var $el = $('#filter-status');
                    if ($el.length && !$el.data('select2') && typeof $.fn.select2 !== 'undefined') {
                        $el.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            allowClear: false,
                            minimumResultsForSearch: Infinity,
                        }).on('select2:open', function () {
                            var $field = $('.select2-container--open .select2-search__field');
                            if ($field.length) {
                                $field.attr('placeholder', $el.data('search-placeholder') || 'Cari...');
                            }
                        });
                    }

                    $(document).on('change', '#filter-status', syncStatusFilter);
                    syncStatusFilter();
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () {
                        initTenantSwitcher();
                        initStatusFilter();
                    });
                } else {
                    initTenantSwitcher();
                    initStatusFilter();
                }
            })();
        </script>
    @endonce
@endif
