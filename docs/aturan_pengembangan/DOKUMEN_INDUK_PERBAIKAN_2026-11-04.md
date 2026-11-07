# Dokumen Induk Perbaikan Rausyan Fikr

Keputusan dan spesifikasi RFIX dari rencana 4 November 2026, diperbarui sesuai arahan 6 November. Kandidat schema B dipilih untuk siklus pengembangan ini dan tetap dapat ditinjau.

Dokumen ini menyimpan **keputusan dan kriteria penerimaan**. Pembagian sumber, urutan baca dan kedudukan arsip mengikuti [peta dokumentasi](README.md).

ARAH-10/11 menggantikan kontrak lama Refleksi saya/Tinjau refleksi. Tidak ada migrasi ke konten publik. Artikel dan Refleksi publik tetap dua jenis tulisan di `posts`; menu pengelolaan bernama **Artikel**. Kontrak aktif dan hak setiap role dijelaskan di PRD.

## 1. Keputusan yang menjadi dasar pekerjaan

| ID | Keputusan atau arahan | Kedudukan |
|---|---|---|
| ARAH-01 | Seluruh arah perbaikan hasil analisis menjadi prioritas perbaikan | Disetujui pengguna |
| ARAH-02 | Penataan database ditempatkan pada tahap awal, bersama keamanan dan perlindungan data | Permintaan eksplisit pengguna |
| ARAH-03 | Database dan kode pengolahan data harus ringkas, jelas, mudah dirawat, serta menghindari overengineering dan susunan yang saling membelit | Permintaan eksplisit pengguna |
| ARAH-04 | Penjelasan fungsi dan tulisan antarmuka harus mudah dipahami orang yang tidak berlatar IT | Permintaan eksplisit pengguna |
| ARAH-05 | Temuan, rencana, keputusan dan implementasi harus dicatat rinci agar pelaksana berikutnya tidak menafsirkan ulang secara keliru | Permintaan eksplisit pengguna |
| ARAH-06 | Data yang ada pada database develop saat ini boleh dihapus dan dibuat ulang; pelestarian data lama develop tidak diperlukan | Koreksi eksplisit pengguna, menggantikan batas pelestarian data develop sebelumnya |
| ARAH-07 | Schema dan migration boleh disusun ulang; seeder wajib mampu membangun ulang data dasar dan data develop yang diperlukan | Arahan eksplisit pengguna |
| ARAH-08 | Skema database sekarang belum final; tabel, kolom dan relasi masih boleh diubah untuk mencari rancangan yang paling mendukung web | Penegasan eksplisit pengguna |
| ARAH-09 | Pilihan skema harus dinilai dari kebutuhan fungsi, kesederhanaan, integritas, kemudahan kode dan bukti pengujian | Penerapan operasional ARAH-03/08; bukan penetapan skema tertentu |
| ARAH-10 | Hapus Refleksi saya/Tinjau refleksi beserta tabel, data develop dan seluruh komponen khusus | Instruksi eksplisit pengguna, 6 November 2026 |
| ARAH-11 | Ubah menu pengelolaan Artikel & Refleksi menjadi Artikel | Instruksi eksplisit pengguna, 6 November 2026; jenis publik `article`/`reflection` dan URL tetap |

## 2. Prinsip pemilihan schema

Satu identitas pengguna mendukung multi-role; role akun berbeda dari level materi. Struktur baseline adalah pembanding, bukan desain tetap. Kandidat dinilai menurut fungsi, privasi, integritas, kemudahan kode, query penting, kemampuan rebuild, dan pemeliharaan. Jumlah tabel sendiri bukan ukuran kualitas.

- Refleksi saya beserta `member_reflections` dan semua pemakainya dihapus. Tabel progres video yang fiturnya tertunda hanya dipertahankan bila ada fungsi aktif yang membutuhkannya; periksa relasi, tes, dan seeder sebelum menghapus.
- Laporan organisasi menjaga histori serta identitas penulis dan reviewer. Constraint, kebijakan hapus akun, rekap, dan file harus konsisten.
- Index ditetapkan dari query, foreign key, dan unique constraint. Nilai turunan seperti `total_peserta` harus mempunyai satu invariant.
- Gunakan Eloquent, FormRequest, Gate/policy, transaksi, dan komponen Laravel yang sudah ada. Tambahkan abstraksi hanya untuk aturan bersama yang nyata; hindari lapisan repository/service, mesin permission/status, atau schema generik tanpa kebutuhan.
- Rancangan dari kosong, kode, validasi, factory, seeder, tes, dan dokumentasi harus berubah bersama. Data develop lama tidak perlu diselamatkan. Verifikasi target lokal/develop sebelum reset atau cleanup; izin ini tidak berlaku untuk produksi. Database tes tetap terpisah dengan nama berakhiran `_testing` dan pengaman sebelum `RefreshDatabase`.
- Keputusan kandidat per siklus mencatat kebutuhan, relasi, perubahan tabel/kolom/index, aturan akses, contoh query, fixture pembanding, hasil uji, tradeoff, serta alasan memilih atau merevisi. Angka performa hanya berasal dari pengukuran nyata.

### Kandidat B pada siklus 6 November

