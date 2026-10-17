# Catatan Jalur A

## A5-QA — IN_PROGRESS (2026-10-17)

- Lanjutkan QA browser operasional impor Excel, profil dan notifikasi atas instruksi pengguna. Live Preview VS Code terpasang, tetapi belum terhubung ke alat UI; halaman Laravel tetap memerlukan server PHP. Gunakan browser terisolasi dan data sintetis untuk memeriksa alur melalui server aplikasi, bukan membuka file Blade langsung.
- Database, akun, kunci/sesi, berkas serta proses QA dipisahkan dari lingkungan kerja. Tidak melakukan uji kesiapan rilis/SMTP nyata pada batch ini; hasil dan batas verifikasi dicatat setelah pengujian.
- Insiden setup: PowerShell menolak pemuatan env.ps1 karena execution policy, tetapi perintah migrasi berikutnya tetap berjalan. Runner memakai environment path direktori QA tanpa .env; fallback konfigurasi SQLite membuat `database/database.sqlite` pada 2026-10-17 17:03:29–17:03:30 WIB. Pemeriksaan SQLite read-only: 21 tabel aplikasi semuanya 0 baris, migrations 22 baris batch 1. Database MySQL khusus QA masih kosong; environment proyek asli tidak dimuat oleh runner. Seluruh mutasi dihentikan saat ditemukan; tidak ada rollback atau penghapusan file SQLite. Pengguna diberi tahu. Guard konfigurasi eksplisit dan review runner diwajibkan sebelum melanjutkan migrasi/seed khusus QA.
- Pengguna kemudian menetapkan database `rausyan_fikr_test` untuk QA browser. Pemeriksaan read-only MySQL 8.4.3 pada 127.0.0.1:3306 membuktikan schema tersebut ada dan belum memiliki tabel sebelum persiapan. Runner QA diarahkan secara eksplisit ke database ini dengan guard nama/driver/host/port; tanpa migrate:fresh dan tanpa mengubah .env kerja atau default PHPUnit milik B. Database pilihan pengguna tidak dihapus saat cleanup.

## Pengiriman Batch A3/A4 — 2026-10-17

- Pengguna meminta commit dan push hasil A3-VERIFY/A4-AUTH ke dev-A. Uji kesiapan rilis dilakukan bersama pada tahap akhir setelah website siap menuju produksi; tidak menghalangi pengiriman batch pengembangan ini.
- Bukti test/build/review lokal di bawah tetap berlaku untuk snapshot implementasi ini. Catatan belum commit/push pada masing-masing tahap merekam kondisi saat verifikasi, sebelum permintaan pengiriman ini. Integrasi ke develop dilakukan terpisah.

## A4-AUTH — DONE pada implementasi lokal (2026-10-17)

