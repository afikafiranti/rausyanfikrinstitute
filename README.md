# Website Alumni & Kajian — Rausyan Fikr

## 📖 Tentang Proyek
Rausyan Fikr adalah website alumni kajian yang dikembangkan menggunakan **Laravel + Tailwind**.  
Tujuan utama proyek ini adalah:
1. Mengelola data alumni.
2. Memberikan akses materi kajian (YouTube) berdasarkan level.
3. Menyediakan sistem laporan kajian wilayah.
4. Menyediakan informasi organisasi, refleksi, artikel, agenda, tim, galeri dan kontak untuk pengunjung publik.

Pengembangan dilakukan bertahap dalam versi (incremental release):
- **V1** — Data Alumni (MVP)
- **V2** — Materi YouTube
- **V3** — Rekap Laporan Kajian

Status 2026-10-17: fitur inti V1–V3 sudah tersedia pada hasil integrasi A+B di `develop`. A0–A2 berstatus DONE; B0–B2 dan integrasi berstatus REVIEW untuk tindak lanjut rilis. Ketersediaan fitur belum menjadi bukti kesiapan produksi.

Matriks cakupan dan batas fitur ada di [PRD](docs/aturan_pengembangan/PRD.md#cakupan-dan-status-implementasi), urutan pekerjaan di [PLAN](docs/aturan_pengembangan/PLAN.md#tindak-lanjut-setelah-integrasi), dan bukti penyelesaian di [TASK](docs/aturan_pengembangan/TASK.md).

---

## ✨ Fitur Utama

### Versi 1 (V1) — Data Alumni
- Registrasi publik berstatus pending, login, reset password dan verifikasi email.
- Profil, foto, perubahan password, serta pengelolaan data alumni sesuai izin.
- Verifikasi oleh koorda/admin, pembatasan wilayah dan penetapan level awal.
- Pencarian, filter, sorting, pagination dan dashboard data alumni.
- Impor/ekspor CSV dan Excel `.xlsx`, template dengan nama wilayah, serta laporan baris impor gagal.
- Audit log perubahan kritikal dan notifikasi dalam aplikasi.

### Versi 2 (V2) — Materi YouTube
- Pengelolaan playlist/video oleh admin, pencarian dan detail materi.
- Akses playlist/video bagi akun aktif sesuai level; tautan menonton dibuka di YouTube.
- Judul dan durasi video diisi manual. Pengambilan/cache metadata YouTube otomatis masih ditunda.
- Progres menonton bersifat opsional dan masih ditunda; tabel/model yang ada belum menjadi fitur pengguna.

### Versi 3 (V3) — Laporan Kajian
- Draf, edit dan kirim laporan kajian dengan peserta laki-laki/perempuan, catatan serta foto privat.
- Review koorda/admin sesuai wilayah, approve/reject dengan alasan penolakan.
- Rekap dengan filter wilayah/status/tanggal dan ekspor CSV.
- Laporan rejected belum mendukung revisi atau pengiriman ulang.

### Tampilan Publik

- Tersedia: homepage, informasi tentang, statistik publikasi, daftar/detail refleksi, galeri, CTA masuk dan footer. Post terbit serta galeri ditampilkan dari database.
- Direncanakan dan dikonfirmasi pengguna pada 2026-10-17: agenda kajian, profil tim, informasi kontak resmi, artikel publik dan pelengkapan navigasi/CTA pendaftaran. Agenda saat ini masih berupa placeholder; template tim/kontak belum dipasang pada homepage aktif. Halaman artikel belum tersedia; halaman refleksi existing tetap tersedia.
- Task berikutnya: `PUB-01` (agenda), `PUB-02` (tim/kontak), `PUB-03` (navigasi, CTA Daftar dan konten publik final) dan `PUB-04` (daftar/detail artikel). Fitur tersebut tetap bagian cakupan produk.

Pengelolaan master role/wilayah/level melalui CRUD web khusus belum tersedia; rincian implementasinya perlu ditetapkan sebelum dikerjakan. Penetapan level akun melalui verifikasi/pengelolaan alumni sudah tersedia.

---

## 🛠️ Tumpukan Teknologi
- **Framework:** Laravel 12 (PHP 8.3)
- **Frontend:** Blade + Tailwind (Notus Tailwind)
- **Database:** MySQL/MariaDB
- **Chart:** Chart.js
- **Excel:** PhpSpreadsheet dan ekstensi ZIP PHP; kebutuhan platform mengikuti `composer.lock`.
- **Opsional:** Redis untuk cache/queue; Laravel Sanctum/API belum menjadi fitur yang diimplementasikan.

---

## 🚀 Instalasi Lokal

```bash
git clone https://github.com/afikafiranti/rausyanfikrinstitute.git
cd rausyanfikrinstitute

cp .env.example .env
# Isi database lokal terpisah pada .env terlebih dahulu.
composer install --prefer-dist --no-interaction
npm ci
# Hanya untuk instalasi lokal baru; jangan mengganti kunci environment existing.
php artisan key:generate

php artisan migrate
# Master data untuk database lokal baru.
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=LevelSeeder
php artisan db:seed --class=WilayahSeeder

# Jalankan Vite di terminal terpisah: npm run dev
php artisan serve
```

Gunakan PHP 8.3 dengan ekstensi sesuai lockfile, termasuk ZIP untuk Excel. Template email memakai mailer log; pengiriman email nyata memerlukan konfigurasi transport dan verifikasi tersendiri.

Jangan menjalankan seeder demo pada database berisi data nyata. Deployment staging, backup, dan pemulihan mengikuti
[runbook Jalur B](docs/aturan_pengembangan/RUNBOOK_STAGING_JALUR_B.md);
perintah instalasi lokal di atas bukan prosedur upgrade server.

---

## 🌱 Seeder Awal
- **RoleSeeder** — role sistem (admin, koorda, alumni, super_admin)
- **LevelSeeder** — level materi 1–4: dasar, menengah, lanjutan, akhir; terpisah dari role sistem
- **WilayahSeeder** — struktur wilayah awal
- **UserSeeder**, **PostSeeder**, **GallerySeeder** — data contoh untuk lokal/testing

`DatabaseSeeder` menjalankan master data dan hanya memanggil seeder contoh pada environment `local`/`testing`. `UserSeeder` juga membatasi dirinya ke kedua environment tersebut. Tidak ada kelas `DemoDataSeeder`.

---

## 🗄️ Struktur Database (Ringkas)
- `users` (alumni & pengelola)
- `roles`, `role_user` (multi-role)
- `levels` (level materi 1–4)
- `wilayah` (hierarki wilayah)
- `playlists`, `videos` (materi YouTube)
- `video_progress` (schema tersedia; fitur progres belum aktif)
- `kajian_reports` (laporan kajian)
- `audit_logs` (jejak perubahan)
- `notifications` (notifikasi dalam aplikasi)

---

## 🤝 Kontribusi & Aturan Commit
- Ikuti penugasan branch, kepemilikan file dan aturan integrasi pada [AGENTS.md](AGENTS.md).
- Baca [PRD](docs/aturan_pengembangan/PRD.md), [PLAN](docs/aturan_pengembangan/PLAN.md), [TASK](docs/aturan_pengembangan/TASK.md), [AUDIT](docs/aturan_pengembangan/AUDIT.md) dan [SOP](docs/aturan_pengembangan/SOP.md) sebelum mengubah fitur.
- Catat perubahan per jalur dan ajukan per batch A0/A1/A2 atau B0/B1/B2 sesuai rencana aktif; tindak lanjut publik memakai ID PUB-01–PUB-04.
- Automated test memakai database sendiri berakhiran `_testing`; jangan memakai database kerja. Runner dan persyaratan CI dijelaskan di [catatan Jalur B](docs/aturan_pengembangan/CATATAN_JALUR_B.md#pemakaian-environment-dan-runner).
- Aturan commit harian existing tetap berlaku: minimal satu commit per hari kerja dengan format `hari_<nomor> apa_yang_dikerjakan`, misalnya `hari_8 impor CSV dan laporan baris gagal`.

---

## 📄 Lisensi
Proyek ini bersifat internal untuk komunitas alumni kajian.  
Hak cipta © 2025 Rausyan Fikr.
