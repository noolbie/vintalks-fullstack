# Dokumentasi VinTalks (Laravel 12)

## 1. Lokasi folder utama

```
D:\laragon\www\vintalks
├── app/
│   ├── Models/               → Model (data & relasi tabel)
│   ├── Http/Controllers/     → Controller (logika tiap halaman), dibagi per role:
│   │   ├── Admin/            → dashboard, users, mentors, topics, bookings, payments, settings
│   │   ├── Mentor/           → dashboard, profile, availability, bookings, hasil konsultasi
│   │   └── Participant/      → dashboard, mentors, bookings, payments, documents, profile
│   ├── Auth/                 → AuthController (login & register peserta)
│   ├── Services/             → Layanan inti (booking, slot, pembayaran, dokumen, konsultasi)
│   ├── Enums/                → Status login (booking, payment, metode bayar, dll)
│   ├── Notifications/        → Email/notifikasi (pembayaran, booking, pengingat)
│   ├── Policies/             → Aturan akses dokumen & data
│   ├── Http/Requests/        → Validasi form
│   └── Console/Commands/     → Tugas terjadwal (pengingat sesi, mark session selesai)
├── routes/web.php            → SEMUA route aplikasi
├── config/vintalks.php       → Konfigurasi khusus VinTalks (mode bayar, dll)
├── database/
│   ├── migrations/           → Struktur tabel
│   ├── seeders/              → Data demo (roles, akun, mentor, booking, settings)
│   └── factories/            → Data palsu untuk test
├── resources/views/          → File tampilan (Blade .blade.php), dibagi per role
│   ├── layouts/              → template halaman (app = layout utama, auth = layout login)
│   ├── components/           → komponen kecil (mis. sidenav-link)
│   ├── admin/ mentor/ participant/ auth/ landing/
├── public/                   → File statis (logo, favicon) & index.php
├── tests/                    → Feature tests (otomatis)
├── .env                      → Konfigurasi lokal (DB, APP_URL, mode bayar) — JANGAN dibagikan
└── composer.json / package.json
```

---

## 2. Alur MVC 

```
URL /admin/payments/1
   → routes/web.php mencocokkan route admin.payments.show
   → Admin\PaymentController@show(Payment $payment)   ← Controller
   → Payment::load(...)                               ← Model (ambil data dari DB)
   → return view('admin.payments.show', ...)          ← View (render HTML)
```

- **Route** menentukan URL mana jalan ke controller mana, plus cek hak akses role (mis. `role:admin`).
- **Controller** menerima request, memproses (bisa panggil Service), lalu mengirim data ke View.
- **Model** komunikasi dengan tabel database + relasi (mis. `Booking belongsTo MentorProfile`).
- **View** hanya menampilkan data (HTML + Blade `{{ }}`).
- **Service** = logika bisnis yang dipakai banyak controller (BookingService, PaymentService, dll).

---

## 3. Ringkasan Model (`app/Models`)

| Model | Fungsi utama |
|---|---|
| `User` | Akun login semua pengguna; punya role participant/mentor/admin (Spatie Permission) |
| `ParticipantProfile` | Data profil peserta (nomor WA, dll) |
| `MentorProfile` | Data mentor (nama tampilan, keahlian, harga, bio) |
| `MentorAvailability` | Slot jadwal mentor (tanggal + jam mulai/selesai) |
| `Booking` | Sesi konsultasi + status booking/pembayaran; ada helper `price_formatted` |
| `Payment` | Pembayaran + status (`waiting_verification`, `verified`, dst), label metode bayar |
| `Document` | Dokumen upload (bukti bayar, syarat sesi, hasil konsultasi) |
| `ConsultationRequirement` | Syarat konsultasi per booking |
| `ConsultationResult` | Hasil konsultasi + link meeting |
| `ConsultationResultDocument` | Dokumen hasil konsultasi |
| `Topic` | Topik/keahlian mentor |
| `Setting` | Pengaturan key-value (nomor rekening, metode bayar) |

---

## 4. Ringkasan Controller (`app/Http/Controllers`)