- Atas instruksi pengguna, lanjutkan verifikasi registrasi/lupa password: token invalid/kedaluwarsa/pakai ulang, akun impor, validasi dan pembatasan permintaan, serta aksesibilitas form auth existing.
- Pertahankan kebijakan akses: verifikasi email tersedia tetapi belum menjadi syarat modul; status/role/level tidak berubah karena reset password. Tautan Daftar publik tetap PUB-03 milik B; A tidak mengubah homepage atau layout bersama.
- Pengguna memilih penerapan antislop selama pengerjaan. Tidak mengubah desain visual atau menambah dependensi. Email nyata menunggu transport/target uji yang tersedia; gunakan mail/notification fake pada database MySQL terisolasi untuk verifikasi otomatis.
- Hasil: form auth memiliki pasangan label/input dan autocomplete, tombol tampil/sembunyikan password memiliki nama serta status aksesibel, petunjuk registrasi/password diperjelas. Error email/token pada halaman reset kini tampil dengan tautan meminta reset baru; halaman verifikasi menampilkan status gagal dan menyediakan kelanjutan profil tanpa mengklaim email sudah terkirim.
- Tiga kegagalan transport email direproduksi sebagai HTTP 500 sebelum perbaikan: registrasi, kirim ulang verifikasi, dan lupa password. Controller kini hanya menangkap `TransportExceptionInterface`, melaporkan error ke server, dan mengembalikan status/pesan kegagalan yang dapat dicoba ulang. Registrasi tetap menyimpan akun pending/alumni dan sesi; error lain tidak disembunyikan. Token reset yang sudah dibuat sebelum email gagal tetap mengikuti throttle broker 60 detik.
- Test mencakup token invalid/kedaluwarsa/pakai ulang/milik akun lain, password confirmation, throttle, impor CSV -> reset -> login tanpa perubahan role/level/status, validasi registrasi/duplikat/injeksi privilege, notifikasi verifikasi serta tautan verifikasi tidak sah. Tidak ada perubahan schema, kebijakan email wajib, lockfile, environment, homepage atau file Jalur B.
- Perintah: PHP 8.3.30 dengan `-d extension=zip vendor/phpunit/phpunit/phpunit tests/Feature/Auth/RegistrationTest.php tests/Feature/Auth/PasswordResetTest.php tests/Feature/Auth/EmailVerificationTest.php`: **21 tests, 140 assertions**, lulus. `tests/Feature/AuthFormAccessibilityTest.php`: RED 3 kegagalan label/toggle/error token, kemudian RED 1 kegagalan status email; GREEN akhir **4 tests, 49 assertions**. Suite penuh tanpa path/filter: **260 tests, 1921 assertions**, lulus pada MySQL 8.4.3 sementara di port 33377; database `rf_a4_20261017_testing` dan database UI terpisah `rf_a4_ui_20261017_testing`, keduanya memakai environment proses.
- Verifikasi tambahan: lint enam file PHP auth milik implementer lulus; `npm.cmd run build` lulus (162 modul, peringatan data browser lama existing); self-check Node menjalankan handler toggle dua arah dan memeriksa tipe field/nama/status aksesibel; `git diff --check` lulus. Review independen tidak menemukan masalah konkret.
- Gerbang antislop untuk perubahan ini: label/nama/status kontrol PASS melalui DOM test dan self-check JS; feedback gagal PASS melalui HTTP test; kontras petunjuk slate-600/putih PASS 7.58:1 dan tautan indigo-600/putih PASS 6.29:1 melalui contrast-check.py. Gaya existing dipertahankan untuk perbaikan aksesibilitas, tanpa aset/dekorasi baru. QA visual/mobile/keyboard browser belum terverifikasi: in-app browser tidak tersedia dan inventaris browser kosong. Tidak mengklaim audit aksesibilitas menyeluruh atau persetujuan rilis.
- File A4: tiga controller auth RegisteredUser/EmailVerificationNotification/PasswordResetLink; lima view auth login/register/forgot-password/reset-password/verify-email; tiga test Auth Registration/PasswordReset/EmailVerification; test AuthFormAccessibility dan catatan ini. Belum commit/push; integrasi dan bukti SMTP/staging tetap tindak lanjut bersama B.
- Cleanup: server HTTP/MySQL sementara dihentikan, direktori data uji khusus dihapus setelah pemeriksaan path dan proses; hash `.env` tetap sama. Database kerja tidak digunakan.

## A3-VERIFY — DONE pada dev-A (P0, 2026-10-17)

- Koorda hanya dapat meninjau akun non-pengelola di wilayahnya; target ber-role koorda/admin/super_admin (termasuk multi-role alumni+pengelola) disembunyikan dari antrean dan ditolak 403 pada approve/reject. Akun pending tanpa role legacy tetap dapat disetujui dan mendapat role alumni.
- Admin dan super_admin tetap dapat memverifikasi akun lain lintas wilayah, termasuk super_admin, sesuai kontrak sebelumnya. Aktor multi-role memakai hak tertinggi; diri sendiri dan target non-pending tetap tidak dapat diverifikasi. Kebijakan admin ini menjaga pemulihan bila satu-satunya super_admin menjadi pending setelah mengubah profil kritikal.
- Perubahan: `app/Http/Controllers/VerificationController.php` dan `tests/Feature/VerificationRoleAccessTest.php`. Tidak ada perubahan schema, route, view atau file Jalur B.
- RED pada PHP 8.3.30/MySQL 8.4.3 database `rf_a3_20261017_testing`: endpoint koorda ke pengelola mengembalikan 302, bukan 403. Test pemulihan super_admin setelah perubahan profil juga membuktikan pembatasan tambahan admin menghasilkan 403; pembatasan tambahan itu kemudian dihapus. GREEN akhir: test domain verifikasi/regional/profil **35 tests, 257 assertions**; seluruh suite **246 tests, 1780 assertions**, tanpa kegagalan. Database uji terisolasi dan tidak menyentuh `.env`/database kerja.
- Review independen menemukan pembatasan admin ke super_admin dapat mengunci satu-satunya super_admin yang kembali pending. Pembatasan tersebut dihapus; review ulang tidak menemukan temuan konkret. Integrasi ke develop dan QA browser/email nyata tetap ditangani pada batch gabungan.
- Perintah verifikasi: PHP 8.3.30 dengan `-d extension=zip vendor/phpunit/phpunit/phpunit` (suite penuh); pengujian domain memakai empat path eksplisit `tests/Feature/VerificationRoleAccessTest.php tests/Feature/VerificationWorkflowTest.php tests/Feature/RegionalAccessTest.php tests/Feature/ProfileTest.php`. Konfigurasi database melalui environment proses: MySQL lokal khusus port 33377, database di atas; tidak mengubah phpunit.xml atau environment nyata. Lint PHP controller/test, `artisan route:list --path=verifikasi --except-vendor` (3 route), dan `git diff --check` lulus.
- Hash `.env` tetap sama sebelum/sesudah pengujian. Server MySQL sementara telah dihentikan dan direktori data khusus pengujian dihapus setelah suite selesai. Build frontend tidak diulang karena tidak ada perubahan frontend; belum commit/push atau merge batch ini.

