# Task Log Pengembangan

Dokumen ini mencatat pekerjaan yang sudah dilakukan, sedang berjalan, dan berikutnya. Setiap task wajib punya status yang jelas.

Status yang digunakan:
- `TODO`: belum dikerjakan.
- `IN_PROGRESS`: sedang dikerjakan.
- `DONE`: selesai dan sudah dicek.
- `BLOCKED`: tertahan oleh dependency atau keputusan.
- `REVIEW`: perlu review sebelum dianggap selesai.

## Ringkasan Status Saat Ini

### REVIEW: PUB-04 / PUB-03 — Homepage ringkas dan artikel publik

Keputusan terbaru pengguna 2026-10-17: homepage hanya Hero, maksimal empat artikel terbit, maksimal empat foto Galeri dan footer. Cuplikan menuju daftar/detail publik; super admin aktif menjadi satu-satunya pengelola Post dan Galeri. PUB-01 agenda/PUB-02 tim-kontak tidak termasuk cakupan aktif; PUB-03 dibatasi navigasi bagian/halaman yang tersedia. Rincian implementasi, risiko dan verifikasi mengikuti CATATAN_JALUR_B. Daftar kebutuhan lama di bawah dibaca sebagai riwayat bila bertentangan dengan keputusan ini.

Implementasi, review independen dan QA browser lokal selesai 2026-10-18 pada dev-B; suite PHP 8.3/MySQL 266 test/1996 assertion serta build lulus. QA mencakup draf/terbit, admin ditolak, jadwal terbit dipertahankan, mobile 320/390 px dan keyboard/modal. REVIEW tersisa untuk persetujuan konten resmi serta penerapan/integrasi lingkungan target. Migration belum dijalankan pada database kerja; belum commit/push atau CI remote batch baru.

### DONE: A35-INTEGRATE — Integrasi A3–A5 dan QA lokal

Tanggal: 2026-10-17. Owner: penggabung Jalur A atas instruksi pengguna.

- A3–A5 digabung ke develop melalui ecee5b4 tanpa konflik. Tree hasil merge identik dengan abd815b yang diuji.
- Suite gabungan PHP 8.3.30/MySQL 8.4.3 pada database baru terisolasi: 260 test, 1921 assertion lulus; build frontend, 80 route, Composer validate --strict dan check-platform-reqs lulus.
- Review independen tidak menemukan regresi konkret. Dokumen pusat disinkronkan; QA browser unggah XLSX/duplikat/filter/ekspor serta batas wilayah/endpoint koorda lulus. Bukti rinci ada pada CATATAN_JALUR_A bagian A35-INTEGRATE.
- Pengguna mengonfirmasi staging dan SMTP organisasi belum tersedia; selesaikan integrasi dan QA lokal, tanpa mengklaim pengiriman email eksternal atau persetujuan rilis.
- Merge ecee5b4 dan dokumen 13da2b5 sudah dipush ke develop/dev-A. [CI remote develop](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39041927907) snapshot 13da2b5 lulus pada PHP 8.3/MariaDB 11.4, termasuk pengaman runner dan seluruh tahap install/build/test.

### DONE: NEXT-PLAN — Penetapan arah berikutnya dan serah terima Jalur B

Tanggal mulai/selesai: 2026-10-17. Owner: penggabung dokumentasi pada develop, atas persetujuan pengguna.

- Urutan disepakati: A3-VERIFY -> PUB-04 -> PUB-01 -> PUB-02 -> PUB-03 -> staging/rilis; persiapan B3-RELEASE dapat berjalan paralel secara independen.
- Arahan pemilik Jalur B dicatat pada PLAN dan CATATAN_JALUR_B; status implementasi task berikut tetap TODO.
- Verifikasi: konsistensi ID/urutan/tautan lokal dan git diff --check; hasil akhir dicatat pada CATATAN_JALUR_B.

