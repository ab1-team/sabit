{{--
    Partial: daftar pilihan icon untuk form fasilitas / struktur / dll.
    Dipakai via include:
        @include('admin-landing._komponen._pilih-icon', ['selectId' => 'iconSelect1'])

    Mengembalikan <option> kosong + <option> icon Bootstrap Icons + FontAwesome.
    Class `lp-icon-select` dipakai untuk inisialisasi Select2 dengan
    templateResult/templateSelection yang merender icon di opsi pilihan.
--}}
@php
    $icons = [
        // ---- Bootstrap Icons (prefix bi-) ----
        ['bi', 'building',         'Gedung / Bangunan'],
        ['bi', 'apartment',        'Apartemen'],
        ['bi', 'house',            'Rumah'],
        ['bi', 'easel',            'Papan / Kelas'],
        ['bi', 'mortarboard',      'Topi Toga'],
        ['bi', 'book',             'Buku'],
        ['bi', 'books',            'Koleksi Buku'],
        ['bi', 'library',          'Perpustakaan'],
        ['bi', 'journal-text',     'Jurnal'],
        ['bi', 'cpu',              'CPU / Lab Komputer'],
        ['bi', 'pc-display',       'PC Desktop'],
        ['bi', 'laptop',           'Laptop'],
        ['bi', 'wifi',             'Wi-Fi'],
        ['bi', 'globe',            'Internet / Global'],
        ['bi', 'lightbulb',        'Lampu / Ide'],
        ['bi', 'lamp',             'Lampu'],
        ['bi', 'thermometer',      'AC / Suhu'],
        ['bi', 'fan',              'Kipas / Ventilasi'],
        ['bi', 'droplet',          'Air'],
        ['bi', 'cup-hot',          'Kantin / Minuman'],
        ['bi', 'cup-straw',        'Minuman'],
        ['bi', 'basket',           'Basket'],
        ['bi', 'trophy',           'Trofi / Prestasi'],
        ['bi', 'medal',            'Medali'],
        ['bi', 'star-fill',        'Bintang'],
        ['bi', 'flag',             'Bendera'],
        ['bi', 'megaphone',        'Pengumuman'],
        ['bi', 'mic',              'Mikrofon'],
        ['bi', 'camera',           'Kamera'],
        ['bi', 'palette',          'Seni / Warna'],
        ['bi', 'music-note-beamed','Musik'],
        ['bi', 'people',           'Orang / Siswa'],
        ['bi', 'person-arms-up',   'Aktivitas'],
        ['bi', 'bicycle',          'Sepeda'],
        ['bi', 'bus-front',        'Bus / Transport'],
        ['bi', 'tree',             'Pohon / Taman'],
        ['bi', 'flower1',          'Bunga / Taman'],
        ['bi', 'geo-alt',          'Lokasi'],
        ['bi', 'shield-check',     'Keamanan'],
        ['bi', 'first-aid',        'P3K / UKS'],
        ['bi', 'hospital',         'Ruang UKS'],
        ['bi', 'piggy-bank',       'Tabungan'],
        ['bi', 'cash-coin',        'Keuangan'],
        ['bi', 'calculator',       'Kalkulator'],
        ['bi', 'clipboard-data',   'Data / Nilai'],

        // ---- FontAwesome 6 Solid (prefix fa-solid fa-) ----
        ['fa-solid', 'fa-school',           'Sekolah'],
        ['fa-solid', 'fa-graduation-cap',    'Wisuda'],
        ['fa-solid', 'fa-chalkboard',       'Papan Tulis'],
        ['fa-solid', 'fa-chalkboard-user',  'Pengajar'],
        ['fa-solid', 'fa-book-open',        'Buku Terbuka'],
        ['fa-solid', 'fa-flask',            'Lab IPA'],
        ['fa-solid', 'fa-microscope',       'Mikroskop'],
        ['fa-solid', 'fa-laptop-code',      'Lab Komputer'],
        ['fa-solid', 'fa-wifi',             'Wi-Fi'],
        ['fa-solid', 'fa-bolt',             'Listrik'],
        ['fa-solid', 'fa-faucet',           'Air'],
        ['fa-solid', 'fa-fire-extinguisher','Pemadam'],
        ['fa-solid', 'fa-medkit',           'P3K'],
        ['fa-solid', 'fa-bus-school',       'Bus Sekolah'],
        ['fa-solid', 'fa-bicycle',          'Sepeda'],
        ['fa-solid', 'fa-futbol',           'Olahraga Bola'],
        ['fa-solid', 'fa-basketball',       'Basket'],
        ['fa-solid', 'fa-volleyball',       'Voli'],
        ['fa-solid', 'fa-person-running',   'Lari'],
        ['fa-solid', 'fa-music',            'Musik'],
        ['fa-solid', 'fa-palette',          'Seni'],
        ['fa-solid', 'fa-camera',           'Kamera'],
        ['fa-solid', 'fa-couch',            'Ruang Tamu'],
        ['fa-solid', 'fa-utensils',         'Kantin'],
        ['fa-solid', 'fa-mug-hot',          'Minuman'],
        ['fa-solid', 'fa-tree',             'Pohon'],
        ['fa-solid', 'fa-leaf',             'Daun / Hijau'],
        ['fa-solid', 'fa-shield-halved',    'Keamanan'],
        ['fa-solid', 'fa-door-open',        'Pintu'],
        ['fa-solid', 'fa-lightbulb',        'Lampu'],
        ['fa-solid', 'fa-trophy',           'Trofi'],
        ['fa-solid', 'fa-medal',            'Medali'],
        ['fa-solid', 'fa-star',             'Bintang'],
        ['fa-solid', 'fa-flag',             'Bendera'],
        ['fa-solid', 'fa-users',            'Siswa'],
        ['fa-solid', 'fa-user-graduate',    'Alumni'],
        ['fa-solid', 'fa-globe',            'Global'],
        ['fa-solid', 'fa-map-location-dot', 'Lokasi'],
        ['fa-solid', 'fa-comments',         'Diskusi'],
        ['fa-solid', 'fa-megaphone',        'Pengumuman'],
        ['fa-solid', 'fa-calculator',       'Kalkulator'],
        ['fa-solid', 'fa-coins',            'Keuangan'],
    ];
@endphp
<select class="form-select lp-icon-select" id="{{ $selectId ?? 'iconSelect' }}" data-placeholder="Pilih icon...">
    <option value=""></option>
    @foreach ($icons as [$prefix, $name, $label])
        @php
            $value = $prefix === 'bi' ? 'bi-' . $name : 'fa-solid fa-' . $name;
            $class = $value;
            $isSelected = isset($selected) && $selected !== '' && trim($selected) === $value;
        @endphp
        <option value="{{ $value }}" data-icon-class="{{ $class }}" {{ $isSelected ? 'selected' : '' }}>{{ $label }} ({{ $value }})</option>
    @endforeach
</select>