Pemilik: pengembang A (pengguna). Branch: dev-A.

## A0 — P0: Akses dan Seeder Akun Aman

Status: DONE (implementasi dan pengujian lokal; penggabungan diverifikasi terpisah).
Mulai: 2026-10-16.
Selesai: 2026-10-16.

### Scope dan Batas Pengujian

- Endpoint HTTP alumni index/detail dan verifikasi list/approve/reject: admin/super admin lintas wilayah, koorda hanya wilayah sendiri dan harus memiliki wilayah.
- Gate manage-content: hanya admin/super admin; isAdminLike tetap mempertahankan kontrak izin masuk koorda.
- Semua gate pengelola mensyaratkan status active. Regresi HTTP membuktikan koorda yang mengubah wilayah sendiri menjadi pending tidak dapat mengambil akses wilayah baru sebelum disetujui ulang.
- Endpoint materi index/detail: akun aktif dengan level yang memenuhi syarat; null/pending/suspended ditolak.
- Perintah seeding: akun demo dibatasi lingkungan lokal/test; seed ulang tidak mengubah akun nyata yang sudah ada.
- Batas di atas mengikuti A0 pada PLAN yang sudah disetujui. A1/A2 dan file milik B tidak dikerjakan dalam batch ini.

### Lingkungan dan Verifikasi

- PHP 8.3.30 ditemukan di C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe; dependensi vendor sudah tersedia.
- Pengujian menggunakan SQLite :memory: dengan override environment per proses, tanpa mengubah phpunit.xml atau database kerja. Konfigurasi CI/MySQL tetap milik B0.
- Red -> green teramati pada kebocoran daftar/detail/verifikasi lintas wilayah, materi tanpa level/pending/suspended, gate konten untuk koorda, bypass pindah wilayah sendiri, seeding produksi, seed ulang akun, dan ID role existing yang berbeda.
- Full suite pada PHP 8.3.30: 82 test dijalankan, 80 passed, 2 skipped, 398 assertions. Dua test registrasi sudah di-skip pada baseline; registrasi adalah pekerjaan A1.
- Pemeriksaan sintaks file PHP terkait dan git diff --check lulus. Tidak ada perubahan konfigurasi/lockfile atau file implementasi milik B.

Perintah full suite (PowerShell, override hanya pada proses shell pengujian):

```powershell
$env:APP_ENV='testing'
$env:DB_CONNECTION='sqlite'
$env:DB_DATABASE=':memory:'
$env:DB_URL=''
& C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe vendor/bin/phpunit
```

### Perubahan dan Review

- AlumniController membatasi query utama, total, pilihan wilayah/angkatan, dan detail sesuai wilayah koorda. Verifikasi menerapkan batas wilayah pada daftar dan approve/reject; wilayah kosong ditolak.
- AuthServiceProvider mempertahankan izin koorda masuk modul alumni/verifikasi, tetapi membatasi konten ke admin/super admin dan menonaktifkan seluruh gate pengelola saat pending/suspended.
- MateriController menolak akun tidak aktif/tanpa level dan selalu memfilter playlist aktif sesuai level pada index/detail.
- DatabaseSeeder menjalankan demo User/Post/Gallery hanya di local/testing; UserSeeder juga menolak pemanggilan langsung di luar dua lingkungan tersebut. Akun existing tidak ditimpa atau dinaikkan rolenya.
- Level/WilayahSeeder tidak menghapus atau menimpa data existing. RoleSeeder mengenali nama role tanpa mengasumsikan ID, sehingga database parsial tetap dapat dilengkapi.
- Test baru: RegionalAccessTest, ContentGateTest, AccountSeedingTest. Fixture MateriIndexTest dan AlumniDetailTest kini menetapkan status/wilayah yang sesuai skenario akses sah.
- Review akses independen tidak menemukan celah/regresi konkret dalam scope A0; seeder diperiksa terpisah oleh agen utama. Review tidak menggantikan pemeriksaan hasil merge.

