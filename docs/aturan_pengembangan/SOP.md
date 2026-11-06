# SOP Pengembangan dan Sinkronisasi

Prosedur ini digunakan saat menjalankan pekerjaan. Ambil tugas menurut AGENTS/PLAN, kontrak menurut PRD, dan syarat khusus menurut RFIX. Hasil eksekusi hanya dicatat di TASK.

## 1. Lingkungan dan dependensi

- Target sintaks dan pengujian bersama adalah **PHP 8.3** dengan MySQL/MariaDB. Lint pada versi PHP lain tidak membuktikan runtime target.
- Laravel 12, Blade/Tailwind/Vite dan dependency lain mengikuti composer.json/package.json beserta lockfile. Versi Node/CI mengikuti [.github/workflows/tests.yml](../../.github/workflows/tests.yml); jangan memigrasikan framework/Tailwind untuk perapian biasa.
- Instalasi memakai composer install dan npm ci. Perubahan versi dilakukan pada pekerjaan dependency yang dituju, kemudian lockfile, platform, audit, build dan regresi terkait diverifikasi.
- Aktifkan ekstensi yang diminta lockfile, termasuk ZIP untuk XLSX; jangan melewati pemeriksaan platform. Jika menggunakan PHP 8.3 lokal yang ZIP-nya belum dimuat otomatis, aktifkan pada proses CLI melalui -d extension=zip.
- Gunakan database develop dan database tes sendiri. Target tes harus APP_ENV=testing, koneksi mysql/mariadb, dan nama berakhiran _testing.

## 2. Instalasi develop baru

Contoh berikut untuk Bash/Git Bash pada checkout baru; pilih binary PHP target pada PATH terlebih dahulu.

~~~bash
git clone https://github.com/afikafiranti/rausyanfikrinstitute.git
cd rausyanfikrinstitute
test -f .env || cp .env.example .env
~~~

Isi koneksi database develop milik sendiri pada .env. Jangan menimpa konfigurasi existing. Setelah database baru tersedia:

~~~bash
composer install --prefer-dist --no-interaction
npm ci
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
~~~

key:generate hanya untuk environment baru, bukan langkah sinkronisasi/deployment rutin. Seeder contoh terbatas local/testing, memakai identitas sintetis dan tidak dianggap konten resmi. Pengulangan seeder harus menjaga profil/password/role akun existing serta tidak menggandakan fixture.

Jalankan php artisan serve dan npm run dev pada terminal terpisah. Untuk memakai aset hasil build, jalankan npm run build; jangan menyertakan penanda public/hot yang menunjuk dev server tidak aktif pada artefak.

Pada checkout existing, periksa diff dan migration yang akan diterapkan. migrate --seed untuk instalasi baru bukan prosedur upgrade database server.

## 3. Reset develop yang sudah diizinkan

Data develop proyek ini boleh dibuang dan dibuat ulang. Izin tersebut sudah diberikan; setelah target benar-benar teridentifikasi, jangan meminta izin ulang. Izin ini tidak berlaku pada produksi atau sistem lain.

1. Periksa konfigurasi yang benar-benar dimuat: environment, driver, host, nama database dan config cache. Jangan mengganti label environment untuk melewati pengaman atau hanya mempercayai nama file .env.
2. Pastikan target adalah database develop proyek yang dimaksud, bukan database bersama/produksi/tes milik pelaksana lain. Identifikasi dampak migration destruktif; down migration tidak mengembalikan isi tabel yang dihapus.
3. Pada target yang telah benar, jalankan:

~~~bash
php artisan migrate:fresh --seed
php artisan db:seed
php artisan route:list
~~~

Periksa pembentukan dari kosong, keunikan/FK, hasil seeder, dan pengulangannya. Dataset develop lama tidak memerlukan backfill atau jalur upgrade. Schema/migration boleh ditata ulang, tetapi semua model, controller, request, view, navigasi, factory dan tes pemakainya harus ikut selaras. Perbandingan rancangan dan pilihan acuan dicatat sebagai KEP di dokumen induk.

