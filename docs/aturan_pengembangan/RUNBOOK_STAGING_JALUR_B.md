# Runbook staging, backup dan pemulihan — Jalur B

Tanggal: 2026-10-17. Status: persiapan lokal; **belum izin rilis**.

## Batas dan syarat masuk

Belum ada target/akses staging yang diverifikasi pada sesi ini. Semua perintah server di bawah adalah prosedur untuk operator pada lingkungan yang telah ditentukan, bukan catatan eksekusi staging. Jangan menyalin kredensial staging/produksi ke Git, terminal history, issue atau hasil CI.

Sebelum deployment:

1. Integrasikan batch B0–B2 dengan A. Tutup P0 A0 (scope, gate konten, materi, seeder aman) dan B0; uji endpoint pada hasil merge. Catatan aksesibilitas form login A pada QA_JALUR_B_B25.md juga perlu ditangani.
2. Tinjau advisory dependency yang telah dicatat B0. Jangan menganggap build/test lulus sebagai bukti advisory tertutup.
3. Pastikan commit rilis sudah tersimpan/direview, CI remote PHP 8.3 + MariaDB/MySQL lulus dan lockfile disetujui. Workspace dev-B saat runbook dibuat masih memiliki perubahan belum di-commit; jangan deploy langsung dari workspace ini.
4. Tetapkan URL, operator, akses SSH/panel, path release/shared, database terpisah, lokasi backup terenkripsi, pengelola kunci, retensi, batas kehilangan data (RPO) dan batas waktu pulih (RTO). Jangan menyebut target terpenuhi sebelum drill staging diukur.
5. Pastikan mesin server memakai PHP 8.3 beserta ekstensi lockfile, client dump/import yang kompatibel dengan server MySQL/MariaDB, HTTPS, storage cukup, dan akses baca/tulis direktori Laravel sesuai user service. Document root harus menuju public/.

## Konfigurasi staging

- Gunakan .env.staging.example sebagai daftar variabel, lalu provision environment **di luar repo/release**. APP_ENV=staging, APP_DEBUG=false, APP_URL memakai URL HTTPS staging, SESSION_SECURE_COOKIE=true. Database/user staging harus berbeda dari lokal/test/produksi.
- APP_KEY dibuat sekali untuk instalasi staging baru dan disimpan pada secret manager. Pada deployment berikutnya gunakan kunci existing; jangan menjalankan key:generate atau mengganti APP_KEY sebagai langkah deploy rutin. Pemulihan memerlukan kunci lama yang sesuai data terenkripsi. Rotasi kunci yang pernah terlacak harus didahului inventarisasi pemakaian/data terenkripsi.
- Template mail log hanya cocok untuk staging dengan data sintetis; log harus privat. Email nyata memerlukan konfigurasi transport yang diverifikasi dan aturan pengiriman staging. Queue database memerlukan worker terkelola dan monitoring failed jobs; jangan menganggap queue sudah diproses hanya karena halaman berhasil dibuka.
- Shared storage mencakup storage/app/public, storage/app/private, log dan cache runtime yang diperlukan. Foto laporan berada pada storage/app/private/report-photos dan hanya disajikan lewat endpoint berizin. Symlink public/storage hanya menuju storage/app/public, tidak pernah storage/app/private.
- Konfigurasi reverse proxy/HTTPS perlu diperiksa operator; perubahan bootstrap/middleware mengikuti pemilik A. Pastikan URL asset, form POST, cookie dan redirect tetap HTTPS pada staging nyata.

## Menyiapkan release

Bangun artefak dari checkout bersih commit rilis yang telah direview. Urutan di direktori build:

```sh
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
composer validate --strict
composer check-platform-reqs --no-dev
npm ci
npm run build
```

Gunakan lockfile. Jangan menjalankan composer update/npm update pada server. Artefak menyertakan vendor dan public/build/manifest.json beserta asset hashed; node_modules tidak diperlukan pada runtime. Hindari membawa public/hot, environment lokal, dump SQL atau backup ke public/release.

