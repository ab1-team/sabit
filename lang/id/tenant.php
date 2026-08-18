<?php

return [
    'app' => [
        'name'        => 'Konsol Tenant',
        'tagline'     => 'Panel kendali pusat',
        'greeting'    => 'Selamat datang kembali',
        'view_site'   => 'Lihat Aplikasi',
        'logout'      => 'Keluar',
        'logout_confirm_title' => 'Keluar dari Konsol Tenant?',
        'logout_confirm_text'  => 'Sesi Anda akan berakhir dan Anda harus masuk lagi.',
        'logout_confirm_yes'   => 'Ya, keluar',
        'logout_confirm_no'    => 'Batal',
        'language'    => 'Bahasa',
    ],

    'nav' => [
        'dashboard' => 'Beranda',
        'tenant'    => 'Tenant',
        'hak_akses'  => 'Hak Akses',
        'migrasi'   => 'Migrasi',
        'invoice'   => 'Invoice',
        'transaksi' => 'Transaksi',
    ],

    'dashboard' => [
        'title'           => 'Beranda',
        'subtitle'        => 'Ringkasan operasional dari seluruh sekolah tenant.',
        'filter_all'      => 'Semua Sekolah',
        'filter_label'    => 'Filter Sekolah',
        'filter_caption'  => 'Data ditampilkan dari seluruh sekolah tenant.',
        'filter_select'   => 'Pilih Sekolah',

        'stats' => [
            'total_schools'   => 'Total Sekolah',
            'total_owners'    => 'Total Pemilik',
            'new_schools'     => 'Sekolah Baru',
            'active_schools'  => 'Sekolah Aktif',
            'invoice_total'   => 'Total Invoice',
            'invoice_paid'    => 'Invoice Lunas',
            'invoice_open'    => 'Invoice Belum Lunas',
            'nominal_total'   => 'Total Nominal',
            'this_month'      => 'Bulan Ini',
            'this_year'       => 'Tahun Ini',
        ],

        'chart' => [
            'income_title'    => 'Pendapatan Invoice',
            'income_subtitle' => '6 bulan terakhir',
            'tenant_title'    => 'Distribusi Sekolah',
            'tenant_subtitle' => 'Berdasarkan status langganan',
            'no_data'         => 'Belum ada data',
        ],

        'tables' => [
            'recent_invoices' => 'Invoice Terbaru',
            'view_all'        => 'Lihat semua',
            'recent_schools'  => 'Sekolah Terbaru',
            'recent_payments' => 'Pembayaran Terbaru',
            'recent_owners'   => 'Pemilik Terbaru',
            'no_data'         => 'Belum ada data.',
            'col_jenis'       => 'Jenis',
            'col_tanggal'     => 'Tanggal',
            'col_sekolah'     => 'Sekolah',
            'col_pemilik'     => 'Pemilik',
            'col_nominal'     => 'Nominal',
            'col_status'      => 'Status',
            'col_aksi'        => 'Aksi',
            'col_nama'        => 'Nama',
            'col_email'       => 'Email',
            'col_domain'      => 'Domain',
            'col_dibuat'      => 'Dibuat',
            'status_paid'     => 'Lunas',
            'status_unpaid'   => 'Belum Lunas',
            'status_active'   => 'Aktif',
            'status_inactive' => 'Non-Aktif',
            'detail'          => 'Detail',
        ],

        'actions' => [
            'add_school'      => 'Tambah Sekolah',
            'view_schools'    => 'Lihat Semua Sekolah',
            'manage_access'   => 'Kelola Hak Akses',
            'placeholder'     => 'Akan segera tersedia',
        ],
    ],
];
