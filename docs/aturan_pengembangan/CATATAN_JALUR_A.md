# Catatan Jalur A

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

A2 belum dimulai. Dokumen pusat tetap dibekukan sesuai aturan paralel dan disinkronkan oleh penggabung saat merge.