### Risiko dan Integrasi

- Gate manage-content memengaruhi endpoint konten milik B; periksa ulang test endpoint gabungan saat merge.
- Gate review-report juga mensyaratkan akun aktif; fixture dan endpoint laporan B harus mengikuti kontrak ini.
- Verifikasi MySQL/CI bersama dan browser tetap perlu hasil integrasi dengan Jalur B.
- Seed produksi sekarang hanya membuat data master; penyediaan akun pengelola produksi harus memakai prosedur aman, bukan akun/password demo.
- Bug audit/notifikasi penolakan serta registrasi/validasi berada pada A1 dan belum dinyatakan selesai oleh A0.

## A1 — P1: Siklus Data Alumni Konsisten

Status: DONE (implementasi dan pengujian lokal; penggabungan diverifikasi terpisah).
Mulai: 2026-10-16.
Selesai: 2026-10-16.
Commit A0: 283cb46 (belum dipush).

- Registrasi publik melalui GET/POST register, role alumni/status pending, lalu melengkapi profil; sesi pengelola tetap aman.
- POST alumni menetapkan wilayah koorda dari akun aktor dan mewajibkan admin memilih wilayah valid.
- PATCH profil/alumni memvalidasi field sesuai schema; sorting relasi dan audit perubahan level/profil/penghapusan konsisten.
- Approve/reject memeriksa status sebelum tindakan, memakai audit terstruktur dan transaksi.
- Penggantian foto profil menjaga file lama saat gagal; aturan password mengikuti kebijakan auth.
- Test dilakukan bertahap pada endpoint HTTP existing dengan PHP 8.3 dan SQLite :memory: seperti perintah di atas. Kontrak pengujian ini mengikuti A1 dalam PLAN yang sudah disetujui.

### Hasil Implementasi A1

- Route register dipindah ke guest dengan throttle; akun baru dibuat dalam transaksi dengan role alumni/status pending, lalu diarahkan ke profil. Input status/role/level dari pendaftar tidak dipakai. Pengelola yang sudah login tidak dapat mengganti sesinya melalui register.
- Form tambah alumni memiliki pilihan wilayah. Admin wajib memilih wilayah valid; koorda selalu memakai wilayah akun aktor, termasuk bila request mengirim wilayah lain. Koorda tanpa wilayah ditolak.
- Sorting wilayah/level memakai join yang sesuai dan hanya memilih kolom users; pencarian tetap tidak ambigu ketika join aktif.
- Update profil dan data alumni memakai validasi tipe, batas panjang, tanggal Y-m-d, serta enum yang sesuai. PATCH profil mempertahankan field opsional yang tidak dikirim. Perubahan email menghapus penanda verifikasi email lama.
- Perubahan wilayah/angkatan melalui profil sendiri maupun edit admin membuat status pending, menulis audit, dan mengirim notifikasi dalam transaksi yang sama. Hak koorda di wilayah baru menunggu persetujuan ulang.
- Update data/level dan penghapusan alumni disatukan dengan audit terstruktur. Test kegagalan audit membuktikan data dan role tidak berubah/terhapus bila pencatatan gagal. Level yang tidak aktif ditolak.
- Approve/reject membaca ulang dan mengunci target dalam transaksi, memeriksa wilayah/status sebelum efek, serta menyimpan audit/notifikasi secara atomik. Akun aktif yang ditolak tidak menerima notifikasi palsu.
- Foto profil baru dibersihkan bila transaksi gagal; file lama lokal baru dihapus setelah pembaruan berhasil. URL foto eksternal tidak diperlakukan sebagai path lokal untuk penghapusan.
- Ganti password memakai kebijakan Password::defaults dan validasi current_password bawaan. Hapus akun sendiri mencatat audit sebelum delete, lalu logoutCurrentDevice agar guard tidak menyimpan ulang model pengguna yang sudah dihapus.

### Verifikasi dan Review A1

