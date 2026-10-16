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

## Tahap Berikutnya

A1 masih TODO. Sesuai permintaan pengembangan bertahap, batch ini berhenti setelah A0; A1/A2 belum dimulai. Dokumen pusat tetap dibekukan sesuai aturan paralel dan disinkronkan oleh penggabung saat merge.
