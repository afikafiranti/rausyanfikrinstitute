# Task Log Pengembangan

Dokumen ini mencatat pekerjaan yang sudah dilakukan, sedang berjalan, dan berikutnya. Setiap task wajib punya status yang jelas.

Status yang digunakan:
- `TODO`: belum dikerjakan.
- `IN_PROGRESS`: sedang dikerjakan.
- `DONE`: selesai dan sudah dicek.
- `BLOCKED`: tertahan oleh dependency atau keputusan.
- `REVIEW`: perlu review sebelum dianggap selesai.

## Ringkasan Status Saat Ini

Project berada pada fase:
- V1 Data Alumni: berjalan dan sebagian besar sudah aktif.
- V2 Materi Kajian: awal dan perlu perapian schema.
- V3 Laporan Kajian: awal dan perlu definisi workflow.

## Task Selesai

### DONE: Review Awal Project
Tanggal: 2026-07-19

Catatan:
- Project adalah aplikasi Laravel 12 berbasis Blade, Tailwind, dan Vite.
- Modul utama yang tersedia: landing page, auth, dashboard, alumni, profil, verifikasi, notifikasi, manajemen konten, materi awal, laporan awal.

### DONE: Identifikasi Status Tahap Pengembangan
Tanggal: 2026-07-19

Catatan:
- V1 alumni sudah paling matang.
- V2 materi dan V3 laporan masih perlu stabilisasi.

### DONE: Perbaikan Tambah Alumni Baru
Tanggal: 2026-07-19

Masalah:
- Form tambah alumni di halaman admin sebelumnya memakai `route('register')`.
- Alur register biasa melakukan login ke user baru, sehingga admin bisa terganti sesi.
- Pada percobaan awal, data terlihat tidak masuk.

Perbaikan:
- Menambahkan `AlumniController@store`.
- Menambahkan route `POST /alumni` dengan nama `alumni.store`.
- Mengubah form modal tambah alumni agar submit ke `route('alumni.store')`.
- Alumni baru dibuat dengan status `pending`.
- Role `alumni` otomatis ditautkan.
- Proses simpan dibungkus transaksi.
- Error validasi dan pesan sukses ditampilkan di halaman.

Verifikasi:
- `php -l app/Http/Controllers/AlumniController.php` lulus.
- `php artisan route:list --name=alumni.store` menampilkan route.
- Test insert via Laravel model dalam transaksi berhasil.
- User mengonfirmasi tambah data alumni baru sudah berhasil.

### DONE: Pembuatan Struktur Dokumentasi Pengembangan
Tanggal: 2026-07-19

File:
- `docs/aturan_pengembangan/PRD.md`
- `docs/aturan_pengembangan/PLAN.md`
- `docs/aturan_pengembangan/TASK.md`
- `docs/aturan_pengembangan/AUDIT.md`
- `docs/aturan_pengembangan/SOP.md`

### DONE: Instalasi Project Skills untuk Codex
Tanggal: 2026-07-19

Tujuan:
- Memasang skill yang relevan untuk pengembangan web Laravel/Blade/Tailwind, UX workflow, QA browser, debugging, TDD, riset, dan code review.

Skill terpasang:
- `ui-ux-pro-max`
- `ui-styling`
- `design-system`
- `browser-use`
- `remote-browser`
- `qa`
- `setup-matt-pocock-skills`
- `diagnosing-bugs`
- `tdd`
- `code-review`
- `implement`
- `codebase-design`
- `domain-modeling`
- `research`
- `handoff`
- `9router`
- `9router-chat`
- `9router-web-search`
- `9router-web-fetch`
- `shadcn`

Catatan:
- Skill dipasang sebagai project skills di `.agents/skills`.
- CLI juga membuat `skills-lock.json` sebagai lockfile instalasi skill.
- Repo `anomalyco/opencode` dicek, tetapi hanya skill `effect` yang terdeteksi dan tidak relevan untuk Laravel/Blade, sehingga tidak dipasang.
- Repo `shadcn-ui/ui` menyediakan skill `shadcn`; dipasang sebagai referensi pola komponen, bukan keputusan memakai React/shadcn runtime.

Verifikasi:
- `Get-ChildItem .agents/skills -Directory` menampilkan semua skill terpasang.
- `npx skills@latest list --agent codex --json` mengonfirmasi scope `project` dan agent `Codex`.

## Task Berikutnya Prioritas Urgent