- Red -> green dijalankan pada registrasi publik/sesi admin, penetapan wilayah, sorting relasi, audit terstruktur/rollback, validasi data, reject akun aktif, level nonaktif, dan koreksi hasil review.
- Test gabungan melalui HTTP: daftar -> lengkapi profil/wilayah -> login koorda -> approve -> login alumni -> buka materi, lulus.
- Full suite final: PHP 8.3.30, SQLite :memory:, **109 tests passed, 556 assertions, 0 skipped**. Dua test registrasi yang dahulu dilewati kini aktif dan lulus.
- Pemeriksaan sintaks PHP yang berubah dan git diff --check lulus. Form wilayah juga dirender oleh test endpoint alumni.
- Review Standards: tidak ada temuan konkret. Review Spec: satu temuan perubahan wilayah melalui admin belum membuat pending ditemukan, direproduksi, diperbaiki, dan ditutup melalui test serta review ulang.
- Pemeriksaan akhir independen atas hapus akun/password tidak menemukan masalah baru. Tidak ada perubahan file implementasi milik B atau dokumen pusat.

### Risiko dan Integrasi A1

- MySQL/MariaDB, perilaku lock pada permintaan bersamaan, browser, email produksi, dan build frontend belum diuji pada batch ini. Konfigurasi CI/database dan aset frontend tetap mengikuti B0/B1.
- Reject mempertahankan status pending sesuai kontrak; pengulangan reject dapat menghasilkan notifikasi baru karena belum ada penanda siklus pengajuan terpisah.
- Audit lama yang sudah tersimpan sebagai JSON berlapis tidak dimigrasikan; catatan baru memakai struktur array yang benar.
- A0 dan A1 belum dipush pada akhir batch A1; publikasi dan penggabungan menjadi langkah terpisah.

## A2 — P2: Materi dan Operasi Alumni

Status: DONE pada implementasi dan pengujian lokal; belum di-commit/push.
Mulai: 2026-10-16.

- A2.1: pencarian judul/deskripsi playlist, tautan menonton video/playlist YouTube, serta hapus aksi edit yang belum tersedia. Test pada GET materi index/detail dengan filter status/level existing tetap berlaku.
- A2.2: pengelolaan playlist/video oleh admin, DONE pada pengujian lokal.
- A2.3: impor/ekspor Excel dan CSV alumni beserta template, validasi, dan scope wilayah, DONE pada pengujian lokal.
- Progress menonton dan sinkronisasi metadata otomatis tetap ditunda sesuai PLAN.

### A2.1 — DONE: Pencarian dan Penggunaan Materi

- Query pencarian judul/deskripsi dikelompokkan di bawah filter playlist aktif dan batas level; input q harus string maksimal 150 karakter. Urutan pagination stabil dan parameter pencarian dipertahankan.
- Form desktop/mobile memakai label terhubung input, tombol cari, hasil kosong, dan reset pencarian. Tombol edit/tambah tanpa endpoint dihilangkan.
- Detail menyediakan tautan playlist dan video pada domain YouTube tetap, dengan ID di-URL-encode dan atribut keamanan tab baru. Video mengikuti order_index yang sudah tersedia.
- Test pencarian dan tautan teramati gagal sebelum implementasi lalu lulus. Target materi: 8 tests/43 assertions. Full suite: PHP 8.3.30, SQLite :memory:, **112 tests passed, 581 assertions**. Lint controller/test dan git diff --check lulus.
- Review independen tidak menemukan masalah konkret pada scope A2.1. MySQL, browser, pemutaran di layanan YouTube, dan build frontend belum diperiksa; batas akses aplikasi tidak membatasi tautan YouTube setelah diketahui pengguna.
- File berubah hanya MateriController, view materi index/show, MateriIndexTest, dan catatan jalur ini. Tidak ada perubahan schema, dependensi, route, atau file milik B.
- A2.1 belum di-commit/push. A2.2 CRUD materi dan A2.3 CSV alumni tetap TODO, sehingga A2 keseluruhan belum selesai.

Dokumen pusat tetap dibekukan sesuai aturan paralel dan disinkronkan oleh penggabung saat merge.

### A2.2 — DONE: CRUD Playlist dan Video

- Admin/super admin aktif mengelola playlist/video melalui gate manage-content existing. Halaman pengelolaan terpisah dari halaman alumni, sehingga draft dan level tinggi dapat dikelola tanpa membuka aksesnya ke alumni.
- Route baru hanya dalam blok materi milik A; controller/view/test baru tetap pada domain materi. Tidak ada schema/dependensi/file milik B yang diubah.
- Validasi ID YouTube, level aktif, panjang judul, boolean publikasi, durasi, dan urutan sesuai schema. Route video memeriksa induk playlist untuk mencegah perubahan video milik playlist lain.
- Form playlist dan video memakai POST/PATCH/DELETE, CSRF, label, dan pesan validasi/sukses existing. Penghapusan playlist memerlukan checkbox konfirmasi karena cascade menghapus video dan progres terkait.
- Test HTTP: CRUD admin/super admin, penolakan role/status lain, draft tidak bocor ke alumni, validasi ID/level/duplikat, batas parent video, serta cascade delete.

