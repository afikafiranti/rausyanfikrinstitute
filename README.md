# Website Alumni & Kajian — Rausyan Fikr

## 📖 Tentang Proyek
Rausyan Fikr adalah website alumni kajian yang dikembangkan menggunakan **Laravel + Tailwind**.  
Tujuan utama proyek ini adalah:
1. Mengelola data alumni.
2. Memberikan akses materi kajian (YouTube) berdasarkan level.
3. Menyediakan sistem laporan kajian wilayah.

Pengembangan dilakukan bertahap dalam versi (incremental release):
- **V1** — Data Alumni (MVP)
- **V2** — Materi YouTube
- **V3** — Rekap Laporan Kajian

---

## ✨ Fitur Utama

### Versi 1 (V1) — Data Alumni
- Registrasi & Login.
- Profil Alumni (view/edit).
- Verifikasi oleh Koordinator Daerah + penetapan Level awal.
- Dashboard Data Alumni (tabel + chart).
- Impor & Ekspor CSV.
- Audit log perubahan kritikal.

### Versi 2 (V2) — Materi YouTube
- Mapping playlist YouTube ke level.
- Akses video sesuai level.
- Cache metadata video.
- (Opsional) progres menonton video.

### Versi 3 (V3) — Laporan Kajian
- Form laporan kajian (judul, pemateri, peserta, catatan, foto).
- Workflow review Koorda (approve/reject).
- Dashboard & ekspor laporan.

---

## 🛠️ Tumpukan Teknologi
- **Framework:** Laravel 12 (PHP 8.3)
- **Frontend:** Blade + Tailwind (Notus Tailwind)
- **Database:** MySQL/MariaDB
- **Chart:** Chart.js
- **Opsional:** Redis (cache/queue), Laravel Sanctum (API)

---

## 🚀 Instalasi Lokal

```bash
git clone https://github.com/afikafiranti/rausyanfikrinstitute.git
cd rausyan-fikr

composer install
npm install && npm run dev

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

php artisan serve
```

---

## 🌱 Seeder Awal
- **RoleSeeder** — role sistem (admin, koorda, alumni, super_admin)
- **LevelSeeder** — level akses default (Level 1,2,3)
- **WilayahSeeder** — struktur wilayah awal
- **DemoDataSeeder** (opsional) — data dummy alumni untuk testing

---

## 🗄️ Struktur Database (Ringkas)
- `users` (alumni & pengelola)
- `roles`, `role_user` (multi-role)
- `levels` (Level 1/2/3)
- `wilayah` (hierarki wilayah)
- `playlists`, `videos` (materi YouTube)
- `kajian_reports` (laporan kajian)
- `audit_logs` (jejak perubahan)

---

## 🤝 Kontribusi & Aturan Commit
- Fork repo → buat branch feature → Pull Request.
- Setiap hari **wajib commit minimal 1x** dengan format:
  ```text
  hari_<nomor> apa_yang_dikerjakan
  ```
  Contoh:
  - `hari_1 migrasi levels/wilayah/roles + seed awal`
  - `hari_4 edit profil + audit log`
  - `hari_8 impor CSV + error report`
  - `hari_H1 deploy staging + migrate --force`

---

## 📄 Lisensi
Proyek ini bersifat internal untuk komunitas alumni kajian.  
Hak cipta © 2025 Rausyan Fikr.