### DONE: Tambah Test untuk Alumni Store
Prioritas: urgent
Tanggal mulai: 2026-07-22
Tanggal selesai: 2026-07-22
Owner/sub-agen: QA dan Test Engineer, Backend Laravel Engineer

Checklist:
- [x] Test admin bisa tambah alumni.
- [x] Test user baru mendapat role alumni.
- [x] Test user baru status pending.
- [x] Test email duplikat gagal validasi.
- [x] Test password confirmation gagal validasi.

Catatan:
- Test dibuat di `tests/Feature/AlumniStoreTest.php`.
- Seam yang diuji adalah HTTP `POST /alumni` sebagai admin.
- PHPUnit dikonfigurasi memakai database MySQL khusus testing `rf_db_testing` karena PHP lokal tidak memiliki driver `pdo_sqlite`.
- Migration playlist diperbaiki dari `is-active` menjadi `is_active` agar migration test suite dapat berjalan.

Verifikasi:
- `php artisan test --filter=AlumniStoreTest`: 3 passed, 19 assertions.
- `php artisan test`: 26 passed, 2 skipped, 76 assertions.

### DONE: Review Filter dan Empty State Daftar Alumni
Prioritas: tinggi
Tanggal mulai: 2026-07-22
Tanggal selesai: 2026-07-22
Owner/sub-agen: UX Workflow Designer, Frontend Blade dan Tailwind Engineer

Checklist:
- [x] Pastikan data baru terlihat jika filter kosong.
- [x] Tambah tombol reset filter jika belum ada.
- [x] Pastikan pesan kosong membedakan antara "belum ada data" dan "tidak cocok filter".

Catatan:
- Halaman daftar alumni sekarang menampilkan jumlah hasil dibanding total alumni.
- Filter aktif ditampilkan sebagai badge ringkas.
- Tombol reset muncul hanya saat filter aktif.
- Empty state filter aktif menjelaskan bahwa hasil tidak cocok dan memberi aksi reset.
- Empty state tanpa filter menjelaskan bahwa data alumni memang belum ada.
- UX review memakai skill `ui-ux-pro-max` untuk aturan empty state, deep linking query, form feedback, dan table handling.

Verifikasi:
- `php artisan test --filter=Alumni`: 5 passed, 28 assertions.
- `php artisan test`: 28 passed, 2 skipped, 85 assertions.

### DONE: Audit Migration dan Seeder
Prioritas: urgent
Tanggal mulai: 2026-07-22
Tanggal selesai: 2026-07-22
Owner/sub-agen: Database dan Data Integrity Specialist, Backend Laravel Engineer

Checklist:
- [x] Cek `users`.
- [x] Cek `roles` dan `role_user`.
- [x] Cek `levels`.
- [x] Cek `wilayah`.
- [x] Cek `playlists`.
- [x] Cek `videos`.
- [x] Cek `video_progress`.
- [x] Cek `kajian_reports`.
- [x] Cek nilai enum `ab`.
- [x] Cek `is-active` vs `is_active`.

Catatan:
- `migrate:fresh --seed --env=testing` berhasil pada database testing `rf_db_testing`.
- Migration `playlists` disinkronkan dengan model `Playlist` lewat kolom `tags` dan `is_active`.
- Migration baru ditambahkan untuk `videos`, `video_progress`, dan `kajian_reports` agar model yang sudah ada memiliki tabel database.
- `User::hasAnyRole()` ditambahkan karena middleware `ScopeWilayah` memakainya.
- `MateriController@index` disesuaikan agar memakai `is_active`, mengirim `$items`, dan membatasi playlist berdasarkan `level_id` user.
- View `materi.index` disesuaikan agar aman menampilkan relasi `level` dan status aktif.
- View `materi.show` dasar ditambahkan agar route detail materi tidak mengarah ke view yang tidak ada.
- Test `MateriIndexTest` ditambahkan untuk memastikan halaman materi menampilkan playlist aktif sesuai level user.

Verifikasi:
- `php artisan migrate:fresh --seed --env=testing`: berhasil.
- `php artisan test --filter=MateriIndexTest`: 1 passed, 5 assertions.
- `php artisan test`: 29 passed, 2 skipped, 90 assertions.

### DONE: Tambah Tombol Hapus Data Alumni
Prioritas: tinggi
Tanggal mulai: 2026-07-22
Tanggal selesai: 2026-07-22
Owner/sub-agen: Backend Laravel Engineer, UX Workflow Designer, QA dan Test Engineer

