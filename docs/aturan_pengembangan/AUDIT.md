# Audit Pengembangan

Dokumen ini menjaga pengembangan tetap pada jalur produk, teknis, UX, dan prioritas.

## Prinsip Audit

1. Jangan menambah fitur baru sebelum workflow utama yang terkait stabil.
2. Jangan mengubah schema tanpa mencatat dampaknya ke controller, model, seeder, view, dan test.
3. Jangan membiarkan form gagal tanpa pesan error yang terlihat.
4. Jangan menggunakan data dummy di area dashboard produksi tanpa label jelas.
5. Jangan membuat role/permission ambigu.
6. Jangan mengabaikan scope wilayah untuk koorda.
7. Jangan lanjut ke tahap berikutnya jika ada error fatal di log untuk workflow yang baru disentuh.

## Audit Status Saat Ini

### Alumni Store
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Form tambah alumni sudah diarahkan ke `alumni.store`.
- Insert database berhasil.
- Role alumni ditambahkan.
- Error validasi sudah ditampilkan.
- Automated test mencakup tambah alumni oleh admin, role alumni, status pending, email unik, dan password confirmation.

Risiko tersisa:
- Tidak ada risiko blocker tersisa untuk workflow tambah alumni.

Tindakan:
- Lanjutkan audit sorting dan scope alumni pada task berikutnya.

### Alumni Filter dan Empty State
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Daftar alumni sekarang menampilkan ringkasan jumlah hasil dan total alumni.
- Filter aktif terlihat sebagai badge.
- Tombol reset tersedia ketika filter aktif.
- Empty state filter aktif berbeda dari empty state data kosong.

Risiko tersisa:
- Sorting kolom relasi seperti wilayah/level perlu audit terpisah karena controller belum melakukan join khusus untuk sort relasi.

Tindakan:
- Audit sorting alumni pada task stabilisasi berikutnya jika fitur sorting dipakai di UI.

### Alumni Delete
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Route `alumni.destroy` tersedia untuk hapus data alumni.
- Tombol hapus hanya muncul untuk target yang boleh dihapus oleh aktor yang sedang login.
- Endpoint menolak penghapusan akun yang sedang login.
- Endpoint hanya mengizinkan aktor dengan role `super_admin` dan `admin`.
- Endpoint mengizinkan target dengan role `alumni` dan `koorda`.
- Endpoint menolak target dengan role `super_admin` atau `admin`.
- Test mencakup hapus alumni, hapus koorda, self-delete ditolak, koorda sebagai aktor ditolak, dan admin/super admin sebagai target ditolak.

Risiko:
- Penghapusan bersifat permanen, belum memakai soft delete.
- Akun koorda yang dihapus akan kehilangan akses koordinasi wilayah secara permanen.

Tindakan:
- Pertimbangkan soft delete atau arsip alumni jika kebutuhan audit data alumni makin ketat.
- Jangan memberikan akses hapus akun kepada role `koorda` tanpa keputusan produk eksplisit.

### Role Akun dan Level Materi Alumni
Status: terkendali dan sudah memiliki automated test.

Pemetaan:
- `roles` mengatur hak akses sistem: `super_admin`, `admin`, `koorda`, `alumni`.
- `levels` mengatur akses materi/pembelajaran: `Level 1/dasar`, `Level 2/menengah`, `Level 3/lanjutan`, `Level 4/akhir`.

Temuan:
- Seeder level sebelumnya memakai deskripsi `superadmin`, `admin`, `koordinator`, dan `alumni`.
- Hal itu membuat halaman detail alumni bisa menampilkan `Level superadmin` untuk user yang sebenarnya hanya memiliki role `alumni`.
- Seeder dan data migration sudah dikoreksi agar level tidak memakai nama role sistem.
- Halaman detail alumni sekarang menampilkan `Role Akun` dan `Level Materi` secara terpisah.

