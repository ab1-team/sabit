@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-msg-toolbar .lp-search {
            position: relative;
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #d4d8dd;
            border-radius: .55rem;
            height: 44px;
            padding: 0 .65rem;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .lp-msg-toolbar .lp-search:focus-within {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12);
        }
        .lp-msg-toolbar .lp-search .material-symbols-rounded {
            color: #64748b;
            font-size: 20px;
            margin-right: .5rem;
        }
        .lp-msg-toolbar .lp-search input {
            border: 0;
            outline: 0;
            background: transparent;
            flex: 1;
            height: 100%;
            font-size: .92rem;
            color: #1f2937;
            padding: 0;
        }
        .lp-msg-toolbar .lp-search input::placeholder { color: #94a3b8; }
        .lp-msg-toolbar .lp-search-clear {
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            padding: .15rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .lp-msg-toolbar .lp-search-clear:hover { color: #1f2937; background: #f1f5f9; }
        .lp-msg-toolbar .lp-search-clear .material-symbols-rounded { font-size: 18px; margin: 0; }

        .lp-msg-toolbar .form-select {
            height: 44px;
            border-radius: .55rem;
            border-color: #d4d8dd;
            box-shadow: none !important;
            font-size: .92rem;
        }
        .lp-msg-toolbar .form-select:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
        }
        /* Samakan tinggi Select2 dengan search (44px) */
        .lp-msg-toolbar .select2-container .select2-selection {
            height: 44px !important;
            border-radius: .55rem !important;
            border-color: #d4d8dd !important;
            padding: 0 .65rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .lp-msg-toolbar .select2-container .select2-selection__rendered {
            line-height: 44px !important;
            padding-left: 0 !important;
            font-size: .92rem;
            color: #1f2937;
        }
        .lp-msg-toolbar .select2-container .select2-selection__arrow {
            height: 42px !important;
        }
        .lp-msg-toolbar .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .lp-msg-toolbar .select2-container--bootstrap-5:focus-within .select2-selection {
            border-color: #1d4ed8 !important;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
        }

        .lp-msg-row td { vertical-align: middle; }
        .lp-msg-subj { max-width: 360px; }
        .lp-msg-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: #94a3b8;
        }
        .lp-msg-empty .material-symbols-rounded { font-size: 48px; opacity: .55; }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $unreadCount = $messages->getCollection()->where('is_read', false)->count();
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Pesan dari formulir kontak di halaman landing publik (/kontak).'
            .($unreadCount > 0 ? ' <span class="badge bg-primary ms-1">'.$unreadCount.' belum dibaca</span>' : '')
            .'</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'titleSlot' => $titleSlot,
    ])

    <form method="GET" action="{{ route('app.admin-landing.contact-messages') }}" id="lpMsgFilter" class="card mb-3 lp-msg-toolbar">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-9">
                    <div class="lp-search">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" name="q" id="lpMsgQ" value="{{ $q }}" placeholder="Cari nama, email, subjek, atau isi pesan..." autocomplete="off">
                        <button type="button" class="lp-search-clear" id="lpMsgClear" title="Bersihkan pencarian">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" id="lpMsgStatus" class="form-select">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua status</option>
                        <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Belum dibaca</option>
                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Sudah dibaca</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($messages->isEmpty())
                <div class="lp-msg-empty">
                    <span class="material-symbols-rounded">mail</span>
                    <div class="mt-2">Belum ada pesan masuk.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px"></th>
                                <th>Pengirim</th>
                                <th>Subjek</th>
                                <th style="width:150px">Tanggal</th>
                                <th style="width:200px" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($messages as $msg)
                                <tr class="lp-msg-row {{ $msg->is_read ? '' : 'fw-semibold' }}">
                                    <td class="text-center">
                                        @if ($msg->is_read)
                                            <span class="material-symbols-rounded text-muted" style="font-size:18px;" title="Sudah dibaca">mark_email_read</span>
                                        @else
                                            <span class="material-symbols-rounded text-primary" style="font-size:18px;" title="Belum dibaca">mark_email_unread</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $msg->name ?: 'Anonim' }}</div>
                                        <div class="text-muted small fw-normal">{{ $msg->email ?: '—' }}</div>
                                    </td>
                                    <td class="lp-msg-subj">
                                        <div>{{ $msg->subject ?: '(tanpa subjek)' }}</div>
                                        <div class="text-muted small fw-normal text-truncate" style="max-width:360px;">{{ \Illuminate\Support\Str::limit($msg->message, 90) }}</div>
                                    </td>
                                    <td class="text-muted small fw-normal">{{ $msg->created_at?->format('d M Y H:i') ?: '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary lp-view-message"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#lpMessageModal"
                                                    data-name="{{ e($msg->name) }}"
                                                    data-email="{{ e($msg->email) }}"
                                                    data-subject="{{ e($msg->subject) }}"
                                                    data-message="{{ e($msg->message) }}"
                                                    data-date="{{ $msg->created_at?->format('d M Y H:i') }}"
                                                    title="Lihat detail">
                                                <span class="material-symbols-rounded" style="font-size:16px;">visibility</span>
                                            </button>
                                            <form action="{{ route('app.admin-landing.contact-messages.mark', $msg->id) }}" method="POST" class="lp-ajax d-inline">
                                                @csrf
                                                <input type="hidden" name="is_read" value="{{ $msg->is_read ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-sm {{ $msg->is_read ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="{{ $msg->is_read ? 'Tandai belum dibaca' : 'Tandai sudah dibaca' }}">
                                                    <span class="material-symbols-rounded" style="font-size:16px;">{{ $msg->is_read ? 'mark_email_unread' : 'mark_email_read' }}</span>
                                                </button>
                                            </form>
                                            @include('admin-landing._komponen.formulir-hapus', [
                                                'action' => route('app.admin-landing.contact-messages.destroy', $msg->id),
                                                'confirm' => 'Hapus pesan ini?',
                                                'iconOnly' => true,
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $messages->links() }}</div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade modal-fullscreen" id="lpMessageModal" tabindex="-1" aria-labelledby="lpMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="lpMessageModalLabel">Detail Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row small mb-0">
                    <dt class="col-sm-3">Pengirim</dt><dd class="col-sm-9" id="lpMsgName">—</dd>
                    <dt class="col-sm-3">Surel</dt><dd class="col-sm-9" id="lpMsgEmail">—</dd>
                    <dt class="col-sm-3">Subjek</dt><dd class="col-sm-9 fw-semibold" id="lpMsgSubject">—</dd>
                    <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9" id="lpMsgDate">—</dd>
                    <dt class="col-sm-3">Pesan</dt><dd class="col-sm-9" style="white-space:pre-wrap;" id="lpMsgBody">—</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@include('admin-landing._skrip')

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal detail pesan
    const modal = document.getElementById('lpMessageModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            document.getElementById('lpMsgName').textContent = btn.dataset.name || '—';
            document.getElementById('lpMsgEmail').textContent = btn.dataset.email || '—';
            document.getElementById('lpMsgSubject').textContent = btn.dataset.subject || '(tanpa subjek)';
            document.getElementById('lpMsgDate').textContent = btn.dataset.date || '—';
            document.getElementById('lpMsgBody').textContent = btn.dataset.message || '—';
        });
    }

    // Select2 untuk filter status (auto-submit saat ganti)
    const $status = $('#lpMsgStatus');
    if ($status.length && window.jQuery && $.fn.select2) {
        $status.select2({
            theme: 'bootstrap-5',
            width: '100%',
            minimumResultsForSearch: Infinity,
            language: { noResults: () => 'Tidak ditemukan' }
        }).on('change', function () {
            document.getElementById('lpMsgFilter').submit();
        });
    } else if ($status.length) {
        $status.addEventListener('change', function () {
            document.getElementById('lpMsgFilter').submit();
        });
    }

    // Submit otomatis saat tekan Enter di kolom cari
    const q = document.getElementById('lpMsgQ');
    if (q) {
        q.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('lpMsgFilter').submit();
            }
        });
    }

    // Tombol clear
    const clearBtn = document.getElementById('lpMsgClear');
    if (clearBtn && q) {
        clearBtn.addEventListener('click', function () {
            q.value = '';
            q.focus();
            document.getElementById('lpMsgFilter').submit();
        });
    }
});
</script>
@endsection