Checklist:
- [x] Tambah route hapus alumni.
- [x] Tambah controller destroy dengan proteksi data admin/koorda dan self-delete.
- [x] Tambah tombol hapus pada tabel alumni.
- [x] Tambah konfirmasi sebelum hapus.
- [x] Tambah automated test hapus alumni.
- [x] Jalankan test suite.

Catatan:
- Route `DELETE /alumni/{user}` ditambahkan sebagai `alumni.destroy`.
- Tombol hapus hanya tampil untuk user yang memiliki role `alumni`, bukan akun yang sedang login, dan bukan akun admin-like.
- Controller menolak hapus self-account, akun pengelola, dan user non-alumni dari endpoint ini.
- Penghapusan melepas relasi role lalu menghapus user dalam transaksi.
- Test `AlumniDestroyTest` ditambahkan untuk hapus alumni, self-delete ditolak, dan akun admin-like ditolak.

Verifikasi:
- `php artisan test --filter=Alumni`: 8 passed, 50 assertions.
- `php artisan test`: 32 passed, 2 skipped, 112 assertions.

### DONE: Perbaiki Upload Foto Profil Alumni
Prioritas: urgent
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: Backend Laravel Engineer, QA dan Test Engineer

Tujuan:
- Memastikan foto profil yang diunggah dari akun alumni terbaca dan tampil melalui accessor avatar.

Checklist:
- [x] Reproduksi bug dengan automated test.
- [x] Pastikan upload menyimpan file ke disk publik.
- [x] Pastikan kolom `photo_url` terisi.
- [x] Pastikan `avatar_url` membaca `photo_url`.
- [x] Jalankan test profil dan full test suite.

Catatan:
- Form desktop dan mobile profil sudah memakai `enctype="multipart/form-data"`.
- Controller profil sudah menyimpan upload ke `photo_url` dalam format `/storage/avatars/...`.
- Bug terjadi karena `User::getAvatarUrlAttribute()` belum membaca kolom `photo_url`.
- Accessor sekarang mendukung URL penuh, `/storage/...`, `storage/...`, dan path file di disk `public`.
- Regression test ditambahkan di `tests/Feature/ProfileTest.php`.

Verifikasi:
- `php artisan test --filter=ProfileTest`: 6 passed, 29 assertions.
- `php artisan test`: 33 passed, 2 skipped, 120 assertions.

### DONE: Petakan Role Akun dan Level Materi Alumni
Prioritas: urgent
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: Backend Laravel Engineer, Database dan Data Integrity Specialist, QA dan Test Engineer

Tujuan:
- Memisahkan konsep `roles` sebagai hak akses sistem dari `levels` sebagai tingkat materi/pembelajaran.
- Menghilangkan tampilan keliru seperti alumni berlevel `superadmin`.

Checklist:
- [x] Reproduksi bug dengan automated test detail alumni.
- [x] Koreksi data seeder level agar tidak memakai nama role sistem.
- [x] Tambah migration koreksi data level di database berjalan.
- [x] Tampilkan `Role Akun` dan `Level Materi` secara terpisah di detail alumni.
- [x] Jalankan migration lokal.
- [x] Jalankan test detail alumni dan full test suite.

Catatan:
- `roles`: `super_admin`, `admin`, `koorda`, `alumni`.
- `levels`: `Level 1/dasar`, `Level 2/menengah`, `Level 3/lanjutan`, `Level 4/akhir`.
- Halaman detail alumni sekarang menampilkan role alumni sebagai `Role Akun`, sementara level materi tampil sebagai `Level Materi`.
- Migration `2026_07_23_000004_normalize_learning_level_descriptions.php` mengubah data level lama yang sebelumnya memakai deskripsi role.

Verifikasi:
- `php artisan test --filter=AlumniDetailTest`: 2 passed, 12 assertions.
- `php artisan migrate`: berhasil.
- `php artisan test`: 35 passed, 2 skipped, 132 assertions.

### DONE: Bedakan Warna Badge Role Koorda
Prioritas: rendah
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: UX Workflow Designer, Frontend Blade dan Tailwind Engineer, QA dan Test Engineer

Tujuan:
- Membuat badge role `Koorda` lebih mudah dibedakan dari badge `Alumni` pada tabel daftar alumni.

Checklist:
- [x] Cek warna badge role di tabel alumni.
- [x] Ubah warna `Koorda` dari emerald ke violet.
- [x] Tambah test agar kelas warna badge Koorda tetap berbeda dari Alumni.
- [x] Jalankan test terkait dan full test suite.