### DONE: A3-VERIFY — Batas Role Target Verifikasi

Prioritas: P0 sebelum rilis. Owner: Jalur A.

- [x] Koorda hanya memverifikasi target non-pengelola di wilayahnya; role koorda/admin/super_admin termasuk multi-role ditolak. Admin/super_admin dapat meninjau akun lain lintas wilayah, termasuk pemulihan super_admin pending; verifikasi diri sendiri tetap ditolak.
- [x] Aturan berlaku pada antrean dan endpoint approve/reject, dengan scope wilayah serta syarat status tetap berlaku.
- [x] Regression test wilayah, target pengelola, role ganda, tanpa wilayah, status, dan alur alumni sah lulus.
- [x] Kebijakan dan bukti ada pada CATATAN_JALUR_A; review independen dan suite gabungan 260 test/1921 assertion lulus. Terintegrasi pada ecee5b4.

### DONE: A4-AUTH dan A5-FIX — Auth dan aksesibilitas profil

- A4 menangani kegagalan transport email pada registrasi/kirim ulang/reset, validasi token dan aksesibilitas form auth. Akun pending/sesi dipertahankan saat transport gagal; email terverifikasi belum menjadi syarat akses modul.
- A5-FIX menutup label profil dan overflow foto mobile: 32 kontrol berlabel, ID unik, viewport 320/390 px serta desktop dan fokus keyboard diverifikasi. Build dan pemeriksaan diff lulus.
- A5-QA tetap REVIEW untuk batas operasional: SMTP lokal Mailpit lulus; SMTP eksternal dan staging belum tersedia. Rincian QA lokal ada pada catatan integrasi A35.

### TODO: B3-RELEASE — Penutupan Risiko dan Bukti Rilis

Prioritas: P0 untuk risiko keamanan; verifikasi operasional wajib sebelum rilis. Owner: Jalur B; penggabung A+B untuk suite akhir.

- [ ] Triase advisory Composer yang sudah tercatat dan perbaiki dependency terdampak secara terarah; ikuti lockfile dan verifikasi PHP 8.3. Audit npm dilewati sesuai keputusan pengguna.
- [ ] Inventarisasi pemakaian kunci historis dan dampak dekripsi sebelum menetapkan tindakan server; jangan menganggap penghapusan dari tracking menyelesaikan paparan lama.
- [x] CI remote batch integrasi A3–A5 snapshot 13da2b5 lulus: run 39041927907 pada develop, PHP 8.3/MariaDB 11.4. Batch berikut tetap wajib memiliki bukti CI sendiri.
- [ ] Setelah target staging tersedia, uji alur per role, email nyata, impor Excel, upload, mobile/keyboard serta backup/restore sesuai runbook pada lingkungan terisolasi.
- [ ] Gabungkan bukti A3-VERIFY, test gabungan, build dan review; pertahankan status REVIEW rilis sampai syarat terpenuhi.

### DONE: DOC-SYNC — Sinkronisasi cakupan fitur dan rencana publik

Prioritas: dokumentasi integrasi.
Tanggal mulai: 2026-10-17.
Tanggal selesai: 2026-10-17.
Owner: penggabung dokumentasi A+B pada develop, atas instruksi pengguna.

- README, PRD, PLAN, TASK, AUDIT dan SOP diselaraskan dengan implementasi hasil integrasi.
- Matriks PRD membedakan fitur tersedia, sebagian, direncanakan dan ditunda; status ketersediaan bukan persetujuan rilis.
- Pengguna mengonfirmasi fitur tampilan publik tetap akan dibuat: agenda, tim, kontak, artikel dan pelengkapan navigasi publik dicatat sebagai PUB-01–PUB-04.
- Referensi instalasi/seeder diperbaiki, task materi lama ditutup oleh A2 dan workflow laporan diselaraskan ke REVIEW B2; riwayat audit tetap dipertahankan dengan konteks tanggal.
- Verifikasi: 56 pemeriksaan konsistensi ID PUB-01–PUB-04, cakupan artikel, tautan lokal/anchor, referensi seeder dan status task lulus; git diff --check lulus. Dicocokkan dengan route/view/controller/seeder existing.
- Perubahan hanya dokumentasi; test aplikasi/build tidak dijalankan ulang. CI remote, QA operasional dan staging tetap mengikuti tindak lanjut rilis.

