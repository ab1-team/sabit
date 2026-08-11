@extends('layouts.tenant.base')
@section('content')

@php
    $avatar = $user->foto
        ? \App\Models\Profil::tenantStorageUrl('users/' . $user->foto)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama ?? 'User') . '&size=120&background=111827&color=fff&bold=true';
    $isFilled = fn($v) => $v ? 'is-filled' : '';
@endphp

<style>
    .profile-hero {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #0f766e 100%);
        color: #fff;
    }
    .profile-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 20%, rgba(255,255,255,.08), transparent 40%),
            radial-gradient(circle at 80% 80%, rgba(16,185,129,.25), transparent 50%);
        pointer-events: none;
    }
    .profile-hero .hero-body {
        position: relative;
        padding: 2rem 1.75rem 1.75rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1.25rem;
    }
    .profile-hero .avatar-ring {
        position: relative;
        width: 96px;
        height: 96px;
        border-radius: 50%;
        padding: 4px;
        background: linear-gradient(135deg, #10b981, #06b6d4);
        flex-shrink: 0;
    }
    .profile-hero .avatar-ring img,
    .profile-hero .avatar-ring .avatar-fallback {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        background: #0b1220;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 36px;
        font-weight: 700;
    }
    .profile-hero .avatar-upload-btn {
        position: absolute;
        right: -4px;
        bottom: -4px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #10b981;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0,0,0,.3);
        transition: transform .2s;
    }
    .profile-hero .avatar-upload-btn:hover { transform: scale(1.08); }
    .profile-hero .avatar-upload-btn input { display: none; }
    .profile-hero h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    .profile-hero .meta {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem .75rem;
        margin-top: .35rem;
        font-size: .875rem;
        opacity: .85;
    }
    .profile-hero .meta span {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .profile-hero .meta .material-symbols-rounded { font-size: 18px; }
    .profile-hero .role-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: rgba(16,185,129,.2);
        color: #6ee7b7;
        padding: .25rem .65rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        margin-top: .5rem;
    }

    .profile-tabs {
        display: flex;
        gap: .5rem;
        padding: .5rem;
        background: #fff;
        border-radius: .85rem;
        box-shadow: 0 6px 18px rgba(15,23,42,.08);
        margin: -1.25rem 1.5rem 0;
        position: relative;
        z-index: 2;
    }
    .profile-tabs .nav-tab {
        flex: 1;
        text-align: center;
        padding: .65rem 1rem;
        font-weight: 600;
        font-size: .875rem;
        color: #475569;
        border-radius: .65rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        transition: all .25s;
    }
    .profile-tabs .nav-tab:hover { color: #0f172a; background: #f1f5f9; }
    .profile-tabs .nav-tab.active {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        box-shadow: 0 6px 14px rgba(15,23,42,.18);
    }
    .profile-tabs .nav-tab .material-symbols-rounded { font-size: 18px; }

    .profile-section { padding: 1.75rem 1.5rem 1.5rem; }

    .profile-actions {
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px dashed #e2e8f0;
    }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; animation: fadeIn .25s ease; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<form action="/app/profile/update/{{ $user->id }}" method="POST" id="FormProfil" enctype="multipart/form-data" class="mb-4">
    @csrf
    @method('PUT')

    <div class="profile-hero">
        <div class="hero-body">
            <label class="avatar-ring" title="Ganti foto">
                @if($user->foto)
                    <img id="avatarPreview" src="{{ $avatar }}" alt="avatar">
                @else
                    <div class="avatar-fallback">{{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}</div>
                @endif
                <div class="avatar-upload-btn">
                    <span class="material-symbols-rounded" style="font-size:18px">photo_camera</span>
                    <input type="file" name="photo" accept="image/*" onchange="previewAvatar(this)">
                </div>
            </label>
            <div style="min-width:200px;flex:1;">
                <h1>{{ $user->nama ?? 'Pengguna' }}</h1>
                <div class="meta">
                    @if($user->email)
                        <span><span class="material-symbols-rounded">mail</span>{{ $user->email }}</span>
                    @endif
                    @if($user->telepon)
                        <span><span class="material-symbols-rounded">call</span>{{ $user->telepon }}</span>
                    @endif
                    @if($user->username)
                        <span><span class="material-symbols-rounded">person</span>{{ '@' . $user->username }}</span>
                    @endif
                </div>
                @if($user->jabatan)
                    <span class="role-badge">
                        <span class="material-symbols-rounded" style="font-size:14px">badge</span>
                        {{ $jabatans->firstWhere('id', (int)$user->jabatan)->nama_jabatan ?? $user->jabatan }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="profile-tabs" id="profileTabs">
        <div class="nav-tab active" data-tab="home">
            <span class="material-symbols-rounded">person</span> Informasi Pribadi
        </div>
        <div class="nav-tab" data-tab="access">
            <span class="material-symbols-rounded">lock</span> Keamanan Akun
        </div>
    </div>

    <div class="card mt-3">
        <div class="tab-panel active" data-panel="home">
            <div class="profile-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3 {{ $isFilled($user->nama) }}">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $user->nama }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3 {{ $isFilled($user->nik) }}">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" value="{{ $user->nik }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3 {{ $isFilled($user->jabatan) }}">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan" class="form-control select2">
                                <option value="">— Pilih jabatan —</option>
                                @foreach (($jabatans ?? collect()) as $j)
                                    <option value="{{ $j->id }}" {{ (string)$user->jabatan === (string)$j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                                                <select name="jenis_kelamin" class="form-control select2">
                            <option value="L" {{ ($user->jenis_kelamin ?? '')=='L'?'selected':'' }}>Laki-laki</option>
                            <option value="P" {{ ($user->jenis_kelamin ?? '')=='P'?'selected':'' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3 {{ $isFilled($user->email) }}">
                            <label class="form-label">Surel</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3 {{ $isFilled($user->telepon) }}">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="telepon" value="{{ $user->telepon }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group input-group-outline mb-3 {{ $isFilled($user->alamat) }}">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" class="form-control">{{ $user->alamat }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <button type="reset" class="btn btn-secondary">
                        <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle">refresh</span>
                        Atur Ulang
                    </button>
                    <button type="button" class="btn bg-gradient-info" data-type="home">
                        <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle">save</span>
                        Perbarui Data
                    </button>
                </div>
            </div>
        </div>

        <div class="tab-panel" data-panel="access">
            <div class="profile-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3 {{ $isFilled($user->username) }}">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" name="username" value="{{ $user->username }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <button type="button" class="btn bg-gradient-dark" data-type="access">
                        <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle">save</span>
                        Perbarui Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const r = new FileReader();
            r.onload = e => {
                const img = document.getElementById('avatarPreview');
                if (img) {
                    img.src = e.target.result;
                } else {
                    const ring = document.querySelector('.avatar-ring');
                    ring.innerHTML = `<img id="avatarPreview" src="${e.target.result}" alt="avatar">
                        <div class="avatar-upload-btn">
                            <span class="material-symbols-rounded" style="font-size:18px">photo_camera</span>
                            <input type="file" name="photo" accept="image/*" onchange="previewAvatar(this)">
                        </div>`;
                }
            };
            r.readAsDataURL(input.files[0]);
        }
    }

    $('.select2').select2({
        theme: 'bootstrap-5',
        allowClear: false,
        width: '100%'
    });

    document.querySelectorAll('#profileTabs .nav-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            document.querySelectorAll('#profileTabs .nav-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            document.querySelector(`.tab-panel[data-panel="${target}"]`).classList.add('active');
        });
    });

    document.querySelectorAll('[data-type]').forEach(btn => {
        btn.addEventListener('click', () => {
            const formData = new FormData(document.getElementById('FormProfil'));
            formData.append('section', btn.dataset.type);

            $.ajax({
                url: $('#FormProfil').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.msg || 'Profil berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => location.reload(), 1200);
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.errors
                        ? Object.values(xhr.responseJSON.errors).flat().join('\n')
                        : 'Cek kembali input';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                }
            });
        });
    });
</script>
@endsection
