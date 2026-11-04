<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kontak Dukungan Teknis
    |--------------------------------------------------------------------------
    |
    | Satu sumber kebenaran untuk informasi kontak yang dipakai di seluruh
    | aplikasi (halaman login, halaman tagihan, popup notifikasi, modal
    | dukungan teknis, footer invoice PDF).
    |
    | Nilai bisa dioverride lewat environment variable supaya mudah
    | disesuaikan per deployment tanpa mengubah kode.
    |
    */

    'nama' => env('SUPPORT_NAMA', 'Technical Support ABT'),

    'email' => env('SUPPORT_EMAIL', 'astabratamgl@gmail.com'),

    'telepon' => env('SUPPORT_TELEPON', '+62 882-0066-44656'),

    // Nomor dalam format international tanpa spasi/plus, untuk tautan wa.me.
    'whatsapp' => env('SUPPORT_WHATSAPP', '62882006644656'),

    /*
    | Email tagihan/Invoice (dipakai pada invoice PDF & halaman tagihan).
    | Dipisahkan dari email dukungan teknis karena kadangkala berbeda.
    */
    'email_tagihan' => env('SUPPORT_EMAIL_TAGIHAN', 'info@astabratagroup.com'),

    'telepon_tagihan' => env('SUPPORT_TELEPON_TAGIHAN', '(0293) 3195558'),

    'perusahaan' => env('SUPPORT_PERUSAHAAN', 'PT. Asta Brata Teknologi'),

    'situs' => env('SUPPORT_SITUS', 'astabratagroup.com'),

];