Hasil dan keputusan:

- Halaman kelola dan edit menggunakan controller terpisah MateriManagementController, tanpa mengubah kontrak controller baca materi alumni. Route baru di dalam blok materi sebelum route dinamis /{id}.
- Level yang dipilih wajib aktif. Bila playlist lama memakai level nonaktif, form menandainya nonaktif dan meminta admin memilih level aktif sebelum menyimpan; tidak melonggarkan aturan publikasi.
- Input lama dipulihkan hanya pada form yang gagal, dengan penanda form ditentukan server; judul video tidak mengisi ulang judul playlist. Konfirmasi server dan checkbox diperlukan untuk menghapus video maupun playlist karena progres terkait ikut terhapus.
- Review menemukan admin tanpa level tidak dapat mencapai kelola dari navigasi materi. Regresi dibuktikan gagal lalu diperbaiki: admin aktif tanpa level diarahkan dari materi.index ke materi.manage. Akun alumni tanpa level tetap ditolak sesuai A0.
- Test materi pada PHP 8.3.30/SQLite :memory: **18 tests passed, 142 assertions**. Full suite final: **121 tests passed, 659 assertions**, tanpa skip. Tidak ada kegagalan tersisa.
- Pemeriksaan sintaks controller/test dan git diff --check lulus. Form kelola/edit dirender oleh test HTTP; browser, MySQL, CSS build, dan layanan YouTube belum diuji.
- Review independen mengecek gate, induk video, dan pemisahan draft; temuan navigasi serta kejelasan level nonaktif telah ditangani. Tidak ada schema/dependensi atau file milik B yang berubah.
- A2.1 dan A2.2 belum di-commit/push. A2.3 impor/ekspor CSV tetap TODO; A2 keseluruhan belum selesai.

### A2.3 — DONE: Impor/Ekspor Alumni

- Impor menambah akun baru saja, dengan enam kolom tetap: name, email, wilayah_id, phone, angkatan, pekerjaan. Header lain (termasuk role/status/level/password) ditolak sebelum penulisan. Batas berkas 1 MiB, UTF-8 dengan BOM opsional, maksimal 200 record data.
- Setiap baris divalidasi sesuai schema; email/telepon duplikat dan wilayah koorda yang berbeda menjadi laporan kegagalan baris. Baris valid tetap disimpan. Angkatan 0 dipertahankan. Header, encoding, atau jumlah record yang tidak sesuai menolak berkas sebelum akun ditambah.
- Akun hasil impor berstatus pending, role alumni, tanpa level/verifikasi email, memakai password acak yang tidak ditampilkan atau diekspor. Pemilik akun menyetel kata sandi melalui fitur lupa kata sandi existing; pengiriman email membutuhkan konfigurasi mail operasional.
- Pembuatan akun, relasi role, dan audit alumni.import berada dalam transaksi per baris. Kegagalan audit membatalkan baris tersebut; baris sebelumnya yang berhasil tetap tersimpan. Gangguan database tak terduga tetap diteruskan sebagai error, bukan diklaim sebagai impor berhasil.
- Daftar dan ekspor memakai User::alumniListing serta AlumniIndexRequest yang sama. Scope wilayah koorda, pencarian, angkatan, wilayah, level, AB, dan sorting konsisten. Input filter yang salah tipe ditolak melalui validasi, bukan error 500. Ekspor mencakup seluruh hasil filter tanpa batas halaman dan hanya enam kolom profil tersebut.
- Ekspor menetralkan awalan formula spreadsheet dengan petik tunggal. Nilai tersebut berubah saat diekspor; CSV bukan mekanisme pemulihan data atau pembaruan akun existing.
- Tautan impor/ekspor ditambahkan pada daftar alumni. Endpoint memakai auth dan gate view-alumni existing, dengan penolakan koorda tanpa wilayah serta akun pending/suspended/alumni biasa. Route statis berada sebelum detail dinamis.
- Review menemukan kutip CSV yang tidak ditutup dapat menelan record berikutnya. Regresi dibuktikan gagal lalu diperbaiki dengan validasi struktur record mentah sebelum penulisan. Kutip rusak menolak seluruh berkas; koma, kutip ganda, dan newline sah tetap diterima. UI menjelaskan nomor baris sebagai urutan record CSV. Review ulang tidak menemukan masalah baru.
- Test CSV: **15 tests, 90 assertions**. Full suite PHP 8.3.30 dengan SQLite :memory:: **137 tests passed, 754 assertions**, tanpa skip. Pemeriksaan sintaks file PHP A2.3, route:list alumni, dan git diff --check lulus.
- Browser, MySQL, pengiriman email nyata, dan build frontend belum diuji. Tidak ada schema/dependensi/file milik B yang diubah sampai tahap CSV. A2.1–A2.3 belum di-commit/push.