Risiko:
- Data lama di environment lain tetap perlu menjalankan migration terbaru.
- Penentuan level materi final masih perlu dikaitkan dengan workflow verifikasi dan materi kajian.

Tindakan:
- Jangan memakai `levels.description` untuk menyimpan role.
- Saat verifikasi alumni, pilih `level_id` berdasarkan tingkat materi, bukan hak akses.
- Gunakan relasi `roles` untuk kebutuhan permission/menu.

### Ubah Level Materi Alumni dan Koorda
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Route `alumni.level.update` tersedia untuk mengubah `users.level_id`.
- Aktor dibatasi ke role `super_admin` dan `admin`.
- Target dibatasi ke role `alumni` dan `koorda`.
- Target dengan role `super_admin` atau `admin` ditolak.
- Form `Ubah Level Materi` hanya tampil di halaman detail jika aktor dan target memenuhi aturan.
- Perubahan level dicatat ke `audit_logs` dengan action `alumni.level.update`.

Risiko:
- Perubahan level berdampak pada akses materi karena modul materi memakai `users.level_id`.

Tindakan:
- Jangan mengubah role akun saat hanya ingin mengubah level materi.
- Saat modul materi dikembangkan, pastikan akses playlist/video tetap mengikuti level materi terbaru.

### Level Materi dan Kolom Daftar Alumni
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Halaman daftar alumni sekarang menampilkan kolom `Level Materi`, bukan `Status`.
- Kategori level final adalah `dasar`, `menengah`, `lanjutan`, dan `akhir`.
- `admin` dan `super_admin` ditetapkan ke level materi `akhir`.
- Migration data sudah disiapkan untuk memperbarui database berjalan.

Risiko:
- Modul materi memakai `users.level_id`; menaikkan admin/super admin ke level `akhir` berarti mereka dapat melihat seluruh materi yang dibatasi level.

Tindakan:
- Jangan mengembalikan kategori `mahir`; gunakan `akhir` sebagai level tertinggi.
- Jika status akun tetap diperlukan di daftar alumni, tampilkan sebagai filter/detail terpisah, bukan menggantikan kolom level materi.

### Edit Data Alumni dan Koorda
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Route `alumni.edit` dan `alumni.update` tersedia untuk edit data profil alumni/koorda.
- Aktor dibatasi ke role `super_admin` dan `admin`.
- Target dibatasi ke role `alumni` dan `koorda`.
- Target dengan role `super_admin` atau `admin` ditolak.
- Perubahan dicatat ke `audit_logs` dengan action `alumni.update`.

Risiko:
- Perubahan email/telepon berdampak pada identitas login dan kontak alumni.

Tindakan:
- Jangan memakai form edit data ini untuk mengubah role akun.
- Jangan memakai form edit data ini untuk mengubah level materi; gunakan fitur `Ubah Level Materi`.
- Pertahankan validasi unik untuk email dan nomor telepon.

### Tombol Aksi Icon Only
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Tombol aksi tabel alumni sekarang hanya menampilkan icon untuk `Detail`, `Edit`, dan `Hapus`.
- Setiap tombol tetap memiliki `aria-label` dan `title` agar makna aksi tidak hilang bagi screen reader dan tooltip browser.
- Ukuran tombol dibuat tetap agar kolom aksi stabil saat jumlah tombol berbeda antar role.

Tindakan:
- Jangan membuat tombol icon-only tanpa `aria-label`.
- Jaga mapping icon: mata untuk detail, pena untuk edit, tempat sampah untuk hapus.

### Badge Role Daftar Alumni
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Badge `Koorda` sebelumnya memakai warna emerald yang secara visual terlalu dekat dengan badge `Alumni` yang memakai sky.
- Badge `Koorda` sekarang memakai violet agar lebih mudah dibedakan saat scanning tabel.

Pemetaan warna saat ini:
- `Super Admin`: rose.
- `Admin`: amber.
- `Koorda`: violet.
- `Alumni`: sky.