Kandidat B menghapus struktur Refleksi saya dan progres video yang tidak dipakai, mengonsolidasikan index, menetapkan telepon nullable-unique, serta memakai foreign key `RESTRICT` pada penulis dan reviewer laporan. Kandidat A mempertahankan progres dan index ganda. Kandidat B mempertahankan tabel khusus untuk identitas, role, wilayah, level, tulisan publik, galeri, materi, dan laporan. Schema yang dipilih mempunyai 19 tabel aplikasi bernama di luar tabel `migrations`; hasil rebuild, pengukuran dan lingkungan spesifik ada di [TASK](TASK.md). Pemilihan ini tidak membekukan arsitektur untuk siklus berikutnya.

## 3. Keputusan teknis

| ID | Hal yang perlu ditetapkan | Arah minimum yang disarankan | Pengaruh pada pelaksanaan |
|---|---|---|---|
| KEP-01 | Perilaku hapus akun setelah aplikasi digunakan | Tolak penghapusan akun yang menjadi penulis atau reviewer laporan; FK keduanya memakai `RESTRICT` | Menjaga histori laporan di aplikasi dan database; tidak membatasi reset develop yang benar |
| KEP-02 | Retensi laporan saat akun penulis atau penanggap dihapus | Blokir hapus akun terkait laporan apa pun; jaga identitas penulis dan reviewer pada rekap/audit | Refleksi saya tidak menjadi pengecualian karena telah dihapus sesuai ARAH-10 |
| KEP-03 | Definisi pengelola terakhir dan perubahan profil kritisnya | Lindungi pengelola global aktif terakhir dan, bila ada, super admin aktif terakhir; cek role multi-role dalam transaksi database | Implementasi memakai unique users serta uji dua proses database; rincian hasil di TASK |
| KEP-04 | Library grafik yang dipertahankan | Pelaksanaan RFIX-14 memilih paket Chart.js yang sudah ada dan membundle-nya; hapus ApexCharts CDN setelah chart dashboard berhasil dipindah | Kunci versi lewat package-lock; verifikasi grafik dan pesan saat gagal |
| KEP-05 | Batas volume ekspor XLSX | Batas aplikasi 5.000 baris; CSV untuk volume lebih besar | Nilai dipilih dari ukur sintetis lokal PHP 8.3/ZIP; hasil dan batas lingkungan ada di TASK |
| KEP-06 | Pemakaian kunci historis pada server | Operator memeriksa identitas/pemakaian tanpa menampilkan nilai rahasia; rotasi mengikuti dampak data terenkripsi | Status pemeriksaan dan tindak lanjut operasional ada di AUDIT/TASK |
| KEP-07 | Keunikan dan normalisasi telepon/data profil | Telepon opsional nullable-unique; validasi dan constraint database selaras | Migration lama gagal dengan jelas bila menemukan nomor ganda, tanpa mengubah data secara diam-diam |
| KEP-08 | Identitas dan lisensi proyek | Pemilik menetapkan keterangan yang berlaku; selaraskan metadata skeleton dengan pilihan tersebut | Pelaksana tidak menetapkan lisensi atas nama pemilik |
| KEP-09 | Nama menu dan akses navigasi Refleksi publik | Menu pengelolaan tulisan menjadi Artikel; route publik `/refleksi` dipertahankan | Perubahan label tidak menghapus jenis konten atau URL publik |
| KEP-10 | Kandidat schema yang dipakai pada siklus pengembangan berikutnya | Siklus 6 November memilih kandidat B: hapus tabel progres yang belum dipakai, konsolidasikan index, unique phone dan FK laporan RESTRICT | Keputusan berdasarkan hasil rebuild, integritas serta regresi lokal; kandidat ini tidak membekukan arsitektur akhir |

## 9. Rincian pekerjaan