### Integrasi lokal A+B — 2026-10-17

- Penggabungan atas instruksi pengguna mempertahankan hasil A c411747 serta commit B a31cecf. Satu konflik metadata Composer diselesaikan tanpa memperbarui paket lama; route A/B tetap lengkap.
- Suite gabungan PHP 8.3.33/MySQL 8.4.3: 239 test lulus, 1686 assertion, tanpa skip. Build frontend, validasi Composer/platform, route list dan drill migration/backup/restore terisolasi lulus.
- Test integrasi baru menutup akses dashboard bagi managerial pending/suspended melalui gate aktif A. Ekstensi gd/zip ditetapkan eksplisit pada CI untuk Excel.
- Merge lokal bukan push atau persetujuan rilis. B dan MERGE tetap REVIEW untuk tindak lanjut CI remote, advisory/penilaian kunci historis serta staging yang belum tersedia. Catatan rinci ada pada CATATAN_JALUR_B.md.

### Pembagian Aktif Dua Orang — 2026-10-16

Rencana aktif dan batas file ada di PLAN.md pada bagian Rencana Aktif: Dua Orang Bekerja Paralel. Tabel ini telah disinkronkan dengan integrasi A+B pada 2026-10-17; rincian bukti ada di CATATAN_JALUR_A.md dan CATATAN_JALUR_B.md.

| ID | Pemilik | Prioritas | Status | Pekerjaan |
|---|---|---|---|---|
| A0 | Pengembang A / dev-A | P0 | DONE | Scope koorda pada daftar/detail/tindakan; gate konten; akses materi menurut status/level; seeder akun aman; test negatif |
| B0 | Pengembang B / dev-B | P0 | REVIEW | PHP 8.3/MySQL, database test terisolasi, environment tidak terlacak, auth dashboard; CI remote dan penilaian kunci tetap tindak lanjut |
| A1 | Pengembang A / dev-A | P1 | DONE | Registrasi/role/wilayah, validasi, sorting, audit/transaksi/notifikasi |
| B1 | Pengembang B / dev-B | P1 | REVIEW | Dashboard/grafik terscope, upload aman, slug unik, homepage/refleksi/galeri terbit; gate aktif A dipakai saat integrasi |
| A2 | Pengembang A / dev-A | P2 | DONE | CRUD dan penggunaan materi, pencarian, impor/ekspor Excel/CSV alumni dengan nama wilayah |
| B2 | Pengembang B / dev-B | P2 | REVIEW | Draf/edit/kirim/review/rekap/CSV, QA browser B dan drill backup/restore lokal selesai; staging nyata belum tersedia |
| MERGE | Penggabung PR | Per batch | REVIEW | Integrasi lokal A+B atas instruksi pengguna; bukti validasi dicatat pada CATATAN_JALUR_B.md. Push/CI remote/staging belum dilakukan |

### Integrasi Jalur A ke develop — 2026-10-16

