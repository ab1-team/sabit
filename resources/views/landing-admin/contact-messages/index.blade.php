@extends('layouts.tenant.base')

@section('style')
    @include('landing-admin._styles')
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Pesan yang dikirim dari formulir kontak di halaman landing publik (/kontak).</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'titleSlot' => $titleSlot,
    ])

    <form method="GET" action="{{ route('app.landing.contact-messages') }}" class="card mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small">Cari</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Nama, email, subjek, atau isi pesan...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Belum dibaca</option>
                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Sudah dibaca</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">search</span>
                        Terapkan
                    </button>
                    <a href="{{ route('app.landing.contact-messages') }}" class="btn btn-sm btn-light">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($messages->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">mail</span>
                    <div class="mt-2">Belum ada pesan masuk.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:36px"></th>
                                <th>Pengirim</th>
                                <th>Subjek</th>
                                <th style="width:140px">Tanggal</th>
                                <th style="width:220px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($messages as $msg)
                                <tr>
                                    <td class="text-center">
                                        @if ($msg->is_read)
                                            <span class="material-symbols-rounded text-muted" style="font-size:18px;" title="Sudah dibaca">mark_email_read</span>
                                        @else
                                            <span class="material-symbols-rounded text-primary" style="font-size:18px;" title="Belum dibaca">mark_email_unread</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $msg->name ?: 'Anonim' }}</div>
                                        <div class="text-muted small">{{ $msg->email ?: '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $msg->subject ?: '(tanpa subjek)' }}</div>
                                        <div class="text-muted small text-truncate" style="max-width:380px;">{{ \Illuminate\Support\Str::limit($msg->message, 90) }}</div>
                                    </td>
                                    <td>{{ $msg->created_at?->format('d M Y H:i') ?: '—' }}</td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary lp-view-message"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#lpMessageModal"
                                                    data-name="{{ e($msg->name) }}"
                                                    data-email="{{ e($msg->email) }}"
                                                    data-subject="{{ e($msg->subject) }}"
                                                    data-message="{{ e($msg->message) }}"
                                                    data-date="{{ $msg->created_at?->format('d M Y H:i') }}">
                                                <span class="material-symbols-rounded" style="font-size:16px;">visibility</span>
                                            </button>
                                            <form action="{{ route('app.landing.contact-messages.mark', $msg->id) }}" method="POST" class="lp-ajax d-inline">
                                                @csrf
                                                <input type="hidden" name="is_read" value="{{ $msg->is_read ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $msg->is_read ? 'Tandai belum dibaca' : 'Tandai sudah dibaca' }}">
                                                    <span class="material-symbols-rounded" style="font-size:16px;">{{ $msg->is_read ? 'mark_email_unread' : 'mark_email_read' }}</span>
                                                </button>
                                            </form>
                                            @include('landing-admin._components.delete-form', [
                                                'action' => route('app.landing.contact-messages.destroy', $msg->id),
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

@include('landing-admin._scripts')

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('lpMessageModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('lpMsgName').textContent = btn.dataset.name || '—';
        document.getElementById('lpMsgEmail').textContent = btn.dataset.email || '—';
        document.getElementById('lpMsgSubject').textContent = btn.dataset.subject || '(tanpa subjek)';
        document.getElementById('lpMsgDate').textContent = btn.dataset.date || '—';
        document.getElementById('lpMsgBody').textContent = btn.dataset.message || '—';
    });
});
</script>
@endsection
