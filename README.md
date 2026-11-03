# Website Alumni dan Kajian — Rausyan Fikr

Aplikasi Laravel untuk data alumni, materi kajian bertingkat, laporan wilayah, tulisan publik, Galeri dan refleksi pribadi anggota.

## Mulai untuk agent

Baca [AGENTS.md](AGENTS.md), lalu [dokumen induk perbaikan](docs/aturan_pengembangan/DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md), [TASK](docs/aturan_pengembangan/TASK.md), [PLAN](docs/aturan_pengembangan/PLAN.md#rencana-aktif), [PRD](docs/aturan_pengembangan/PRD.md), [AUDIT](docs/aturan_pengembangan/AUDIT.md) dan [SOP](docs/aturan_pengembangan/SOP.md).

Arahan aktif 4 November 2026:

- Prioritaskan RFIX-01 (konfirmasi hapus) dan penataan DB sejak RFIX-02.
- Skema belum final; tabel/kolom/relasi/migration boleh ditata ulang menurut kebutuhan web.
- Data develop lama boleh dibuang dan dibangun kembali melalui seeder. Tidak diwajibkan jalur upgrade dataset lama.
- Seeder, validasi, izin, model, query dan tes harus sesuai schema yang dipilih.
- Perbaikan aplikasi masih direncanakan; sinkronisasi dokumentasi tidak menutup temuan kode.

## Fitur yang tersedia pada source

| Area | Kondisi |
|---|---|
| Akun/profil/alumni | Register pending, login, reset/verifikasi email, data profil, verifikasi anggota, filter/sort/pagination, level, audit dan notifikasi |
| Impor/ekspor | CSV/XLSX, template dan laporan baris gagal |
| Materi | Playlist/video dan akses akun aktif menurut level; metadata manual |
| Dashboard | Statistik pengelola dan dashboard pribadi alumni terpisah |
| Laporan | Draf/edit/kirim/review/rekap/CSV/foto privat; khusus pengelola aktif sesuai wilayah |
| Refleksi saya | Semua role: draf privat, kiriman opsional dan tanggapan pengelola berwenang |
| Publik | Hero, maksimal empat Artikel, maksimal empat Galeri, Footer; daftar/detail Artikel dan URL Refleksi publik |
| Pengelolaan publik | Post/Artikel/Refleksi dan Galeri hanya oleh super admin aktif |

Refleksi saya adalah catatan internal. Mengirimnya untuk ditanggapi tidak membuatnya tampil publik. Artikel dan Refleksi publik merupakan dua jenis tulisan yang dikelola untuk pembaca umum.

Progres menonton dan metadata YouTube otomatis ditunda. Revisi/kirim ulang laporan rejected serta CRUD web khusus master belum tersedia. Agenda/tim-kontak dan section tambahan tidak termasuk homepage aktif.

## Kondisi verifikasi dan risiko

Basis kode yang diperiksa: bd9cdc1bc728916060e50d4566eccc64a172df9b. [CI 39086196915](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915) completed/success dengan **280 warnings, 7 passed, 2172 assertions**. Ini bukti baseline, bukan hasil test patch RFIX.

[Audit bertanggal](docs/aturan_pengembangan/AUDIT_REPOSITORI_2026-11-03.md) memuat injeksi JavaScript konfirmasi hapus, aturan cascade laporan, akun pengelola/status, dependency, query/index dan URL publik. Source diperiksa ulang 4 November; perbaikan aplikasi belum diterapkan. SMTP eksternal/staging/pemulihan sebelum live belum dinyatakan selesai.

## Teknologi

- Laravel 12, target PHP 8.3; versi terpasang mengikuti composer.lock.
- Blade, Tailwind 3, Vite, Alpine/Flowbite.
- MySQL/MariaDB.
- PhpSpreadsheet dan ekstensi ZIP untuk XLSX; ekstensi lain mengikuti lockfile.
- Dashboard aktif mengambil ApexCharts melalui CDN; Chart.js juga masih terpasang. Penyeragaman dan penguncian versi merupakan RFIX-14.
- Driver database untuk sesi/cache/antrean mengikuti konfigurasi baseline; dapat ditinjau bersama rancangan schema/hosting.

## Instalasi develop baru

~~~bash
git clone https://github.com/afikafiranti/rausyanfikrinstitute.git
cd rausyanfikrinstitute
cp .env.example .env
~~~

Isi koneksi database develop pada .env terlebih dahulu, lalu:

~~~bash
composer install --prefer-dist --no-interaction
npm ci
php artisan key:generate
php artisan migrate --seed
npm run dev
~~~

Jalankan php artisan serve pada terminal lain. Key generation di atas untuk environment develop baru. Seed baseline belum mencakup seluruh fixture rencana RFIX-23.

Reset develop yang sudah diidentifikasi benar telah diizinkan pengguna. Prosedur target dan rebuild memakai [SOP](docs/aturan_pengembangan/SOP.md#3-reset-develop-yang-sudah-diizinkan). Database testing tetap terpisah berakhiran _testing.

## Catatan pengembangan dan rilis

TASK adalah satu register progres aktual. Instruksi dan bukti lama dapat dibaca pada riwayat Git serta catatan jalur; jangan mengulang task yang sudah terintegrasi. Menulis dokumentasi atau menggabungkan kode tidak menjadi bukti rilis.

[Runbook staging](docs/aturan_pengembangan/RUNBOOK_STAGING_JALUR_B.md) merupakan acuan lingkungan tujuan. Ketentuan konservatif data existing dalam runbook berlaku pada konteks operasionalnya; izin reset develop terbaru tidak berlaku pada produksi/sistem lain.

## Identitas dan lisensi

Proyek internal komunitas Rausyan Fikr. Metadata lisensi skeleton dan keterangan pemilik perlu diselaraskan melalui RFIX-20/KEP-08; tidak menetapkan pilihan lisensi baru dalam pembaruan dokumentasi ini.