- **Admin/**: kelola user, mentor, topik, booking, verifikasi/tolak pembayaran, pengaturan (`SettingsController`).
- **Mentor/**: atur jadwal, lihat booking, input hasil konsultasi, kelola profil.
- **Participant/**: cari mentor, booking slot, upload bukti bayar, upload dokumen, lihat hasil konsultasi.
- **Auth/**: `AuthController` — halaman login/register.
- **HomeController**: landing page.
- **DocumentController** & **ConsultationResultDocumentController**: download dokumen privat via signed URL.

---

## 5. Ringkasan View (`resources/views`)

- `layouts/app.blade.php` → layout utama (sidebar + header). Sidebar berbeda otomatis sesuai role.
- `layouts/auth.blade.php` → tampilan login/register.
- `components/sidenav-link.blade.php` → item menu sidebar.
- File per halaman ikut nama controller, mis. `admin/payments/show.blade.php` untuk halaman detail pembayaran admin, `participant/payments/create.blade.php` untuk form bayar peserta.

---

## 6. Route (`routes/web.php`)

Semua route ada di SATU file. Area route bisa dibedakan dari nama & prefix:

| Area | Prefix URL | Nama route | Hak akses |
|---|---|---|---|
| Publik | `/`, `/login`, `/register` | `home`, `login`, `register` | tamu |
| Participant | `/participant/*` | `participant.*` | role participant |
| Mentor | `/mentor/*` | `mentor.*` | role mentor |
| Admin | `/admin/*` | `admin.*` | role admin |
| Dokumen privat | `/documents/*`, `/consultation-results/documents/*` | signed URL | yang punya/pihak terkait |

Cek daftar route: `php artisan route:list`

---

## 7. Konfigurasi penting (`.env`)

```
APP_URL=http://vintalks.test        # URL di browser/dev
DB_CONNECTION=mysql                 # dev pakai MySQL lokal (database: vintalks)
VINTALKS_PAYMENT_MODE=upload        # upload = bukti bayar diupload ke web (mode 2)
MAIL_MAILER=log                     # email dikirim ke log (dev)
```

---

## 8. Akun demo & data awal

Semua password: `password`
- Admin: `admin@vintalks.net`

- Mentor (Dian Arif Saputra): `diyanarif@vintalks.net`

- Participant (Kurt Cobain): `participant@vintalks.net`
Data demo (mentor, slot, topik, booking, setting rekening) dibuat otomatis oleh
`database/seeders/DatabaseSeeder.php`.

---

## 9. Menjalankan test

```
php artisan test
```

Test memakai database SQLite sementara (file `phpunit.xml`), jadi tidak mengganggu
data di MySQL. Saat ini 29 test.

---

## 10. Cara pindah laptop

> Dari = laptop lama. Ke = laptop baru (sudah ada PHP 8.2+ & Composer).

**Langkah 1 — kopi project (tanpa file besar)**

Folder yang TIDAK perlu dikopi (bisa langsung dibuat ulang):
- `vendor/` (dibuat ulang dengan composer)
- `node_modules/`
- `.env` (file ini khusus per mesin — buat ulang)
- `storage/logs/*.log`, `storage/app/private/*`, `bootstrap/cache/*`

Cara paling mudah: kopi seluruh folder `vintalks`, lalu jalankan perintah di bawah
(perintah "buat ulang" ini lebih aman daripada menyalin file besar).

**Langkah 2 — di laptop baru**

```
cd D:\laragon\www\vintalks

# 1. Pasang semua dependensi
composer install

# 2. Buat file .env dari template
copy .env.example .env

# 3. Generate kunci aplikasi (random, wajib)
php artisan key:generate

# 4. Sesuaikan .env (DB MySQL):
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_DATABASE=vintalks
#    DB_USERNAME=root
#    DB_PASSWORD=
#    APP_URL=http://vintalks.test  (atau isi sendiri)

# 5. Bikin database dulu di phpMyAdmin/server: buat database baru bernama "vintalks"

# 6. Buat semua tabel + data demo
php artisan migrate:fresh --seed --force

# 7. (opsional) bersihkan cache
php artisan config:clear
php artisan cache:clear
```

**Selesai.** Buka di browser (mengikuti struktur Laragon):
`http://localhost/vintalks/public/`

> Kalau sub-folder beda (mis. bukan `vintalks`), sesuaikan saja nama di URL.
> Pastikan folder project berada di dalam `D:\laragon\www\` supaya dikenali Laragon.

**Troubleshooting singkat**
- `tidak bisa cari vendor` → jalankan `composer install`.
- `No application encryption key` → `php artisan key:generate`.
- `Table not found` → `php artisan migrate:fresh --seed --force`.
- Halaman kosong/500 → cek `.env` `APP_DEBUG=true`, baca `storage/logs/laravel.log`.
