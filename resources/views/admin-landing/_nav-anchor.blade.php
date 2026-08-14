{{--
    Partial: sidebar nav-anchor untuk halaman /app/admin-landing/pengaturan.
    Mengikuti pola halaman /app/pengaturan/sop: daftar anchor yang
    highlight-nya dikontrol via CSS :has(:target) -- tidak butuh JS
    scroll-spy. Klik anchor akan mengubah hash URL ke #lp-section-xxx
    dan menampilkan satu panel konten yang sesuai.
--}}
@php
    // Daftar section. 'id' akan jadi elemen target (id="lp-section-..."),
    // 'icon' diambil dari Material Symbols agar seragam dengan gaya halaman.
    $lpSections = [
        ['id' => 'hero',       'label' => 'Hero Beranda',     'icon' => 'image'],
        ['id' => 'identitas',  'label' => 'Identitas Sekolah','icon' => 'badge'],
        ['id' => 'kontak',     'label' => 'Kontak',           'icon' => 'contact_phone'],
        ['id' => 'medsos',     'label' => 'Media Sosial',     'icon' => 'share'],
        ['id' => 'background', 'label' => 'Background Tema',  'icon' => 'wallpaper'],
        ['id' => 'warna',      'label' => 'Warna Tema',       'icon' => 'palette'],
        ['id' => 'sambutan',   'label' => 'Sambutan',         'icon' => 'person_celebrate'],
    ];

    // (Active state sepenuhnya dikontrol CSS :has(:target) — tidak
    // butuh parsing hash di PHP. Block ini hanya placeholder.)
@endphp

<aside class="lp-pengaturan-aside" aria-label="Navigasi bagian pengaturan">
    <div class="card lp-anchor-card shadow-sm">
        <div class="card-header">
            <div class="lp-anchor-header-icon">
                <span class="material-symbols-rounded">bookmarks</span>
            </div>
            <div>
                <h5>Bagian Pengaturan</h5>
                <div class="sub">Pilih modul pengaturan</div>
            </div>
        </div>
        <div class="card-body d-grid gap-1 lp-anchor-menu">
            @foreach ($lpSections as $sec)
                <a href="#lp-section-{{ $sec['id'] }}" class="lp-anchor-link">
                    <span class="mi"><span class="material-symbols-rounded">{{ $sec['icon'] }}</span></span>
                    <span>{{ $sec['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</aside>