#### Perubahan arah A2.3: Excel untuk admin — DONE

- Atas permintaan pengguna, .xlsx menjadi format utama admin, dengan unduh template, unggah Excel, dan ekspor Excel sesuai filter. CSV existing tetap kompatibel.
- Template satu worksheet Alumni berisi enam header existing; kolom telepon bertipe teks agar nol awal tidak hilang. Halaman menyediakan referensi ID/nama wilayah sesuai izin aktor.
- Impor tetap menambah akun pending, validasi dan audit transaksi yang sama; formula tidak dihitung, kolom role/level/password ditolak, dan batas wilayah tetap berlaku. Template kosong tidak membuat akun contoh.
- Pengguna menyetujui pengecualian terbatas composer.json/lock untuk Excel. PhpSpreadsheet **5.10.0** ditambahkan bersama empat dependency wajib (composer/pcre, maennchen/zipstream-php, markbaker/complex, markbaker/matrix): **5 installs, 0 updates, 0 removals**. Tidak ada pembaruan paket lama atau perubahan konfigurasi PHP.
- Composer check-platform-reqs lulus pada PHP 8.3.30 dengan `-d extension=zip`. ZIP pada PHP 8.4 lokal sudah aktif; PHP 8.3 lokal membutuhkan opsi tersebut saat pengujian atau aktivasi ekstensi pada runtime server. Runtime tim wajib memenuhi kebutuhan Composer.
- Audit Composer sesudah instalasi melaporkan **48 advisory pada 14 paket existing**; kelima paket baru tidak tercantum. Tindak lanjut jalur B: tinjau pembaruan keamanan paket lama secara terpisah, termasuk Laravel/Symfony/Guzzle/CommonMark dan dependency dev. Paket tersebut sengaja tidak diperbarui dalam pengecualian Excel.
- Implementasi selesai: template .xlsx kosong satu worksheet Alumni, header tebal/freeze/filter, kolom telepon teks; halaman impor menampilkan urutan langkah serta referensi wilayah sesuai hak aktor. Ekspor Excel menjadi pilihan utama di daftar alumni; CSV tetap tersedia.
- Pembaca Xlsx khusus memeriksa ZIP maksimal 1000 entry/10 MiB data terurai, lalu listWorksheetInfo bawaan library memeriksa satu sheet, maksimal 201 baris termasuk header dan enam kolom sebelum workbook dimuat. Berkas rusak, sheet tambahan, kolom tambahan, atau batas terlampaui ditolak sebelum akun ditulis.
- Formula pada data ditolak per baris tanpa evaluasi. Telepon numerik ditolak dengan penjelasan memakai format teks. Nilai ekspor seluruhnya ditulis sebagai teks eksplisit agar nol awal dan teks menyerupai formula tetap aman.
- Pengujian CSV+Excel: **25 tests, 129 assertions**. Full suite akhir: **148 tests passed, 798 assertions**, PHP 8.3.30 dengan ZIP dan SQLite :memory:, tanpa skip. Sintaks support/controller/test, Composer validate/check-platform-reqs, serta git diff --check lulus. Route baru tetap dalam blok alumni milik A.
- Review independen akhir tidak menemukan masalah konkret pada akses, formula, kolom tersembunyi, atau pemotongan baris. Batas skala: workbook ekspor Excel dibangun di memori; gunakan CSV streaming untuk data besar, atau ganti writer saat volume melebihi memori PHP. Batas ini dicatat pada kode.
- A2.1–A2.3 **DONE pada implementasi dan verifikasi lokal**. Belum di-commit/push; browser, MySQL, pengiriman email nyata, dan build frontend belum diuji. Pengecualian file jalur B hanya composer.json/lock yang disetujui pengguna; tidak ada paket existing diperbarui.

#### A2.3 lanjutan: nama wilayah — DONE pada pengujian lokal