Setelah environment/shared storage dipasang pada release staging yang belum dipromosikan:

```sh
php artisan route:list
php artisan migrate:status
php artisan storage:link
php artisan config:cache
php artisan view:cache
```

Cache hanya dijalankan setelah environment final tersedia. Route caching belum diwajibkan/diverifikasi pada repo ini; periksa kompatibilitas route closure sebelum menambah route:cache. Config cache dibuat ulang untuk tiap environment, tidak disalin dari lokal/CI.

## Backup sebelum perubahan database

Bekukan penulisan selama snapshot database + file agar referensinya konsisten: aktifkan maintenance pada release aktif, hentikan scheduler dan drain/hentikan worker sesuai service manager. Pastikan tidak ada transaksi tulis luar aplikasi atau perubahan schema selama backup. Pada staging baru, sediakan cara bypass maintenance hanya untuk operator yang berhak.

```sh
php artisan down
```

Simpan dump/file/environment/revisi release sebagai satu set backup privat di luar public/ dan release. Contoh MySQL (nama database/path diisi operator dari inventaris yang telah diverifikasi):

```sh
mysqldump --defaults-extra-file=/secure/mysql-backup.cnf \
  --single-transaction --routines --triggers --events --hex-blob \
  --no-tablespaces --set-gtid-purged=OFF \
  --result-file=/secure/backup/database.sql DATABASE_TERVERIFIKASI
```

--defaults-extra-file harus menjadi opsi pertama. File .cnf dibaca hanya oleh user backup dan tidak dikomit; jangan memakai -pPASSWORD pada argumen. Contoh ini diuji dengan client/server MySQL 8.4.3 lokal. Untuk MariaDB gunakan mariadb-dump/client versi yang sesuai dan sesuaikan opsi vendor (opsi GTID MySQL bukan kontrak MariaDB). Dump transaction hanya memberikan konsistensi transaksi untuk tabel transactional; inventaris engine/routine/event dan privilege harus diperiksa pada staging.

Backup juga mencakup:

- storage/app/public (covers, galleries dan upload publik lain).
- storage/app/private (termasuk report-photos), dengan izin privat saat restore.
- Environment/APP_KEY serta kunci pendukung yang diperlukan, dienkripsi dan dikelola dengan akses terpisah. Jangan mencetak nilainya. Kunci pembuka backup disimpan di tempat terpisah dari arsipnya.
- Commit/revisi artefak, manifest file/checksum SHA-256, versi DB/PHP/client, waktu UTC snapshot, operator dan tujuan pemulihan.

Periksa exit code dump, ukuran bukan nol, checksum dan kemampuan membaca arsip. Kirim salinan terenkripsi ke lokasi backup terpisah, verifikasi unduhan/aksesnya dan lakukan restore uji sebelum menyatakan backup dapat dipakai. Jangan menghapus backup lama yang masih dibutuhkan kebijakan retensi. Maintenance baru boleh diakhiri setelah backup dan langkah deployment selesai atau dibatalkan dengan aman.

## Menerapkan perubahan

Setelah backup sah dan review migration menyatakan aman untuk rilis ini:

```sh
php artisan migrate --force
php artisan config:cache
php artisan view:cache
php artisan queue:restart
```

Tidak menjalankan migrate:fresh, migrate:refresh atau seed akun pada database existing. Seeder domain A yang belum dinyatakan aman tidak menjadi langkah deploy. Promosikan release secara atomik melalui mekanisme hosting yang dipilih, reload PHP-FPM bila diperlukan, lalu jalankan smoke test. Aktifkan kembali worker/scheduler dengan versi release yang sama.

