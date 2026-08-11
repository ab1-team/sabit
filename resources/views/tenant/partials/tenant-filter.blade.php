@php
    $tenants = $tenants ?? [];
    $currentTenantId = $currentTenantId ?? null;
    $currentTenant = $currentTenant ?? null;
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

            <form method="GET" action="{{ url()->current() }}" class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
                @foreach (request()->except(['tenant_id', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <label for="tenant-switcher-inline" class="sr-only">Pilih Sekolah</label>
                <select id="tenant-switcher-inline" name="tenant_id" class="tenant-switcher-select block w-full sm:min-w-[280px] sm:max-w-[420px]" data-placeholder="Pilih Sekolah">
                    <option value="">Semua Sekolah</option>
                    @foreach ($tenants as $t)
                        <option value="{{ $t->id }}" {{ ($currentTenantId ?? '') === $t->id ? 'selected' : '' }}>
                            {{ $t->nama }}{{ !empty($t->domain) ? ' · '.$t->domain : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </section>

    @once
        <style>
            .tenant-switcher-select.select2-hidden-accessible { min-width: 0; max-width: 100%; width: 100% !important; }
            @media (min-width: 640px) {
                .tenant-switcher-select.select2-hidden-accessible { min-width: 280px; max-width: 420px; }
                .tenant-switcher-select + .select2-container { min-width: 280px !important; max-width: 420px !important; }
            }
            .tenant-switcher-select + .select2-container { width: 100% !important; }
            .tenant-switcher-select + .select2-container .select2-selection { min-height: 40px; }
        </style>
        <script>
            (function () {
                if (window.__tenantSwitcherInit) {
                    if (typeof window.jQuery !== 'undefined') window.jQuery(document).trigger('tenant-switcher:init');
                } else {
                    window.__tenantSwitcherInit = true;
                }

                function initTenantSwitcher() {
                    if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 === 'undefined') {
                        return setTimeout(initTenantSwitcher, 80);
                    }
                    var $ = window.jQuery;
                    $('.tenant-switcher-select').each(function () {
                        var $el = $(this);
                        if ($el.data('select2')) return;
                        $el.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: $el.data('placeholder') || 'Pilih Sekolah',
                            allowClear: false,
                            minimumResultsForSearch: 0,
                        }).on('change', function () {
                            var form = this.form;
                            if (form) form.submit();
                        });
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initTenantSwitcher);
                } else {
                    initTenantSwitcher();
                }
            })();
        </script>
    @endonce
@endif