Tindakan:
- Jaga kontras dan perbedaan hue antar role badge.
- Hindari memakai warna hijau untuk role karena hijau sudah dipakai status `Active`.

### Upload Foto Profil
Status: terkendali dan sudah memiliki automated test.

Temuan:
- Form profil desktop dan mobile sudah mendukung upload file dengan `multipart/form-data`.
- `ProfileController@update` menyimpan file ke disk `public` dan mengisi kolom `photo_url` dengan URL `/storage/avatars/...`.
- `User::avatar_url` sebelumnya belum membaca `photo_url`, sehingga foto yang tersimpan tetap tidak terbaca oleh view.
- Accessor avatar sekarang membaca `photo_url` serta mendukung URL penuh, URL storage lokal, dan path disk publik.

Risiko:
- File lama yang tersimpan di disk publik tetap bergantung pada symlink Laravel `public/storage`.

Tindakan:
- Pastikan server produksi sudah menjalankan `php artisan storage:link` jika foto belum tampil di browser meskipun database sudah berisi `/storage/avatars/...`.

### Auth dan Register
Status: perlu review lanjutan.

Temuan:
- Route register berada di middleware auth pada file `routes/auth.php`, tidak seperti pola umum register publik.
- Perlu dipastikan apakah ini memang kebijakan internal atau kesalahan routing.

Risiko:
- Pengunjung baru mungkin tidak bisa registrasi jika harus guest.
- Admin dan publik bisa memiliki ekspektasi alur berbeda.

Tindakan:
- Putuskan apakah register publik dibuka atau hanya admin yang membuat akun.
- Jika register publik dibuka, pisahkan alur `register` publik dan `alumni.store` admin.

### Scope Wilayah
Status: terkendali secara teknis, perlu QA workflow.

Temuan:
- Middleware `ScopeWilayah` memakai method `hasAnyRole`, sementara model user yang ditinjau memiliki `hasRole`.
- Helper `User::hasAnyRole()` sudah ditambahkan dan meneruskan ke `hasRole()`.

Risiko:
- Scope wilayah tetap perlu diuji end-to-end saat modul laporan aktif.

Tindakan:
- Tambahkan test scope wilayah saat workflow laporan mulai dibangun.

### Materi Kajian
Status: schema dasar stabil, workflow fitur masih awal.

Temuan:
- Controller sebelumnya mencari field `status = publik` pada playlist.
- View sebelumnya membaca `$items`, sedangkan controller mengirim `$playlists`.
- Inkonsistensi `is-active` dan `is_active` pada migration playlist sudah diperbaiki menjadi `is_active`.
- Migration untuk `videos` dan `video_progress` sudah ditambahkan.
- `playlists.tags` sudah ditambahkan agar sesuai model.
- `MateriController@index` dan view index sudah memakai `is_active` dan `$items`.
- View detail materi dasar sudah tersedia.

Risiko:
- CRUD playlist/video belum tersedia.
- Detail materi masih dasar dan belum memiliki UX final.
- Progress menonton belum dihubungkan ke UI.

Tindakan:
- Lanjutkan task Perbaiki Modul Materi untuk CRUD playlist/video, detail materi, dan UX akses per level.

### Test Environment
Status: terkendali.

Temuan:
- PHP lokal tidak memiliki `pdo_sqlite`, sehingga konfigurasi SQLite in-memory di PHPUnit tidak bisa berjalan.
- PHPUnit sekarang memakai MySQL database khusus `rf_db_testing`.

Risiko:
- Test dapat gagal di mesin lain jika database `rf_db_testing` belum dibuat atau kredensial MySQL berbeda.

Tindakan:
- Pastikan SOP/setup developer mencatat kebutuhan database testing.
- Jangan arahkan `RefreshDatabase` ke database development `rf_db`.

### Migration dan Seeder
Status: terkendali.