Catatan:
- `Alumni` tetap memakai `bg-sky-200 text-sky-800`.
- `Koorda` sekarang memakai `bg-violet-200 text-violet-800`.
- Perubahan dilakukan di `resources/views/alumni/index.blade.php`.

Verifikasi:
- `php artisan test --filter=AlumniIndexFilterTest`: 3 passed, 13 assertions.
- `php artisan test`: 36 passed, 2 skipped, 136 assertions.

### DONE: Akses Hapus Akun Alumni dan Koorda
Prioritas: tinggi
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: Backend Laravel Engineer, Frontend Blade dan Tailwind Engineer, QA dan Test Engineer

Tujuan:
- Memberikan akses kepada `super_admin` dan `admin` untuk menghapus akun dengan role `alumni` atau `koorda`.
- Tetap melindungi akun `super_admin`, `admin`, dan akun yang sedang digunakan.

Checklist:
- [x] Tambah test admin bisa menghapus alumni.
- [x] Tambah test admin bisa menghapus koorda.
- [x] Tambah test super admin bisa menghapus alumni dan koorda.
- [x] Tambah test koorda tidak bisa menghapus akun.
- [x] Tambah test admin/super admin tidak bisa dihapus dari daftar alumni.
- [x] Sesuaikan tombol hapus pada tabel alumni.
- [x] Jalankan test terkait dan full test suite.

Catatan:
- Endpoint `alumni.destroy` sekarang membatasi aktor ke role `super_admin` dan `admin`.
- Target hapus dibatasi ke role `alumni` dan `koorda`.
- Tombol hapus hanya tampil untuk target yang memang boleh dihapus.
- Penghapusan tetap melepas relasi role sebelum menghapus user.

Verifikasi:
- `php artisan test --filter=AlumniDestroyTest`: 7 passed, 59 assertions.
- `php artisan test`: 40 passed, 2 skipped, 173 assertions.

### DONE: Akses Ubah Level Materi Alumni dan Koorda
Prioritas: tinggi
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: Backend Laravel Engineer, Frontend Blade dan Tailwind Engineer, QA dan Test Engineer

Tujuan:
- Memberikan akses kepada `super_admin` dan `admin` untuk mengubah `Level Materi` akun dengan role `alumni` atau `koorda`.
- Tetap memisahkan `Role Akun` dari `Level Materi`.

Checklist:
- [x] Tambah route update level alumni.
- [x] Tambah validasi aktor hanya `super_admin` dan `admin`.
- [x] Batasi target hanya akun `alumni` dan `koorda`.
- [x] Lindungi target `super_admin` dan `admin`.
- [x] Tambah form `Ubah Level Materi` pada halaman detail alumni.
- [x] Catat perubahan level ke `audit_logs`.
- [x] Tambah automated test permission dan tampilan form.
- [x] Jalankan test terkait dan full test suite.

Catatan:
- Route baru: `PATCH /alumni/{user}/level` dengan nama `alumni.level.update`.
- Form update level hanya muncul di detail alumni jika aktor adalah `super_admin` atau `admin`, dan target adalah `alumni` atau `koorda`.
- Perubahan level memakai field `users.level_id` dan tidak mengubah role akun.

Verifikasi:
- `php artisan route:list --name=alumni.level.update`: route tersedia.
- `php artisan test --filter=AlumniDetailTest`: 8 passed, 44 assertions.
- `php artisan test`: 46 passed, 2 skipped, 205 assertions.

### DONE: Tabel Alumni Menampilkan Level Materi
Prioritas: tinggi
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: Database dan Data Integrity Specialist, Frontend Blade dan Tailwind Engineer, QA dan Test Engineer

Tujuan:
- Mengganti kolom `Status` pada halaman daftar alumni menjadi `Level Materi`.
- Menetapkan kategori level menjadi `dasar`, `menengah`, `lanjutan`, `akhir`.
- Memastikan akun `admin` dan `super_admin` berada pada level materi `akhir`.

Checklist:
- [x] Ubah header tabel alumni dari `Status` menjadi `Level Materi`.
- [x] Tampilkan `users.level.description` pada kolom baru.
- [x] Ubah `LevelSeeder` ke kategori final lowercase.
- [x] Ubah `UserSeeder` agar admin dan super admin memakai `level_id` level `akhir`.
- [x] Tambah migration data untuk update database berjalan.
- [x] Jalankan migration lokal.
- [x] Tambah dan jalankan automated test.