- A0/A1/A2 selesai dan digabung ke develop atas instruksi pengguna tanpa menunggu B. Status DONE di atas berlaku pada implementasi dan automated test; QA browser impor Excel dan email nyata masih tertunda.
- Pada 2026-10-16, MERGE keseluruhan masih TODO sambil menunggu B. Status terkini sudah REVIEW sesuai integrasi A+B pada 2026-10-17; jangan mengulang pekerjaan A sebagai TODO.
- Pemeriksaan integrasi: seluruh suite pada PHP 8.3.30 + ZIP dan database MySQL khusus, build frontend, validasi Composer/platform, route dan migration. Hasil final dicatat di CATATAN_JALUR_A.md.
- Pengujian MySQL penuh menemukan tiga assertion audit yang bergantung urutan key JSON. Key diurutkan sebelum assertSame agar isi, struktur, dan tipe tetap diperiksa ketat pada MySQL maupun SQLite; tidak mengubah perilaku aplikasi.
- Dev-B mengambil origin/develop lalu menyelesaikan konflik pada blok route dan Composer bila ada. Dependency Excel ditambahkan dengan persetujuan pengguna, tanpa memperbarui paket lama.

Selama paralel, masing-masing orang membuat CATATAN_JALUR_A.md atau CATATAN_JALUR_B.md di folder dokumentasi ini untuk status task, risiko, dan hasil test. Dokumen pusat diperbarui oleh penggabung setelah merge. Pekerjaan tiap jalur dapat dimulai dari commit dasar yang sama; urutan penggabungan B0 lalu A0 hanya untuk integrasi.

### DONE: Penyusunan Pembagian Kerja Paralel

Tanggal: 2026-10-16.

- Membagi perbaikan hasil audit ke dua pemilik dengan prioritas P0/P1/P2.
- Menetapkan kepemilikan controller/model/view/test/toolchain serta blok route bersama.
- Menetapkan kontrak role/status/schema existing dan larangan perubahan kontrak sepihak.
- Menentukan kriteria selesai, pengujian mandiri, dan pemeriksaan hasil merge.
- Verifikasi tugas ini terbatas pada konsistensi dokumen dan diff; tidak mengubah atau menguji ulang kode aplikasi.

### DONE: Aturan Pembuka Sesi Agen A/B

Tanggal: 2026-10-16.

- Menambahkan AGENTS.md pada root agar agen baru membaca penugasan, prioritas, kepemilikan file, kontrak, dan prosedur merge.
- Menetapkan dev-A untuk pengguna dan dev-B untuk rekan pengembang; branch dev-B belum dibuat lokal.
- Menetapkan default Jalur B untuk sesi baru tanpa penugasan di luar dev-A, dengan pemeriksaan branch/status sebelum mengedit.
- Menyelaraskan SOP pencatatan per jalur agar dokumen pusat tidak diedit bersamaan.
- File aturan dan rencana perlu di-commit serta dibagikan melalui baseline develop sebelum tersedia di checkout rekan.

Fase proyek setelah integrasi 2026-10-17:
- V1 Data Alumni: fitur inti termasuk impor/ekspor CSV/Excel tersedia.
- V2 Materi Kajian: pengelolaan dan akses materi tersedia; metadata otomatis/progres ditunda.
- V3 Laporan Kajian: draf sampai rekap/CSV tersedia; B2 masih REVIEW untuk rilis.
- Publik: homepage/refleksi/galeri tersedia; agenda, tim/kontak, navigasi final dan artikel dilanjutkan melalui PUB-01–PUB-04.

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

### DONE: Perbaiki Modul Materi (ditutup oleh A0/A2)
Prioritas: tinggi setelah audit schema.
Status disinkronkan: 2026-10-17; bukti implementasi/test pada CATATAN_JALUR_A.md dan integrasi A+B.

Checklist:
- [x] Sesuaikan field controller dengan database dan perbaiki variabel view.
- [x] Akses materi menurut status/level, pengelolaan playlist/video dan pencarian.

Metadata otomatis dan progres menonton tetap ditunda; bukan bagian task yang ditutup ini.

### REVIEW: Workflow Laporan Kajian (implementasi B2 tersedia)
Prioritas: sedang.
Status disinkronkan: 2026-10-17; bukti implementasi/test pada CATATAN_JALUR_B.md.