- Pengguna sudah membuka halaman melalui browser; impor Excel belum sempat dicoba, bukan laporan kegagalan impor.
- Atas permintaan pengguna, ekspor Excel/CSV dan template baru memakai kolom wilayah berisi nama, menggantikan ID. Impor mendukung nama wilayah dan tetap menerima template legacy wilayah_id.
- Nama wilayah dicocokkan terhadap master wilayah sesuai akses aktor, tanpa membuat wilayah baru; nama tidak ditemukan atau ambigu dilaporkan per baris. Koorda tetap terbatas pada wilayah akunnya.
- UI referensi wilayah menampilkan nama, dan penjelasan template diperbarui. Pencocokan nama mengabaikan huruf besar/kecil dan spasi di awal/akhir. Ekspor memuat relasi wilayah per batch agar tidak membuat query tambahan untuk tiap alumni.
- Test CSV/Excel: **29 tests, 148 assertions**. Full suite PHP 8.3.30 + ZIP, SQLite :memory:: **152 tests passed, 817 assertions**. Lint keempat file PHP domain impor/ekspor serta git diff --check lulus. Review independen tidak menemukan masalah konkret pada scope wilayah, pemetaan nama, atau kompatibilitas format lama.
- Uji browser berikutnya: unduh template baru, isi wilayah dengan nama seperti Makassar (telepon sebagai teks), unggah dengan email/telepon baru; coba unggah ulang untuk melihat penolakan duplikat. Coba ekspor setelah filter wilayah dan pastikan nama wilayah tampil.
- Impor melalui browser/MySQL belum diuji oleh agen; belum ada commit/push tambahan.

#### Verifikasi Excel pada MySQL — DONE

- Atas permintaan pengguna, AlumniCsvTest dan AlumniExcelTest dijalankan pada **PHP 8.3.30 dengan ZIP dan MySQL 8.4.3**, bukan SQLite: **29 tests passed, 148 assertions**, tanpa skip.
- Database khusus rf_a2_excel_test_20261016_c83e dipastikan belum ada, dibuat hanya untuk test, dan dipakai melalui environment proses (APP_ENV=testing, DB_CONNECTION=mysql, DB_URL kosong). Tidak ada perubahan .env atau penggunaan database pengembangan.
- Koneksi nyata dikonfirmasi melalui MySQL: database uji berisi 21 tabel hasil migration dan nol akun setelah rollback test. Database yang dibuat untuk pengujian ini dihapus setelah selesai.
- Cakupan: impor nama wilayah/legacy ID, validasi dan duplikat, audit rollback, pembatasan wilayah/role/status, formula dan nomor telepon teks, header/struktur/ukuran workbook, template, serta ekspor sesuai filter/wilayah.
- Tidak ada perubahan kode yang dibutuhkan. Impor lewat browser dan pengiriman email nyata tetap belum diuji; hasil ini merupakan pengujian HTTP otomatis yang memakai MySQL.

#### Integrasi dev-A ke develop — 2026-10-16

- Atas instruksi pengguna, dev-A digabung ke develop tanpa menunggu B, melalui merge commit fc94567. Merge tidak memiliki konflik; branch dev-A dipertahankan untuk pekerjaan lanjutan.
- Pengujian penuh pertama di MySQL menemukan tiga assertion yang membandingkan urutan key objek JSON. MySQL dapat menyimpan key dengan urutan berbeda. Test AlumniAuditTest, ProfileTest dan VerificationWorkflowTest kini mengurutkan key sebelum assertSame; isi, struktur dan tipe tetap diperiksa ketat, tanpa perubahan kode aplikasi.
- Hasil akhir hasil merge: **152 tests passed, 817 assertions**, PHP 8.3.30 + ZIP, **MySQL 8.4.3**. Ketiga kelas test terdampak juga lulus pada SQLite :memory:: **26 tests, 131 assertions**.
- Database rf_merge_a_test_20261016_b67d dibuat hanya untuk verifikasi integrasi lalu dihapus; .env dan database pengembangan tidak disentuh.
- `npm.cmd run build` lulus (167 modules). Peringatan data Browserslist/baseline yang lama dicatat untuk pemeliharaan jalur B; paket frontend tidak diperbarui. Composer validate/check-platform-reqs, route:list materi, dan git diff --check lulus.
- PLAN/PRD/TASK/AUDIT/SOP disinkronkan oleh penggabung sesuai pengecualian SOP. A0/A1/A2 ditandai DONE; B0/B1/B2 dan merge akhir A+B tetap belum dinyatakan selesai.
- QA impor melalui browser, email nyata, dan kesiapan rilis akhir tetap tertunda. Penerimaan tautan YouTube lengkap belum diimplementasikan; pengelolaan materi memakai ID.