Temuan:
- `migrate:fresh --seed --env=testing` berhasil di `rf_db_testing`.
- Tabel inti alumni, role, wilayah, level, konten, materi, progress video, dan laporan kajian sekarang punya migration.
- Nilai enum `ab` memakai `iya`/`tidak`; form alumni juga memakai nilai yang sama.

Risiko:
- Seeder belum mengisi data contoh playlist/video/laporan.
- Perlu seed khusus materi/laporan ketika modulnya mulai aktif.

Tindakan:
- Tambahkan seeder playlist/video saat task materi masuk fase implementasi fitur.

### Laporan Kajian
Status: awal.

Temuan:
- Controller review masih placeholder.
- Workflow approve/reject belum final.

Risiko:
- Pengembangan laporan bisa melebar tanpa PRD jelas.

Tindakan:
- Finalisasi PRD laporan sebelum coding.

### Dashboard
Status: perlu validasi.

Temuan:
- Ada dummy data untuk grafik jumlah kajian per wilayah.

Risiko:
- User mengambil keputusan dari data yang tidak nyata.

Tindakan:
- Beri label dummy atau ganti dengan query database.

## Checklist Audit Sebelum Mulai Task

- [ ] PRD sudah menjelaskan kebutuhan task.
- [ ] PLAN sudah menempatkan task pada prioritas yang benar.
- [ ] TASK sudah mencatat pekerjaan sebagai `TODO` atau `IN_PROGRESS`.
- [ ] Risiko teknis sudah dicatat jika ada.
- [ ] Dampak UX sudah dipahami.
- [ ] Dampak role/permission sudah dipahami.
- [ ] Dampak database sudah dipahami.

## Checklist Audit Sebelum Menutup Task

- [ ] Kode selesai.
- [ ] Error/success state tersedia.
- [ ] Permission dicek.
- [ ] Data database dicek jika task menyimpan data.
- [ ] Log Laravel dicek untuk workflow terkait.
- [ ] TASK diperbarui menjadi `DONE` atau `REVIEW`.
- [ ] AUDIT diperbarui jika ada risiko baru.
- [ ] PRD/PLAN diperbarui jika scope berubah.

## Keputusan Audit

### 2026-07-19: Dokumentasi Pengembangan Wajib Sinkron

Keputusan:
- Setiap pengembangan berikutnya harus mengecek dan memperbarui dokumen di `docs/aturan_pengembangan`.

Alasan:
- Project memiliki banyak modul yang saling terkait.
- Tanpa dokumentasi sinkron, risiko bug workflow dan schema meningkat.

### 2026-07-19: Skill Project Terpasang dan Wajib Dipakai Sesuai Konteks

Keputusan:
- Skill pengembangan dipasang di `.agents/skills` sebagai project skills untuk Codex.
- Skill hanya dipakai saat relevan dengan task dan instruksi skill wajib dibaca sebelum digunakan.

Skill terpasang:
- UI/UX: `ui-ux-pro-max`, `ui-styling`, `design-system`, `shadcn`.
- QA browser: `browser-use`, `remote-browser`, `qa`.
- Engineering: `setup-matt-pocock-skills`, `diagnosing-bugs`, `tdd`, `code-review`, `implement`, `codebase-design`, `domain-modeling`, `research`, `handoff`.
- AI gateway: `9router`, `9router-chat`, `9router-web-search`, `9router-web-fetch`.

Catatan keamanan:
- CLI skills memberi peringatan risk assessment untuk beberapa skill, terutama browser/cloud/network-related skill.
- Skill browser automation dan 9Router tidak boleh menerima API key, token produksi, atau data sensitif tanpa izin eksplisit.
- `shadcn` tidak berarti project berubah menjadi React; pemakaiannya sebatas referensi/pola UI kecuali ada keputusan arsitektur baru.
- Repo `anomalyco/opencode` tidak dipasang karena skill yang terdeteksi (`effect`) tidak relevan untuk project Laravel saat ini.