Smoke test staging: homepage/refleksi/galeri, login setiap role sintetis, guest ditolak dari laporan, koorda lintas wilayah ditolak, draf privat, create/edit/submit, approve/reject beralasan, rekap/CSV sesuai filter, foto privat berizin, upload konten, mobile/keyboard, HTTPS/cookie dan pemrosesan queue. Periksa log serta error HTTP/JS tanpa menyalin data pribadi. Jika lulus:

```sh
php artisan up
```

Catat commit, hasil CI/smoke, waktu maintenance, migration, bukti backup dan operator. Jangan membuka akses produksi bila P0 atau smoke wajib masih gagal.

## Restore atau rollback

Pertama tentukan jenis pemulihan: rollback kode saja jika schema tetap kompatibel, atau restore database + file + kunci/revisi yang cocok. Jangan menjalankan migrate:rollback secara otomatis; down migration dapat menghilangkan data dan bukan pengganti backup.

1. Isolasi lingkungan, maintenance, hentikan scheduler/worker dan penulisan eksternal. Backup kondisi gagal untuk investigasi sebelum menimpanya. Restore uji terlebih dahulu ke **database kosong baru** dan direktori file baru, bukan database yang sedang dipakai.
2. Verifikasi identitas backup/commit serta checksum sebelum mengekstrak/import. Cocokkan vendor/versi DB, kapasitas, privileges dan key material. Tolak arsip rusak; tidak mencoba import sebagian lalu membuka aplikasi.
3. Operator membuat database/user restore terpisah; import dengan client kompatibel. Contoh MySQL shell POSIX pada database baru yang telah diperiksa:

```sh
mysql --defaults-extra-file=/secure/mysql-restore.cnf \
  --default-character-set=utf8mb4 DATABASE_RESTORE_BARU < /secure/backup/database.sql
```

4. Pulihkan upload publik/privat, environment dan APP_KEY yang cocok. Pastikan storage privat tidak tersymlink publik; buat public/storage menuju direktori publik saja. Pasang artefak commit yang sesuai snapshot. Jangan mengubah kunci untuk “memperbaiki” kegagalan dekripsi.
5. Periksa jumlah/baris penting, relasi user-role-wilayah, status/reviewer/alasan laporan, jumlah peserta, referensi file dan checksum byte. Uji dekripsi canary yang sah; periksa login dan seluruh smoke test di lingkungan restore yang terisolasi.
6. Bersihkan/bangun ulang config/view cache pada release restore. Session/cookie lama dapat perlu invalidasi sesuai penilaian operator; cache bukan sumber data utama. Jangan menjalankan queue hasil restore sebelum menilai job yang mungkin sudah pernah diproses dan risiko pengiriman ulang.
7. Setelah verifikasi dan persetujuan operator, alihkan konfigurasi/release ke hasil restore. Pertahankan kondisi gagal untuk investigasi sesuai retensi. Buka aplikasi, aktifkan worker/scheduler secara terkendali, monitor, ukur downtime/data hilang dan catat hasil terhadap RTO/RPO yang disepakati.

## Latihan lokal yang dapat diulang

```powershell
php scripts/rehearse-recovery.php --run-local-drill --mysql-bin=C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin
```

Script membaca konfigurasi MySQL lokal dari phpunit.xml, membatasi host ke 127.0.0.1, membuat dua database baru dengan nama acak berakhiran _testing, memakai environment/kunci sintetis dan tidak membaca .env nyata. Ia menjalankan migration tanpa seeder, menulis fixture, membuat dump+arsip, memeriksa checksum/corruption, import ke database kosong, memverifikasi data/relasi/file/dekripsi, lalu menghapus kedua database dan direktori sementara miliknya. Jalankan dari workspace ini pada mesin lokal yang mengizinkan pembuatan database uji. Tidak menerima target database existing atau arsip eksternal.

Arsip drill hanya berisi data sintetis dan tidak disimpan setelah sukses. Angka durasi drill kecil bukan janji RTO produksi. Script ini **bukan utilitas backup produksi** dan tidak melakukan upload backup, restore staging, pergantian release atau rotasi kunci.
