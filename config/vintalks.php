<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VinTalks configuration
    |--------------------------------------------------------------------------
    */

    // Zona waktu default aplikasi (bisa diubah lewat env VINTALKS_TIMEZONE).
    'timezone' => env('VINTALKS_TIMEZONE', 'Asia/Jakarta'),

    /*
    | Payment mode:
    | - upload : participant uploads payment proof directly on the website (MOD 2)
    | - google_form : participant is directed to a Google Form (MOD 1)
    */
    // Mode pembayaran: 'upload' (unggah bukti bayar di website) atau 'google_form' (arahkan peserta ke Google Form).
    'payment_mode' => env('VINTALKS_PAYMENT_MODE', 'upload'),

    // URL Google Form untuk mode pembayaran google_form (diisi lewat .env).
    'google_form_url' => env('VINTALKS_GOOGLE_FORM_URL'),

    // Nama perusahaan/brand yang dipakai di tampilan (mis. untuk judul email/notifikasi).
    'company_name' => 'VinTalks',
];