Checklist:
- [x] Field/status/role laporan ditetapkan; draf, edit/kirim, review approve/reject, rekap dan CSV tersedia.
- [ ] Selesaikan CI remote dan validasi staging/rilis bersama B0–B2.

Laporan rejected tetap terkunci; alur revisi/kirim ulang belum tersedia.

## Pekerjaan Publik Berikutnya

Urutan aktif setelah penyederhanaan pengguna: **PUB-04 bersama navigasi PUB-03**. PUB-01/PUB-02 berada di luar cakupan aktif. Lihat A3-VERIFY dan B3-RELEASE untuk riwayat integrasi/syarat rilis.

Cakupan disederhanakan pengguna pada 2026-10-17; status berikut mengikuti bukti pada CATATAN_JALUR_B. Pemilik mengikuti PLAN dan kepemilikan file existing.

### TODO: PUB-01 — Agenda Kajian Publik

Di luar cakupan aktif sesuai penyederhanaan homepage oleh pengguna pada 2026-10-17. Jangan implementasikan sebelum ada arahan baru; checklist berikut disimpan sebagai kebutuhan historis.

Prioritas: P2. Owner: Jalur B.

- [ ] Tetapkan konten agenda resmi dan cara pengelolaannya sebelum coding.
- [ ] Tampilkan informasi agenda pada halaman publik aktif; sediakan empty state bila belum ada agenda.
- [ ] Verifikasi informasi, tautan, tampilan mobile dan keyboard; jangan menampilkan contoh sebagai agenda nyata.

### TODO: PUB-02 — Profil Tim dan Kontak Resmi

Di luar cakupan aktif sesuai penyederhanaan homepage oleh pengguna pada 2026-10-17. Jangan implementasikan sebelum ada arahan baru; checklist berikut disimpan sebagai kebutuhan historis.

Prioritas: P2. Owner: Jalur B.

- [ ] Gunakan nama/peran/foto tim dan kanal kontak resmi yang disetujui pengelola.
- [ ] Pasang konten pada halaman publik aktif dan ganti template yang belum digunakan.
- [ ] Verifikasi kanal/tautan kontak, aksesibilitas dan tampilan mobile.

### REVIEW: PUB-03 — Navigasi Homepage Ringkas

Cakupan sekarang hanya navigasi Artikel/Galeri, tautan Hero membaca artikel, dan utilitas Masuk/Area anggota existing; dikerjakan bersama PUB-04. Kebutuhan CTA Daftar/section lainnya pada checklist lama ditunda sesuai penyederhanaan pengguna.

Prioritas: P2. Owner: Jalur B; koordinasi A bila menyentuh view auth.

- [x] Hubungkan navigasi Artikel/Galeri, Hero membaca artikel serta Masuk/Area anggota ke route tersedia.
- [x] Hapus tautan homepage ke section di luar cakupan; CTA Daftar tambahan ditunda sesuai keputusan pengguna.
- [x] Uji navigasi mobile/keyboard dan tautan publik yang tersedia; bukti ada pada catatan jalur.
- [ ] Persetujuan konten resmi oleh pengelola pada lingkungan target.

### REVIEW: PUB-04 — Artikel Publik

Prioritas: P2. Owner: Jalur B.

- [x] Reuse Post dengan jenis Refleksi/Artikel dan published_at untuk draf/terbit; pengelolaan Post/Galeri hanya super admin aktif, sesuai keputusan pengguna. Konten lama tetap Refleksi.
- [x] Buat daftar dan detail artikel publik serta tautan navigasi.
- [x] Pastikan hanya artikel terbit yang dapat dibaca publik, dengan empty/not-found state dan test publikasi; edit biasa tidak mengubah jadwal terbit.
- [x] Verifikasi tampilan mobile dan keyboard; pertahankan URL/alur refleksi existing.
- [ ] Persetujuan konten resmi dan penerapan migration/integrasi lingkungan target; data QA sintetis hanya pada database terisolasi.

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
