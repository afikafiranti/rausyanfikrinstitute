# Aturan Agen Pengembangan Rausyan Fikr

## Mulai dari arahan aktif 4 November 2026

Pengguna meminta dokumen perbaikan disimpan ke GitHub dan pencatatan diselaraskan dengan kode. Basis kode yang diperiksa adalah develop pada bd9cdc1bc728916060e50d4566eccc64a172df9b. Perubahan dokumentasi ini belum memperbaiki aplikasi, mengubah schema, menjalankan reset/seeder, atau membuktikan rilis.

Baca berurutan sebelum bekerja:

1. [Dokumen induk perbaikan](docs/aturan_pengembangan/DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md): keputusan pengguna, kontrak fungsi, rincian RFIX-01–23, pengujian dan syarat selesai.
2. [TASK](docs/aturan_pengembangan/TASK.md): satu register progres aktual; RFIX aplikasi masih TODO; RFIX-20 PARTIAL karena subtugas pencatatan sudah selesai.
3. [PLAN](docs/aturan_pengembangan/PLAN.md#rencana-aktif): urutan pelaksanaan; mulai RFIX-01 dan perancangan database RFIX-02.
4. [PRD](docs/aturan_pengembangan/PRD.md): fungsi yang tersedia dan batas cakupan.
5. [AUDIT](docs/aturan_pengembangan/AUDIT.md): risiko aktif dan kekuatan buktinya.
6. [SOP](docs/aturan_pengembangan/SOP.md): pelaksanaan, reset develop, pengujian dan pencatatan.

Instruksi eksplisit pengguna terbaru berlaku lebih dahulu. Catatan A/B, audit UI dan runbook bertanggal lama merupakan bukti historis; jangan mengulang fitur yang sudah tersedia atau mengambil larangan reset lama sebagai aturan develop saat ini.

## Keputusan database yang wajib dipahami

- Skema database masih dirancang. Nama/jumlah tabel, kolom, model, relasi dan migration belum final.
- Data develop yang ada boleh dihapus dan dibentuk ulang. Pengguna tidak mewajibkan pelestarian atau jalur upgrade dataset develop lama.
- Migration develop boleh dirapikan, digabung atau disusun ulang. Seeder wajib membangun data dasar dan contoh yang cukup untuk alur web.
- Pilih skema menurut fungsi, privasi, integritas, kesederhanaan kode dan pengukuran. Struktur baseline menjadi pembanding, bukan desain yang harus dibekukan.
- Izin reset develop sudah ada. Verifikasi host, database, environment dan config cache untuk memastikan target benar; jangan meminta izin ulang untuk target develop proyek yang telah teridentifikasi.
- Izin tersebut tidak berlaku untuk produksi/sistem lain. Database tes tetap terpisah, berakhiran _testing, dan pengaman sebelum RefreshDatabase tetap dipakai.
- Tabel framework dan fitur tertunda diperiksa menurut pemakaian. Menghapus tabel juga harus menangani relasi/model/tes/seeder yang merujuknya.
- Perubahan schema harus diikuti penyesuaian kode dan seeder dalam cakupan yang sama. Jangan menambah abstraksi/dependency tanpa kebutuhan.

## Kontrak fungsi yang tetap diuji

- Role akun super_admin, admin, koorda, alumni mendukung multi-role; role berbeda dari level materi.
- Post/Artikel/Refleksi publik dan Galeri hanya dikelola super admin aktif. Admin tetap dapat mengelola materi.
- Refleksi saya tersedia untuk empat role: draf hanya penulis; pengiriman memerlukan active dan wilayah; isi terkunci setelah dikirim.
- Koorda wilayah kiriman serta admin/super admin aktif dapat membaca/menanggapi kiriman orang lain. Tidak ada self-review atau penerbitan otomatis ke publik.
- Laporan hanya untuk pengelola aktif sesuai wilayah/kewenangan; alumni ditolak pada menu dan endpoint.
- Pending/active/suspended, aturan review, audit dan privasi tidak boleh melemah akibat perapian schema.
- Homepage tetap Hero, maksimal empat Artikel, maksimal empat Galeri, Footer. URL Refleksi publik tetap tersedia.
- Agenda/tim-kontak, progres menonton, metadata YouTube otomatis, revisi laporan rejected dan CRUD web master tetap mengikuti cakupan PRD.

## Kenali branch, sumber dan pekerjaan bersama

1. Jalankan git branch --show-current dan git status --short sebelum mengubah file. Pertahankan perubahan pengguna; jangan reset/clean/checkout paksa.
2. Sinkronkan dari develop yang benar. Dev-A/dev-B masih berada pada snapshot lebih lama; jangan menganggap sudah memuat arahan ini sebelum memeriksa.
3. Pilih ID RFIX dan catat IN_PROGRESS di TASK saat mulai implementasi. Jangan berhenti pada penyusunan rencana ulang jika pengguna sudah meminta pekerjaan tersebut dijalankan.
4. Gunakan perubahan terkecil yang menyelesaikan target. Tugas lintas domain harus memperbarui seluruh pemakai schema/kontrak yang terdampak.
5. Jika terdapat pekerjaan pengembang lain, ikuti penugasan aktif dan hindari menimpa berkasnya. Peta domain A/B di bawah membantu koordinasi; ia tidak membekukan struktur DB yang diizinkan berubah.
6. Tidak perlu memulai sub-agent atau pekerjaan paralel hanya karena ada dua domain. Ikuti instruksi pelaksanaan dan alat yang tersedia.

### Peta domain untuk koordinasi

| Domain | Berkas/tanggung jawab baseline |
|---|---|
| A | Auth, alumni, profil, verifikasi, materi, notifikasi; model User/Role/Level/Wilayah/AuditLog/Playlist/Video/VideoProgress; gate, ScopeWilayah, bootstrap/app.php; routes/auth.php; seeder akun/master dan tes domain A |
| B | Dashboard/Chart/Landing/Report/Management/MemberReflection; model Post/Gallery/KajianReport/MemberReflection; layout/navigasi/CSS/JS; Post/GallerySeeder; toolchain, CI, runner, template environment dan tes domain B |
| Bersama | Blok routes/web.php, rancangan schema, migration/seeder terkait, TASK/PLAN/PRD/AUDIT/SOP dan dokumen induk |

## Bukti sebelum menutup pekerjaan

- Jangan menyebut bug selesai hanya karena dokumen atau kode sudah diubah.
- Uji perubahan risiko dengan regresi yang relevan, kemudian integrasi yang terdampak. Jangan membuat tes yang hanya menyalin implementasi.
- Target runtime bersama PHP 8.3 dan MySQL/MariaDB. Lint PHP versi lain tidak menggantikan pengujian runtime target.
- CI baseline 39086196915 completed/success; ringkasannya 280 warnings, 7 passed, 2172 assertions. Angka lokal historis 260/287 bukan bukti test baru.
- Catat perintah, runtime, commit, hasil, warning/skip dan batasnya. Audit dependency bertanggal tidak otomatis membuktikan keterjangkauan serangan.
- Perbarui TASK dan AUDIT sesuai bukti; selaraskan PRD/PLAN bila keputusan berubah. Catatan A/B menyimpan detail, tidak membuat register progres aktif lain.
- Dokumen lama dapat dibaca dari riwayat Git atau catatan jalur. Persetujuan integrasi lama tidak dianggap persetujuan deployment baru.