ID RFIX adalah spesifikasi pekerjaan dan kriteria penerimaan. Status, commit, pengujian, dan batas hasil hanya dicatat di [register RFIX pada TASK](TASK.md#register-rfix). ID temuan RF dapat ditelusuri pada [AUDIT](AUDIT.md).

### RFIX-01 — Amankan konfirmasi hapus alumni

**Asal:** RF-01. **Prioritas:** P0. **Domain:** view bersama B dan endpoint/test alumni A.

**Target:** nama pengguna selalu diperlakukan sebagai teks. Nama berapostrof tidak merusak konfirmasi; ekspresi penanda tidak dijalankan.

**Berkas terkait:** resources/views/alumni/index.blade.php; resources/js/app.js atau modul JavaScript existing hanya jika handler memang dipindah; tests/Feature/AlumniDestroyTest.php; pengujian interaksi browser yang memakai tooling tersedia.

**Langkah:**

- Reproduksi dengan nama biasa, apostrof, kutip ganda, karakter HTML dan ekspresi penanda sintetis pada lingkungan uji.
- Gunakan pesan konfirmasi statis atau atribut data yang dibaca sebagai teks; pilih perubahan terkecil.
- Pertahankan CSRF, method DELETE, batas aktor/target dan larangan penghapusan yang sudah berlaku.
- Uji handler melalui browser aplikasi ketika runtime tersedia, termasuk batal dan lanjut hapus.
- Jalankan pengujian endpoint penghapusan serta build bila JavaScript/aset berubah.

**Syarat selesai:** tidak ada eksekusi penanda; konfirmasi tetap bekerja untuk nama sah; penghapusan di luar izin ditolak. Bukti handler terisolasi saja tidak menggantikan pemeriksaan integrasi akhir.

### RFIX-02 — Petakan kebutuhan dan susun rancangan schema develop

**Asal:** ARAH-02/03. **Prioritas:** P1, dikerjakan sejak tahap awal. **Domain:** integrasi A+B.

**Berkas terkait:** database/migrations/*.php; app/Models/*.php; config/database.php; config/cache.php; config/session.php; config/queue.php; seeder dan controller yang membaca/menulis setiap tabel.

**Langkah:**

- Catat vendor/versi database, target develop/test dan konfigurasi driver yang dipakai.
- Gunakan inventaris baseline pada audit awal sebagai pembanding, lalu petakan ke fungsi yang benar-benar dibutuhkan.
- Petakan setiap tabel/kolom ke pembaca, penulis, relasi, aturan hapus dan sumber data seeder.
- Putuskan struktur dipertahankan, disederhanakan, digabung bila satu fungsi, atau dihapus bila tidak diperlukan; tandai alasan setiap keputusan.
- Bandingkan kandidat menurut kriteria rancangan pada bagian 2 dan uji bagian yang membedakannya dengan data sintetis.
- Catat pilihan sementara/terpilih beserta alasan dan masalah yang belum lolos uji.
- Susun migration dari kosong, constraint/index dan seeder untuk kandidat yang sedang diuji; kompatibilitas dataset lama develop tidak diwajibkan.

**Syarat selesai:** tersedia kebutuhan data, kandidat yang dibandingkan, alasan keputusan KEP-10, rancangan yang dipilih untuk siklus tersebut, serta peta perubahan migration/model/tes/seeder. Rancangan dapat direvisi berdasarkan temuan pengujian; tidak dibatasi bentuk atau isi database lama develop.

### RFIX-03 — Lindungi histori laporan dan atur penghapusan akun

**Asal:** RF-04. **Prioritas:** P1. **Dependensi:** RFIX-02, KEP-01/02, lingkungan uji RFIX-22.

**Berkas terkait:** app/Http/Controllers/AlumniController.php; app/Http/Controllers/ProfileController.php; app/Models/User.php; app/Models/KajianReport.php; migration acuan hasil penataan yang mendefinisikan FK; tests/Feature/AlumniDestroyTest.php; tests/Feature/ProfileTest.php; tests/Feature/AlumniAuditTest.php; tests/Feature/KajianReportSummaryTest.php.

**Langkah:**

- Buat fixture penulis dengan laporan draft, submitted, approved dan rejected, serta pengguna yang hanya menjadi reviewer.
- Tulis tes gagal yang membuktikan kebijakan pelestarian yang dipilih pada penghapusan lewat daftar alumni dan profil sendiri.
- Terapkan aturan controller dan database yang konsisten; pemeriksaan aplikasi saja tidak cukup bila FK tetap dapat menghapus histori.
- Definisikan FK pada migration acuan hasil penyusunan ulang; data develop boleh direset dan dibentuk ulang lewat seeder.
- Tentukan pembersihan avatar/foto laporan menurut retensi, setelah transaksi berhasil dan hanya untuk path milik aplikasi; tidak ada file/tabel Refleksi saya yang dipertahankan.
- Uji bahwa rekap jumlah laporan/kehadiran periode lama tetap sesuai kebijakan setelah tindakan akun.

**Syarat selesai:** penghapusan tidak diam-diam menghapus histori organisasi; pesan menjelaskan alasan penolakan bila ditolak; kebijakan file laporan/avatar konsisten; audit tindakan terjaga. Refleksi saya dihapus sesuai ARAH-10. Tidak otomatis menerapkan soft delete ke semua tabel.

### RFIX-04 — Konsolidasikan index yang berlebih

**Asal:** RF-11. **Prioritas:** P1. **Dependensi:** RFIX-02.

**Berkas terkait:** migration users hasil konsolidasi; tiga migration index baseline sebagai pembanding; User::scopeAlumniListing; DashboardController; ChartController; tes schema/rebuild RFIX-22.

**Langkah:**

- Tentukan definisi index/constraint akhir dari query dan kebutuhan FK, termasuk urutan kolom dan keunikan.
- Tinjau query daftar, filter/sort, statistik dan ekspor menggunakan fixture sintetis yang dapat diulang.
- Letakkan index yang diperlukan satu kali pada migration acuan dan hapus definisi pembentuk index berulang dari susunan akhir.
- Bangun database kosong dan periksa index yang terbentuk; tidak wajib menguji jalur upgrade data develop lama.
- Bandingkan hasil query serta rencana eksekusi/waktu pada dataset seeder yang sama.

**Syarat selesai:** tidak ada index identik tanpa alasan; unique/FK berlaku; hasil daftar/rekap sesuai fixture. Nama dan isi migration lama boleh dirapikan/dikonsolidasikan karena target develop dibuat ulang. Hasilnya diuji dari database kosong.

### RFIX-05 — Selaraskan kolom, constraint dan aturan data

**Asal:** ARAH-03 dan hasil RFIX-02. **Prioritas:** P1.

**Berkas terkait:** migration users/role_user/levels/wilayah/kajian_reports; ProfileUpdateRequest; AlumniController; AlumniCsvController; StoreKajianReportRequest; UpdateKajianReportRequest; model terkait; tes profil, impor dan laporan.

**Langkah:**

- Cocokkan tipe, panjang, nullable, enum, default dan FK dengan aturan form, impor dan kode penyimpanan.
- Definisikan keunikan email/pasangan role serta telepon sesuai KEP-07; siapkan fixture valid dan fixture benturan untuk menguji constraint.
- Petakan nilai turunan seperti total_peserta dan pastikan jumlahnya konsisten pada create, update, submit, rekap dan ekspor.
- Samakan nilai yang dipakai formulir, seeder, enum dan filter; data develop lama dapat diganti dengan fixture yang konsisten.
- Lakukan hanya perubahan schema yang mempunyai kebutuhan dan dampak jelas; data yang bertentangan dilaporkan dengan strategi penanganannya.

**Syarat selesai:** schema baru, validasi dan seeder konsisten; constraint menolak data tidak sah; nilai turunan memenuhi invariant. Data develop lama tidak harus dipertahankan. Tidak menambah trigger, tabel enum baru, atau normalisasi berlapis tanpa kebutuhan.

### RFIX-06 — Tinjau tabel/kolom tertunda dan sisa schema

**Asal:** ARAH-03. **Prioritas:** P1 untuk keputusan struktur.

**Berkas terkait:** VideoProgress, migration video_progress, konfigurasi driver framework, kolom profil yang ditandai inventaris, seeder dan dokumentasi cakupan.

**Langkah:**

- Periksa pemakaian video_progress pada route, model, job, test, ekspor, data dan integrasi.
- Periksa fungsi kolom seperti consent_at dan makna AB sebelum menilai kebutuhan retensi/penghapusannya.
- Periksa sessions, cache, antrean dan password reset menurut driver yang dipakai.
- Catat keputusan akhir tiap kandidat beserta fungsi yang didukung atau alasan dikeluarkan dari schema aktif.
- Hapus kandidat yang tidak diperlukan beserta relasi/model/tes/seeder yang khusus bergantung padanya; reset develop telah diizinkan.

**Syarat selesai:** schema aktif berisi struktur yang mempunyai fungsi jelas dan seluruh referensinya konsisten. Untuk video_progress, temuan source tambahan menunjukkan relasi pada Video serta dua pengujian MateriManagementTest; keduanya ikut ditangani bila tabel dikeluarkan. Tidak menambah kembali fitur progres hanya untuk membenarkan keberadaan tabel.

### RFIX-07 — Kurangi query role berulang tanpa melemahkan izin

**Asal:** RF-10. **Prioritas:** P1. **Domain:** model User A; view bersama B.

**Berkas terkait:** app/Models/User.php; app/Http/Controllers/AlumniController.php; resources/views/alumni/index.blade.php; gate/provider dan navigasi hanya jika terpengaruh; tes role, akses regional dan daftar alumni.

**Langkah:**

- Ukur query nyata pada halaman dengan jumlah baris berbeda dan beberapa kombinasi role.
- Gunakan relasi yang sudah dimuat untuk keputusan tampilan atau hitung izin target sekali; pertahankan mekanisme izin endpoint.
- Hindari cache role global yang dapat mengembalikan izin lama setelah perubahan role.
- Uji multi-role, perubahan role dalam request/alur yang relevan, aktor tidak aktif dan koorda lintas wilayah.
- Bandingkan pertumbuhan jumlah query sebelum/sesudah.

**Syarat selesai:** query tambahan role tidak tumbuh empat kali per baris alumni biasa seperti jalur baseline; keputusan izin tetap benar. Estimasi 60/400 query pada audit awal tidak dipakai sebagai angka benchmark aktual.

### RFIX-08 — Lindungi keberadaan pengelola aktif

**Asal:** RF-05. **Prioritas:** P1. **Dependensi:** KEP-03.

**Berkas terkait:** ProfileController::update/destroy; AlumniController::update/destroy; AuthServiceProvider; VerificationController; model User; tests/Feature/ProfileTest.php; tests/Feature/AlumniDestroyTest.php; tests/Feature/VerificationRoleAccessTest.php.

**Langkah:**

- Tentukan invarian pengelola global dan super admin sesuai KEP-03, termasuk susunan multi-role.
- Tulis tes perubahan wilayah/angkatan, self-delete dan pengelola lain yang masih aktif.
- Lindungi transisi pada semua endpoint terkait, bukan hanya tombol/menu.
- Pertimbangkan transaksi dan penguncian bersama saat dua pengelola mengubah akun bersamaan; menghitung di luar transaksi saja dapat berpacu.
- Pertahankan larangan self-verify dan batas target verifikasi koorda; jangan membuka pemulihan lewat eskalasi role tidak berizin.

**Syarat selesai:** tindakan pengguna tidak menghilangkan seluruh jalur pengelolaan yang wajib tetap tersedia; penolakan dijelaskan; tindakan sah saat ada pengganti tetap berjalan.

### RFIX-09 — Pertahankan status suspended saat profil berubah

**Asal:** RF-06. **Prioritas:** P1.

**Berkas terkait:** ProfileController::update; AlumniController::update; aturan status User; tes ProfileTest dan AlumniAuditTest.

**Langkah:**

- Uji matriks status awal pending/active/suspended dengan perubahan biasa dan perubahan wilayah/angkatan.
- Pertahankan suspended ketika perubahan profil tidak merupakan tindakan aktivasi kembali yang sah.
- Terapkan perilaku konsisten pada pembaruan sendiri dan pembaruan lewat pengelola.
- Pertahankan audit/notifikasi yang sesuai hasil nyata; jangan mengirim pesan seolah akun masuk antrean biasa jika status tetap ditangguhkan.

**Syarat selesai:** profil tidak menjadi jalan menghilangkan penangguhan. Fitur aktivasi ulang baru tidak ditambahkan tanpa keputusan cakupan.

### RFIX-10 — Perbarui dependency PHP berdasarkan paparan

**Asal:** RF-02. **Prioritas:** P1. **Domain:** toolchain B, tes domain A+B.

**Berkas terkait:** composer.json, composer.lock, .github/workflows/tests.yml bila pemeriksaan ditambahkan, run-ci.sh, tes auth/profil/materi/laporan.

**Langkah:**

- Jalankan audit Composer resmi dan catat waktu, versi paket, ID advisory serta dependensi yang menarik paket tersebut.
- Kelompokkan paparan HTTP/runtime, fitur yang tidak dikonfigurasi, CLI, dan kode yang tidak menerima input pengguna.
- Pertahankan target PHP 8.3/Laravel 12 dan lakukan pembaruan terarah dengan dependensi yang memang perlu ikut berubah.
- Jalankan validasi Composer, pemeriksaan platform, tes domain terdampak, lalu suite gabungan pada lockfile akhir.
- Catat advisory yang selesai diperbaiki, yang masih berlaku, dan penilaian tidak berlaku beserta alasannya.

**Syarat selesai:** lockfile baru konsisten, PHP 8.3 terpenuhi, risiko yang berlaku ditutup atau memiliki keputusan penanganan yang jelas. Advisory TokenGuard tidak disebut sebagai bypass login web tanpa konfigurasi guard yang relevan.

### RFIX-11 — Perbarui dependency frontend/tooling secara terarah

**Asal:** RF-03. **Prioritas:** P1.

**Berkas terkait:** package.json, package-lock.json, vite.config.js, postcss.config.js, tailwind.config.js dan modul JavaScript aktif.

**Langkah:**

- Perbarui hasil audit bertanggal dan petakan paket langsung/transitif serta pemakaiannya pada browser, build atau development server.
- Tangani rantai shell-quote/concurrently sebagai satu rantai dampak; periksa pula Axios, Vite, Rollup, PostCSS dan paket relevan lain.
- Hapus dependency yang terbukti tidak dipakai setelah memeriksa import, konfigurasi dan build.
- Pilih pembaruan versi yang menyelesaikan risiko tanpa memigrasikan Tailwind/framework secara tidak perlu.
- Jalankan instalasi dari lockfile, build dan interaksi yang bergantung pada JavaScript.

**Syarat selesai:** build dan interaksi tetap berfungsi; hasil audit akhir dan alasan triase tercatat. Jumlah advisory lama tidak diperlakukan sebagai ukuran keberhasilan tunggal.

### RFIX-12 — Bersihkan warning dan perjelas gate CI

**Asal:** RF-07. **Prioritas:** P1.

**Berkas terkait:** .github/workflows/tests.yml, run-ci.sh, phpunit.xml, tests/TestCase.php, tests/Support/TestDatabaseSafety.php, tests/Tooling/run-ci-test.sh, bootstrap/config jika terbukti menjadi penyebab.

**Langkah:**

- Tampilkan rincian warning PHPUnit yang terpotong dan pastikan penyebabnya sebelum memperbaiki.
- Pertahankan database test terpisah serta pemeriksaan sebelum RefreshDatabase bekerja.
- Perbaiki sumber warning; jangan sekadar mematikan pelaporannya agar pipeline hijau.
- Tetapkan kategori warning/deprecation dan advisory yang menggagalkan CI, berikut alasan pengecualian yang sah.
- Bedakan tes PHP, build aset dan pengujian interaksi browser dalam laporan hasil.
- Catat run CI pada commit patch akhir, bukan run baseline atau branch lain.

**Syarat selesai:** hasil CI dapat dibaca tanpa menyembunyikan peringatan; assertion, warning, skip dan batas pengujian dilaporkan akurat.

### RFIX-13 — Tutup penilaian kunci historis

**Asal:** RF-08. **Prioritas:** P1 kondisional untuk rilis.

**Berkas terkait:** .gitignore, template environment, dokumentasi AUDIT/TASK/runbook; konfigurasi server hanya pada lingkungan yang memang dituju.

**Langkah:**

- Periksa apakah kunci historis pernah digunakan pada lingkungan yang masih relevan tanpa menampilkan nilainya.
- Bedakan kunci develop yang datanya boleh dibuang dari kunci server yang mempunyai data terenkripsi.
- Untuk develop yang benar-benar dibangun ulang, gunakan kunci develop yang sesuai tanpa kebutuhan menyelamatkan dataset lama.
- Jika ada pemakaian server nyata, catat dampak sesi/data terenkripsi dan lakukan rotasi/pemulihan sesuai hasil inventarisasi.
- Pastikan rahasia tidak masuk source, log, contoh output atau dokumen.

**Syarat selesai:** pemakaian kunci diketahui atau ketidaktersediaan buktinya dijelaskan spesifik; tindakan sesuai lingkungan. Ketidakpastian kunci server tidak menghalangi perancangan schema develop.

### RFIX-14 — Kunci sumber grafik dan tangani kegagalan pemuatan

**Asal:** RF-09. **Prioritas:** P2. **Dependensi:** KEP-04.

**Berkas terkait:** resources/views/dashboard/index.blade.php, resources/js/app.js atau modul grafik existing, package.json/package-lock.json bila perlu, README.

**Langkah:**

- Pilih library yang dipertahankan berdasarkan cakupan perubahan dan fungsi tiga grafik aktif.
- Hilangkan ketergantungan URL JavaScript yang berubah tanpa versi; gunakan aset yang versinya terkunci.
- Tambahkan pemeriksaan HTTP gagal dan pesan yang dapat dipahami ketika grafik/data gagal dimuat.
- Sediakan pembacaan angka/tabel yang sesuai agar pengguna tidak bergantung pada warna/grafik saja.
- Uji responsif, data kosong, sesi tidak berizin dan kegagalan jaringan.

**Syarat selesai:** grafik berjalan dari dependency yang jelas, kegagalan tidak meninggalkan ruang kosong tanpa penjelasan, dan dokumentasi stack sesuai kode.

### RFIX-15 — Jaga kestabilan URL tulisan terbit

**Asal:** RF-12. **Prioritas:** P2.

**Berkas terkait:** app/Models/Post.php, LandingController, PostController, ArticlePublishingTest, PublicContentTest, ContentManagementTest.

**Langkah:**

- Tetapkan bahwa edit judul tulisan terbit tetap mempertahankan URL yang sudah dibagikan.
- Prioritaskan mempertahankan slug yang sudah tersimpan; riwayat slug/redirect ditambahkan hanya jika kebutuhan perubahan URL ditetapkan.
- Pertahankan pembeda jenis artikel/refleksi, keunikan slug, pembatasan draf dan aturan terbit.
- Uji edit judul lalu buka URL sebelumnya, collision judul sama, serta akses lintas jenis.

**Syarat selesai:** judul dapat berubah tanpa memutus URL terbit. Dataset develop baru dapat dibuat ulang; perlindungan ini ditujukan pada perilaku publikasi setelah digunakan.

### RFIX-16 — Tetapkan batas ekspor XLSX yang terukur

**Asal:** batas workbook memori. **Prioritas:** P2. **Dependensi:** KEP-05.

**Berkas terkait:** app/Support/AlumniExcel.php, AlumniCsvController, AlumniIndexRequest, resources/views/alumni/csv.blade.php, AlumniExcelTest/AlumniCsvTest.

**Langkah:**

- Ukur waktu dan puncak memori ekspor pada volume fixture yang bertambah.
- Pertahankan filter, scope wilayah, penulisan sel teks dan perlindungan formula.
- Pilih batas XLSX dengan pesan yang jelas serta CSV streaming sebagai pilihan pada volume lebih besar.
- Tambahkan writer/queue baru hanya jika batas sederhana tidak memenuhi kebutuhan yang dibuktikan.

- Uji data kosong, telepon berawalan nol, Unicode dan data yang menyerupai formula.

**Syarat selesai:** ekspor tidak gagal diam-diam pada volume yang didukung; batas berdasarkan pengukuran; hasil tetap mengikuti izin dan filter.

### RFIX-17 — Rapikan form dan interaksi tanpa mengubah cakupan data

**Asal:** perapian UX. **Prioritas:** P2.

**Berkas terkait:** resources/views/profile/edit.blade.php, resources/views/alumni/index.blade.php, komponen/modal dan JavaScript navigasi yang sudah ada; ProfileTest, AuthFormAccessibilityTest, NavigationAccessibilityTest.

**Langkah:**

- Gunakan satu susunan form responsif atau potongan field bersama bila itu cukup menghilangkan penggandaan desktop/mobile.
- Pertahankan field, endpoint, validasi, error bag, nilai input lama, upload dan autocomplete.
- Samakan perilaku fokus modal tambah alumni dengan kebutuhan keyboard: fokus masuk, Escape, pembatasan fokus dan kembali ke pemicu.
- Uji layar 320/390 piksel dan desktop, label/ID, pesan gagal dan tombol utama.
- Jelaskan definisi hitungan “alumni” sesuai kontrak seluruh akun dalam scope; jangan mengubah angka diam-diam.
- Ikuti arahan penyatuan alur Laporan pada [PRD](PRD.md#modul-laporan-kajian); jangan mempertahankan daftar/review/rekap sebagai tiga halaman kerja yang berulang.

**Syarat selesai:** form yang sama dapat digunakan pada ukuran layar terkait; tidak ada field/aksi penting hilang dan tidak terjadi ID ganda.

### RFIX-18 — Satukan kontrak izin dan aturan pengolahan data

**Asal:** perapian request/helper laporan dan ARAH-03. **Prioritas:** P1 saat menyentuh alur terkait.

**Berkas terkait:** routes/web.php, StoreKajianReportRequest, UpdateKajianReportRequest, ReportController::visibleReports, AuthServiceProvider, ScopeWilayah dan model terkait.

**Langkah:**

- Selaraskan request/helper laporan dengan kontrak pengelola aktif saja; alumni tetap ditolak.
- Petakan aturan yang sama di route, controller, request dan view; tentukan satu rujukan aturan yang sederhana menggunakan pola Laravel existing.
- Pertahankan scope wilayah, role ganda, kepemilikan draf, larangan self-review dan row lock.
- Kurangi percabangan mati yang hanya mendukung alur lama yang sudah dikeluarkan dari cakupan.
- Uji endpoint langsung, bukan hanya keberadaan menu.

**Syarat selesai:** lapisan aplikasi menyatakan aturan yang sama dan tidak membuka akses akibat refactor. Tidak membangun permission engine atau workflow generik baru.

### RFIX-19 — Buktikan kebutuhan operasional sebelum rilis nyata

**Asal:** tindak lanjut operasional. **Prioritas:** P1 untuk penerapan.

**Berkas terkait:** template environment, config/mail.php, config/filesystems.php, runbook, catatan QA, pengujian auth/impor/upload.

**Langkah:**

- Verifikasi reset password, verifikasi email dan pengiriman ulang pada transport eksternal yang benar.
- Uji pengguna hasil impor dapat menyetel password dan masuk.
- Uji HTTPS, cookie, document root public, URL aset, disk privat/publik dan konfigurasi proxy pada lingkungan tujuan.
- Pastikan konfigurasi driver sesi/cache/antrean cocok dengan schema yang dipertahankan dan layanan yang dijalankan.
- Lakukan smoke test setiap role dan wilayah dengan data sintetis.

**Syarat selesai:** bukti lingkungan tujuan tersedia. Staging/SMTP yang belum tersedia dicatat sebagai kebutuhan lingkungan tertentu, tanpa menghalangi reset dan perapian develop.

### RFIX-20 — Selaraskan dokumentasi, identitas dan arsip teknis

**Asal:** perapian dokumentasi/arsip. **Prioritas:** P2/P3.

**Berkas terkait:** README.md, AGENTS.md, docs/aturan_pengembangan/PRD.md, PLAN.md, TASK.md, AUDIT.md, SOP.md, CATATAN_JALUR_A/B, composer.json, layout/aset lama dan cache PowerShell yang ter-track.

**Langkah:**

- Tautkan dokumen induk ini sebagai rujukan arahan baru ketika dimasukkan ke repo saat implementasi.
- Pisahkan ringkasan status terkini dan arsip; pertahankan bukti audit lama sebagai riwayat.
- Selaraskan keterangan reset develop, schema acuan dan seeder agar tidak tersisa perintah pelestarian data lama yang sudah diganti pengguna.
- Hapus berkas/aset/template yang terbukti tidak dirujuk; jangan menandai kerusakan template tidak aktif sebagai kegagalan halaman aktif.
- Selaraskan metadata proyek dan keputusan lisensi KEP-08.
- Catat commit rilis yang dipilih; jangan menganggap main identik dengan develop.

**Syarat selesai:** pelaksana baru dapat mengenali prioritas, kontrak fungsi, cara membentuk database dan status implementasi tanpa membaca riwayat yang saling menimpa.

### RFIX-21 — Perjelas nama menu pengelolaan tulisan

**Asal:** ARAH-04/11. Spesifikasi dan penerimaan nama menu Artikel, jenis tulisan publik, route, serta izin penerbit ada pada RFIX-24. ID ini dipertahankan untuk melacak rencana awal tanpa membuat pekerjaan atau kriteria ganda.

### RFIX-22 — Buktikan rebuild, integrasi dan pemulihan sesuai tahap

**Asal:** ARAH-06/07 dan verifikasi seluruh perubahan data. **Prioritas:** P1.

**Berkas terkait:** migration acuan, seeder/factory, tests/TestCase.php, TestDatabaseSafety, run-ci.sh, script/runbook pemulihan ketika relevan, tes domain A+B.

**Langkah:**

- Verifikasi bahwa target reset adalah database develop proyek ini; database pengujian memakai nama terpisah berakhiran _testing.
- Bangun schema dari database kosong dan jalankan seeder RFIX-23; periksa seluruh constraint, index dan hubungan.
- Jalankan pengujian domain pada schema baru, kemudian suite gabungan dan build pada PHP 8.3/MySQL atau MariaDB.
- Jalankan ulang pembentukan dari kosong untuk memastikan tidak ada ketergantungan pada tabel/baris develop lama.
- Jalankan ulang seeder tanpa reset untuk membuktikan tidak menambah duplikasi master/akun fixture.
- Sebelum pemakaian nyata, perluas drill backup/restore mencakup tulisan dan Galeri publik, laporan serta file privat/publik aktif pada schema akhir. Jangan backup/restore data Refleksi saya yang dihapus.

**Syarat selesai:** develop dapat dibentuk ulang secara konsisten dari source+seeder; tes tidak menyasar database kerja yang salah; bukti rilis menggunakan commit akhir. Jalur upgrade dan penyelamatan data lama develop tidak diwajibkan.

### RFIX-23 — Sediakan seeder dan fixture yang menjadi sumber data develop

**Asal:** ARAH-06/07. **Prioritas:** P1, bagian inti penataan database.

**Berkas terkait:** DatabaseSeeder, RoleSeeder, LevelSeeder, WilayahSeeder, UserSeeder, PostSeeder, GallerySeeder dan factory existing. Tambahkan seeder domain laporan/materi/Refleksi publik hanya bila diperlukan untuk membangun contoh yang dapat dipakai; hindari satu seeder per baris atau satu kelas untuk setiap variasi status.

**Langkah:**

- Pisahkan data referensi yang diperlukan aplikasi dari contoh akun/konten yang hanya untuk local/testing.
- Bentuk empat role dan empat level dengan makna yang benar; selaraskan ID/urutan level dengan logika akses yang dipilih.
- Sediakan cakupan minimal dua wilayah, semua role, active/pending/suspended, akun tanpa wilayah dan kombinasi role untuk pengujian.
- Sediakan Artikel/Refleksi publik berstatus draf/terbit, Galeri, playlist/video dan laporan dengan status yang diperlukan.
- Gunakan identitas sintetis dan alamat domain .test pada akun contoh; semua hubungan pemilik/penanggap laporan dan wilayah harus sah.
- Buat total peserta dan angka rekap yang dapat dihitung ulang dari fixture.
- Gunakan pola seed yang tidak menghasilkan duplikasi ketika diulang; data contoh tetap dibatasi local/testing.
- Buktikan instalasi develop dapat digunakan tanpa input database manual setelah migrate dan seed.

**Syarat selesai:** data dasar dan skenario develop dapat diulang dari source, cukup untuk mencoba alur inti, tidak bercampur dengan klaim/konten resmi, dan tidak memerlukan tabel fitur tertunda agar test dapat berjalan.

### RFIX-24 — Hapus Refleksi saya dan ubah nama menu (arahan 6 November)

**Asal:** ARAH-10/11. **Prioritas:** P1 karena menghapus tabel beserta seluruh datanya.

**Cakupan berkas:** `routes/web.php`; `app/Http/Controllers/MemberReflectionController.php`; `app/Models/MemberReflection.php`; `resources/views/reflections/`; partial navigasi serta `resources/views/dashboard/`; `MemberReflectionTest.php` dan test navigasi/dashboard terkait; migration pembentuk dan cleanup `member_reflections`; seeder/factory, backup/recovery, README/PRD/AGENTS/PLAN/TASK/AUDIT/SOP/Catatan Jalur B.

**Langkah:**

- Hapus seluruh route pribadi `/refleksi-saya` dan `/tinjau-refleksi`, controller, model, views, tautan dan tes/fixture khususnya.
- Hilangkan migration pembentuk tabel Refleksi saya pada rebuild dari kosong. Tambahkan migration cleanup untuk instalasi yang sudah memiliki tabel; `up` menghapus tabel/data dan `down` tidak membuat ulang fitur atau isinya.
- Ganti menu pengelolaan **Artikel & Refleksi** menjadi **Artikel**.
- Pertahankan `Post.type` (`article`/`reflection`), route daftar/detail publik `/artikel` dan `/refleksi`, gate super admin serta draf/terbit. Uji route Refleksi publik tetap bekerja.
- Buktikan endpoint pribadi lama tidak tersedia bagi tamu maupun pengguna terautentikasi; tidak ada tautan navigasi/dashboard atau seeder yang masih mengarah ke sana.
- Verifikasi host, database, environment dan config cache sebelum menerapkan cleanup. Penerapan pada target lokal/develop yang teridentifikasi saja; tidak menjalankan migration/reset pada production.
- Selaraskan README, PRD, PLAN dan rujukan aktif. CATATAN_JALUR_B 18 Oktober tetap ditandai arsip/riwayat, bukan fitur aktif.

**Dampak data:** tabel `member_reflections` yang ada, termasuk draf dan kiriman, terhapus permanen saat migration cleanup dijalankan. Tidak ada konversi ke Post. Bukti target dan hasil migration dicatat di TASK.

**Syarat selesai:** kode, migration kosong, cleanup instalasi lama, tes akses/404, menu, seed, docs dan route publik konsisten; data dihapus hanya pada target yang dinyatakan dan diverifikasi.

## 12. Matriks pengujian penerimaan

Skenario dan hasil yang diharapkan ada pada **Syarat selesai** masing-masing RFIX di bagian 9. Cara menjalankan uji, pemisahan database, dan pemaknaan hasil mengikuti [SOP](SOP.md#4-verifikasi). Perintah, runtime, warning/skip, serta hasil aktual dicatat satu kali di [TASK](TASK.md#register-rfix).

## 14. Catatan perubahan per pekerjaan

Register status, commit, perubahan data, bukti pengujian, dan batas pekerjaan hanya ada di [TASK](TASK.md#register-rfix). Risiko serta tingkat buktinya ada di [AUDIT](AUDIT.md).

## 15. Syarat penutupan tahap perbaikan

Setiap RFIX ditutup setelah kriteria penerimaannya dibuktikan menurut [SOP verifikasi](SOP.md#4-verifikasi). Status `terverifikasi` dan `diterapkan` dibedakan menurut bukti di [TASK](TASK.md).

Bukti staging, SMTP eksternal, backup/restore dan konfigurasi lingkungan nyata mengikuti [SOP](SOP.md) serta [runbook](RUNBOOK_STAGING_JALUR_B.md). Audit awal dan [CI baseline](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915) adalah riwayat bertanggal, bukan hasil patch terbaru. Pelaksanaan tidak mengasumsikan persetujuan deployment dari keputusan implementasi lokal.