## 4. Verifikasi

Reproduksi atau ukur kondisi awal sesuai risiko, lalu uji perubahan melalui antarmuka yang dipakai pengguna/pemanggil. Tambahkan regresi untuk logika berisiko; tidak perlu tes yang hanya menyalin implementasi atau perubahan teks rendah dampak.

Untuk suite gabungan, siapkan database uji terpisah dan user-nya. Contoh Bash/Git Bash:

~~~bash
TEST_DB_DATABASE=proyek_testing TEST_DB_USERNAME=user_uji bash run-ci.sh
~~~

Variabel opsional TEST_DB_HOST/PORT/CONNECTION/PASSWORD mengikuti [runner](../../run-ci.sh). Jangan menaruh password pada riwayat perintah; provision melalui environment lokal yang aman. Runner menyiapkan kunci sintetis dalam proses, mengabaikan DB_* aplikasi, memeriksa config cache dan koneksi, lalu menjalankan dependency/platform/audit/build/route/tes.

- Pertahankan TestDatabaseSafety sebelum RefreshDatabase. Jangan berbagi DB tes antar pelaksana atau menonaktifkan pengaman agar tes berjalan.
- Untuk tes domain langsung, terapkan environment uji yang sama sebelum menjalankan PHPUnit; jangan membiarkan .env/config cache aplikasi menentukan target.
- Gunakan disk/file QA terpisah jika tes paralel memakai Storage::fake; nama DB berbeda tidak memisahkan direktori file palsu.
- PHP lint, unit/integrasi, build dan uji browser membuktikan hal berbeda. Periksa keyboard/mobile, aksi batal/lanjut serta kondisi kosong/gagal bila interaksi berubah.
- Perubahan schema memerlukan rebuild, seed ulang dan uji constraint; perubahan transaksi/penguncian memerlukan pengujian independen pada beberapa koneksi bila klaimnya mencakup konkurensi.
- Audit dependency dilaporkan dengan tanggal, versi, advisory, jalur langsung/transitif dan pemakaian yang relevan. Severity saja tidak membuktikan keterjangkauan serangan.
- Warning/deprecation harus terlihat dan ditangani. Gate yang dijalankan mengikuti runner dan phpunit.xml; alasan pengecualian/triase tersimpan pada AUDIT, bukan disembunyikan.
- Klaim keberhasilan harus memakai output, exit code dan runtime yang benar-benar diperiksa. Bukti lokal tidak menggantikan CI remote, SMTP eksternal atau staging.

Perubahan dokumentasi saja diperiksa melalui konsistensi sumber, ID/anchor/tautan, aturan keselamatan, serta diff; tidak memerlukan pengulangan suite aplikasi bila source executable tidak berubah.

## 5. Pencatatan dan penutupan

Catat satu kali pada TASK:

~~~text
Tanggal dan ID pekerjaan; branch/commit awal-akhir atau keadaan working tree;
target environment/database; keputusan KEP dan berkas/schema/data yang berubah;
perilaku sebelum-sesudah; migration/seeder yang dijalankan;
perintah, runtime dan hasil aktual, termasuk warning/deprecation/skip;
lokasi artefak, batas bukti, risiko dan tindak lanjut yang belum terpenuhi.
~~~

Gunakan [arti status pada TASK](TASK.md#arti-status). Persyaratan produk diperbarui pada PRD, spesifikasi/keputusan pada dokumen induk, urutan pada PLAN, dan penilaian risiko pada AUDIT. Jangan menyalin log/status ke dokumen tersebut. Riwayat A/B tetap arsip bertanggal.

Implementasi belum selesai sebelum kriteria yang relevan terverifikasi. Persetujuan deployment memerlukan konteks dan bukti lingkungan tujuan; ikuti [runbook](RUNBOOK_STAGING_JALUR_B.md) untuk konfigurasi, backup, promosi release dan restore.