Catatan:
- Migration baru: `2026_07_23_000005_update_final_learning_level_and_admin_assignments.php`.
- Kategori final: `dasar`, `menengah`, `lanjutan`, `akhir`.
- Akun admin dan super admin tetap memakai role masing-masing; perubahan ini hanya menyentuh `level_id`.

Verifikasi:
- `php artisan migrate`: berhasil.
- Cek database lokal: Arif Husain/admin dan Nur Afika/super_admin berada di level `akhir`.
- `php artisan test`: 48 passed, 2 skipped, 212 assertions.

### DONE: Akses Edit Data Alumni dan Koorda
Prioritas: tinggi
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: Backend Laravel Engineer, Frontend Blade dan Tailwind Engineer, QA dan Test Engineer

Tujuan:
- Memberikan akses kepada `super_admin` dan `admin` untuk mengedit data profil akun dengan role `alumni` atau `koorda`.
- Tetap melindungi akun `super_admin` dan `admin` dari fitur edit alumni.

Checklist:
- [x] Tambah route edit data alumni.
- [x] Tambah route update data alumni.
- [x] Tambah halaman form edit data alumni.
- [x] Tambah tombol edit di daftar dan detail alumni.
- [x] Batasi aktor ke `super_admin` dan `admin`.
- [x] Batasi target ke `alumni` dan `koorda`.
- [x] Catat perubahan ke `audit_logs`.
- [x] Tambah automated test permission dan update data.
- [x] Jalankan test terkait dan full test suite.

Catatan:
- Route baru: `GET /alumni/{user}/edit` sebagai `alumni.edit`.
- Route baru: `PATCH /alumni/{user}` sebagai `alumni.update`.
- Field yang dapat diedit: nama, email, WA, angkatan, pekerjaan, wilayah, tempat/tanggal lahir, pendidikan terakhir, kampus, status pernikahan, dan AB.
- Role akun dan level materi tidak diubah dari form edit data ini; level tetap memakai fitur `Ubah Level Materi`.

Verifikasi:
- `php artisan route:list --name=alumni.edit`: route tersedia.
- `php artisan route:list --name=alumni.update`: route tersedia.
- `php artisan test --filter=AlumniEditTest`: 5 passed, 42 assertions.
- `php artisan test`: 53 passed, 2 skipped, 254 assertions.

### DONE: Tombol Aksi Alumni Icon Only
Prioritas: rendah
Tanggal mulai: 2026-07-23
Tanggal selesai: 2026-07-23
Owner/sub-agen: UX Workflow Designer, Frontend Blade dan Tailwind Engineer, QA dan Test Engineer

Tujuan:
- Menghapus teks visual pada tombol aksi tabel alumni sehingga tombol hanya menampilkan icon.
- Tetap menjaga aksesibilitas tombol lewat `aria-label` dan tooltip browser.

Checklist:
- [x] Hapus teks visual tombol `Detail`, `Edit`, dan `Hapus`.
- [x] Pertahankan icon mata, pena, dan tempat sampah.
- [x] Tambahkan `aria-label` dan `title` pada tombol icon-only.
- [x] Tambahkan automated test markup tombol aksi.
- [x] Jalankan test terkait dan full test suite.

Catatan:
- Tombol aksi dibuat ukuran tetap `h-8 w-8` agar konsisten pada tabel.
- Teks tidak tampil di UI, tetapi nama aksi tetap tersedia untuk screen reader dan tooltip.

Verifikasi:
- `php artisan test --filter=AlumniIndexFilterTest`: 5 passed, 27 assertions.
- `php artisan test`: 54 passed, 2 skipped, 264 assertions.

### TODO: Perbaiki Modul Materi
Prioritas: tinggi setelah audit schema

Checklist:
- Sesuaikan field controller dengan database.
- Perbaiki variable typo pada `MateriController`.
- Pastikan view menerima variable yang benar.
- Pastikan akses berdasarkan level bekerja.

### TODO: Definisikan Ulang Workflow Laporan Kajian
Prioritas: sedang

Checklist:
- Finalisasi field laporan.
- Finalisasi status laporan.
- Finalisasi role yang boleh submit/review.
- Finalisasi review approve/reject.

## Format Task Baru

Gunakan format ini setiap menambah task:

```text
### STATUS: Judul Task
Prioritas: urgent/tinggi/sedang/rendah
Tanggal mulai:
Tanggal selesai:
Owner/sub-agen:

Tujuan:

Checklist:
- [ ] Item 1
- [ ] Item 2

Catatan:

Verifikasi:
```
