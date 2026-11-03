# Analisis Repositori Rausyan Fikr

> Snapshot audit 3 November 2026. Prioritas dan izin develop yang berlaku sekarang mengikuti [dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md) dan [TASK](TASK.md). Bukti source tetap terikat pada commit yang disebut; dokumen ini tidak menyatakan perbaikan telah diterapkan.

Tanggal analisis: 3 November 2026.
Repositori: https://github.com/afikafiranti/rausyanfikrinstitute
Branch yang dianalisis: develop.
Commit: bd9cdc1bc728916060e50d4566eccc64a172df9b, 18 Oktober 2026.

**Aplikasi sudah memiliki fitur inti, pembatasan akses, transaksi, dan pengujian yang cukup luas untuk dilanjutkan ke tahap stabilisasi. Saya belum menilainya siap dipakai dengan data nyata. Penghalang utamanya adalah injeksi JavaScript pada konfirmasi hapus alumni, dependensi dengan advisory keamanan, risiko hilangnya laporan saat akun dihapus, serta perlindungan siklus akun pengelola.**

Analisis ini tidak mengubah kode, branch, konfigurasi, atau data aplikasi. Salinan repositori tetap bersih setelah pemeriksaan dan build.

**1. Cakupan dan kekuatan bukti**

Saya memeriksa dokumentasi produk dan keputusan terbaru, route, middleware, gate, controller utama, model, migration, seeder, konfigurasi autentikasi/storage, Blade/JavaScript yang digunakan halaman aktif, pengujian terkait, workflow CI, serta log CI pada commit yang sama. Snapshot mencakup 55 berkas PHP aplikasi, 26 controller, 24 migration, dan 42 berkas PHP di tests; angka ini merupakan inventaris, bukan klaim bahwa setiap baris memiliki cakupan pengujian.

| Pemeriksaan | Hasil dan batas |
|---|---|
| Identitas kode | Default branch develop, commit bd9cdc1. Main tertinggal 11 commit; hasil perbandingan GitHub mencatat 85 berkas berubah. |
| Build frontend lokal | Berhasil, 162 modul, Vite 7.2.4. |
| Pengaman runner CI lokal | Berhasil: menolak konfigurasi database tidak aman, mempertahankan berkas environment, dan berhenti jika pemeriksaan awal gagal. |
| GitHub Actions | Run 39086196915, commit bd9cdc1, completed/success. |
| Ringkasan PHPUnit pada CI tersebut | 280 warnings, 7 passed, 2.172 assertions. Total 287 kasus; log tidak menunjukkan kegagalan assertion. |
| Audit npm pada lockfile | 19 entri paket terdampak: 4 low, 4 moderate, 9 high, 2 critical. Angka dapat berubah ketika basis advisory berubah. |
| Pemeriksaan dependency PHP | Pencocokan versi paket runtime dalam composer.lock dengan API advisory Packagist menghasilkan 44 entri cocok pada 12 paket; setelah satu advisory ganda digabung berdasarkan GHSA menjadi 43 advisory unik. Ini bukan eksekusi perintah composer audit. |
| Konfirmasi injeksi JavaScript | Terverifikasi melalui evaluasi handler asli yang diisolasi, setelah escaping HTML dan decoding atribut HTML. |
| PHP/MySQL lokal dan browser aplikasi | Belum dijalankan dalam analisis ini. PHP, Composer, dan server database tidak tersedia; binary Chromium lokal juga tidak tersedia. Bukti PHPUnit berasal dari log CI, bukan pengujian ulang lokal. |
| Staging, SMTP eksternal, server produksi | Tidak diperiksa. Dokumentasi masih mencatat tindak lanjut untuk lingkungan tersebut. |

Sumber CI: [run terbaru](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915) dan [job pengujian](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915/job/117553221495).

**2. Produk yang benar-benar tersedia**

Rausyan Fikr menggabungkan website publik dengan aplikasi internal komunitas alumni kajian. Area publik menampilkan tulisan dan dokumentasi kegiatan. Area anggota mengelola identitas, materi, refleksi pribadi, dan laporan wilayah.

| Modul | Implementasi saat ini | Batas yang perlu dipahami |
|---|---|---|
| Akun | Registrasi pending, login/logout, reset password, verifikasi email | Pengiriman email organisasi belum terbukti pada lingkungan nyata. |
| Profil | Data pribadi, foto, password, wilayah, angkatan | Perubahan wilayah/angkatan mengubah status menjadi pending untuk semua role. |
| Alumni | Daftar, detail, filter, sorting, pagination, tambah/edit/hapus, penetapan level | Daftar memuat seluruh akun dalam scope, termasuk pengelola. Ini keputusan produk yang sudah didokumentasikan. |
| Verifikasi anggota | Approve/reject, level awal, notifikasi database dan audit | Penolakan akun tetap berstatus pending, dengan alasan tercatat. |
| Impor/ekspor | CSV dan XLSX, template, laporan baris gagal | Maksimal impor 200 baris dan 1 MB; ekspor XLSX tetap menyimpan workbook dalam memori. |
| Materi | Playlist/video, pencarian, akses akun aktif sesuai level | Judul/durasi manual; tautan dibuka di YouTube. |
| Dashboard pengelola | Angka dari database, grafik, sebaran angkatan/wilayah | Widget jumlah kajian per bulan masih menyatakan belum tersedia. |
| Dashboard alumni | Status akun, profil, level/wilayah, akses materi dan notifikasi pribadi | Terpisah dari statistik pengelola. |
| Refleksi saya | Draf pribadi semua role, kirim opsional, tanggapan pengelola | Setelah dikirim isi dikunci; bukan konten publik. |
| Laporan kajian | Draf, edit, kirim, review, approve/reject, rekap/CSV, foto privat | Hanya pengelola aktif; laporan rejected belum bisa direvisi/dikirim ulang. |
| Artikel/refleksi publik | Jenis Post terpisah, daftar/detail, draf/terbit | Pengelolaan hanya super admin aktif. |
| Galeri | CRUD dan daftar publik | Galeri langsung publik setelah disimpan; tidak memiliki status draf. |
| Master role/wilayah/level | Struktur database dan seeder tersedia | CRUD web khusus belum tersedia. |
| Progres video/metadata otomatis | Ada sebagian fondasi schema/model | Fitur pengguna belum tersedia dan sudah dinyatakan ditunda. |

Homepage aktif memang dibatasi menjadi Hero → maksimal empat Artikel → maksimal empat Galeri → Footer. Agenda, tim/kontak, statistik/tentang, dan CTA terpisah dikeluarkan dari cakupan berdasarkan keputusan proyek. Ketidakhadirannya tidak saya hitung sebagai cacat.

**3. Arsitektur dan pemisahan hak akses**

Stack yang digunakan sesuai skala aplikasi komunitas: Laravel 12, PHP minimal 8.3, Blade, Tailwind 3, Vite, Alpine/Flowbite, serta MySQL/MariaDB. Rendering utama dilakukan di server. Stack ini memadai untuk form, tabel, laporan dan pengelolaan konten yang menjadi inti produk.

~~~mermaid
flowchart TD
    A["Permintaan pengguna"] --> B["Route, auth, dan gate"]
    B --> C["Controller dan validasi"]
    C --> D["Eloquent"]
    D --> E["MySQL atau MariaDB"]
    C --> F["Blade dan aset Vite"]
    C --> G["Unggahan publik"]
    C --> H["Foto laporan privat"]
    F --> I["Grafik dari CDN"]
~~~

Pemisahan role dan level sudah tepat: role mengatur kewenangan sistem, sedangkan level mengatur materi yang boleh dibuka. Pengguna dapat memiliki beberapa role; pemeriksaan admin/super admin memberi cakupan lebih luas daripada koorda.

| Kewenangan | Super admin aktif | Admin aktif | Koorda aktif | Alumni |
|---|---|---|---|---|
| Melihat data alumni | Lintas wilayah | Lintas wilayah | Wilayah sendiri | Ditolak |
| Edit/hapus alumni atau koorda | Diizinkan dengan batas target | Diizinkan dengan batas target | Ditolak | Ditolak |
| Memverifikasi akun lain | Lintas wilayah | Lintas wilayah | Akun non-pengelola di wilayahnya | Ditolak |
| Mengelola materi | Diizinkan | Diizinkan | Ditolak | Ditolak |
| Mengelola Artikel/Refleksi publik/Galeri | Diizinkan | Ditolak | Ditolak | Ditolak |
| Membuat/mereview laporan | Diizinkan; tidak boleh mereview sendiri | Sama | Sesuai wilayah; tidak boleh mereview sendiri | Ditolak oleh route |
| Membaca draf Refleksi saya milik orang lain | Ditolak | Ditolak | Ditolak | Ditolak |
| Menanggapi refleksi terkirim | Kiriman orang lain lintas wilayah | Sama | Sesuai wilayah kiriman | Ditolak |
| Membuka materi | Akun aktif dan level sesuai; tanpa level diarahkan ke pengelolaan | Sama | Akun aktif dan level sesuai | Akun aktif dan level sesuai |

Admin biasa memang dapat memverifikasi akun super admin yang pending sesuai kontrak produk untuk pemulihan. Saya tidak menganggap perilaku yang sudah dinyatakan eksplisit tersebut sebagai temuan eskalasi hak akses.

Sumber utama: [gate](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Providers/AuthServiceProvider.php#L15), [route](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/routes/web.php), dan [refleksi pribadi](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/MemberReflectionController.php).

**4. Bagian implementasi yang sudah kuat**

Pertama, izin penting diperiksa di server. Menu yang disembunyikan bukan satu-satunya pembatas. Gate menolak pengelola nonaktif; scope wilayah membatasi akses koorda; pengelolaan Post/Galeri memiliki pemeriksaan super admin tambahan.

Kedua, draf refleksi mempunyai batas kepemilikan yang jelas. Query membaca/mengubah draf selalu membatasi user_id. Pengiriman mengunci record dan memeriksa ulang status akun/wilayah. Reviewer memakai wilayah yang disimpan pada kiriman, sehingga perpindahan wilayah penulis setelah pengiriman tidak otomatis memindahkan kewenangan atas kiriman lama.

Ketiga, alur laporan menggunakan transaksi dan row lock untuk pengiriman serta keputusan review. Sistem menolak self-review dan transisi ulang dari status yang sudah diputuskan. Foto laporan disimpan di disk privat dan disajikan melalui endpoint berizin dengan header private/no-store dan nosniff.

Keempat, impor Excel mendapat perlindungan yang cukup rinci: ukuran dan jumlah baris/kolom dibatasi, jumlah sheet diperiksa, ukuran ZIP setelah diekspansi dibatasi, formula ditolak, serta nomor telepon numerik ditolak agar nol di depan tidak hilang. Ekspor XLSX menulis sel sebagai teks. CSV juga memiliki perlindungan terhadap formula spreadsheet.

Kelima, pergantian gambar Post/Galeri menunggu transaksi database berhasil sebelum membersihkan gambar lama. Ada pengujian untuk kegagalan insert/update/delete. Seeder akun dibatasi ke local/testing dan menggunakan firstOrCreate, sehingga tidak rutin menimpa password akun existing.

Keenam, pengaman database test bekerja sebelum RefreshDatabase dapat bermigrasi. Runner memisahkan environment, memeriksa akhiran database _testing, dan menghindari config cache aplikasi. Ini mengurangi risiko salah sasaran saat menjalankan tes.

**5. Temuan RF-01 — Injeksi JavaScript melalui nama alumni**

Prioritas: **P0, tutup sebelum aplikasi dibuka untuk penggunaan nyata.**
Kekuatan bukti: kode sumber dan reproduksi JavaScript terisolasi.

Nama alumni disisipkan ke string JavaScript pada atribut onsubmit untuk konfirmasi hapus. Escaping Blade mengamankan konteks HTML, tetapi browser mendekode entitas HTML sebelum menjalankan isi event handler sebagai JavaScript. Apostrof pada nama kemudian menjadi bagian dari sintaks JavaScript.

Masukan nama berasal dari registrasi/profil dengan validasi string dan panjang; karakter tersebut dapat masuk secara sah. Daftar alumni juga memuat akun pending. Dengan demikian, jalur menuju nilai yang ditampilkan kepada admin tersedia dari input pengguna.

| Skenario uji terisolasi | Hasil |
|---|---|
| Nama biasa | Konfirmasi dipanggil satu kali; penanda tidak berubah. |
| Nama mengandung apostrof | Handler menghasilkan SyntaxError; konfirmasi tidak dipanggil. |
| Nama berisi ekspresi penanda sintetis | Penanda berubah saat handler dijalankan; konfirmasi tetap dipanggil. |

Dampaknya adalah potensi stored XSS yang dipicu ketika admin menggunakan aksi hapus. JavaScript tersebut berjalan dalam konteks halaman admin. Uji ini tidak mengambil data, mengirim permintaan, atau mengakses akun nyata; saya belum menjalankan alur HTTP Laravel dan browser aplikasi secara utuh.

Perbaikan minimum: gunakan handler konfirmasi statis atau pindahkan nama ke atribut data, kemudian baca sebagai teks. Jika teks memang harus dimasukkan ke JavaScript, serialisasikan secara benar untuk konteks JavaScript dan atribut HTML. Tambahkan pengujian interaksi dengan nama berapostrof dan ekspresi sintetis; pengujian assertSee atas HTML saja tidak memadai.

Bukti: [Blade alumni baris 221](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/resources/views/alumni/index.blade.php#L221), [registrasi baris 27](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/Auth/RegisteredUserController.php#L27), [validasi profil baris 19](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Requests/ProfileUpdateRequest.php#L19).

**6. Temuan RF-02 — Dependency runtime PHP memerlukan pembaruan keamanan**

Prioritas: **P1, triase dan tutup risiko yang berlaku sebelum rilis.**
Kekuatan bukti: versi dalam lockfile dicocokkan dengan API advisory Packagist.

Composer.lock masih mengunci Laravel 12.28.1 serta beberapa komponen Symfony 7.3.x. Pemeriksaan paket runtime menghasilkan 43 advisory unik pada 12 paket. Angka ini tidak memasukkan seluruh paket require-dev dan tidak berarti seluruh advisory dapat dieksploitasi melalui aplikasi ini.

Contoh penting adalah Symfony HTTP Foundation 7.3.3 yang masuk rentang terdampak advisory parsing PATH_INFO dan ReDoS pada pengolahan header Cache-Control. Konfigurasi server dan jalur pemanggilan tetap menentukan paparan aktual. Laravel juga memiliki advisory yang harus dibedakan menurut fitur yang digunakan.

Advisory kritis TokenGuard tidak boleh langsung diterjemahkan menjadi klaim bahwa login website dapat diambil alih. Konfigurasi repositori menggunakan guard web/session; TokenGuard API tidak dikonfigurasi pada snapshot ini. Sebaliknya, pemeriksaan header HTTP dan validasi email merupakan area yang lebih dekat dengan penggunaan aplikasi dan perlu mendapat perhatian saat pembaruan.

Perbaikan: perbarui dependency secara terarah dalam batas PHP 8.3/Laravel 12 yang disepakati, perbarui lockfile, jalankan audit Composer resmi, lalu ulangi tes auth, verifikasi email, upload, materi, laporan dan refleksi. Catat alasan bila suatu advisory dinilai tidak berlaku.

Sumber: [composer.lock](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/composer.lock), [API Packagist](https://packagist.org/apidoc#list-security-advisories), [advisory Symfony PATH_INFO](https://symfony.com/blog/cve-2025-64500-incorrect-parsing-of-path-info-can-lead-to-limited-authorization-bypass), [advisory Symfony header](https://symfony.com/cve-2026-108082), [advisory TokenGuard](https://github.com/laravel/framework/security/advisories/GHSA-f9x3-7vq9-cph6).

**7. Temuan RF-03 — Dependency frontend dan alat pengembangan juga terdampak**

Prioritas: **P1 untuk triase rilis; tingkat paparan dipisahkan antara browser, build, dan development server.**

Audit npm terbaru atas lockfile menghasilkan 19 entri paket: 4 low, 4 moderate, 9 high, 2 critical. Kedua entri critical berada pada shell-quote dan concurrently yang mewarisi masalah dari dependency tersebut. Ini bukan dua bukti serangan terpisah terhadap website.

Paket lain yang ditandai antara lain Axios 1.13.2, Vite 7.2.4, Rollup 4.53.3 dan PostCSS 8.5.6. Sebagian advisory menyasar adapter Node atau server pengembangan. Aplikasi menggunakan Laravel sebagai backend dan membundel Axios untuk browser, sehingga keterjangkauan masing-masing advisory perlu diperiksa sesuai konteks tersebut.

Log CI 18 Oktober mencatat 20 kerentanan, sedangkan pemeriksaan saat analisis ini menghasilkan 19 entri. Perubahan basis advisory dapat mengubah angka tanpa perubahan lockfile. Gunakan hasil bertanggal dan versi paket sebagai acuan.

Perbaikan: kelompokkan dependency berdasarkan pemakaian nyata, perbarui rantai dependency yang terdampak, jalankan build serta uji interaksi, dan tambahkan pemeriksaan keamanan yang menghasilkan keputusan CI yang eksplisit. Keberhasilan npm ci/build sendiri belum menutup advisory.

Bukti: [package-lock.json](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/package-lock.json), [job CI](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915/job/117553221495), [advisory shell-quote](https://github.com/ljharb/shell-quote/security/advisories/GHSA-w7jw-789q-3m8p).

**8. Temuan RF-04 — Menghapus akun dapat menghapus laporan yang telah disetujui**

Prioritas: **P1, integritas data organisasi.**
Kekuatan bukti: migration dan alur penghapusan.

Kajian_reports.user_id memakai foreign key dengan cascadeOnDelete. AlumniController dan ProfileController menghapus record user secara permanen. Jika akun yang dihapus mempunyai laporan approved, laporan tersebut juga terhapus oleh database. Rekap periode yang sama kemudian dapat berubah.

Pengaman target admin/super admin pada daftar alumni tidak menyelesaikan masalah ini: koorda tetap boleh dihapus, dan endpoint penghapusan profil sendiri tersedia bagi pengguna yang mengetahui passwordnya. Tes penghapusan yang ditinjau memeriksa hilangnya user/pivot dan audit, tetapi belum mengunci perilaku pelestarian laporan approved.

Foto avatar dan foto laporan juga tidak mempunyai alur pembersihan pada penghapusan user yang ditemukan dalam snapshot ini. Relasi database dapat hilang sementara berkas unggahan tetap tersimpan.

Perbaikan: tetapkan kebijakan terpisah untuk arsip laporan organisasi dan penghapusan data pribadi. Untuk laporan, pilih pembatasan penghapusan penulis atau mekanisme arsip/anonymisasi yang mempertahankan histori. Tentukan pula retensi dan pembersihan unggahan. Lakukan perubahan melalui migration baru dan tes yang memuat laporan approved.

Bukti: [foreign key laporan baris 13](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/database/migrations/2026_07_22_000003_create_kajian_reports_table.php#L13), [hapus alumni baris 284](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/AlumniController.php#L284), [hapus profil baris 128](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/ProfileController.php#L128).

**9. Temuan RF-05 — Akun pengelola terakhir dapat kehilangan akses operasional**

Prioritas: **P1, bersyarat pada susunan akun pengelola.**
Kekuatan bukti: penelusuran transisi status dan gate; belum direproduksi pada database lokal.

Perubahan wilayah atau angkatan melalui profil selalu menetapkan status pending, termasuk bagi super admin. Gate pengelola memerlukan active, pengguna tidak boleh memverifikasi dirinya sendiri, dan koorda tidak boleh memverifikasi akun pengelola.

Jika hanya ada satu admin/super admin aktif, perubahan tersebut dapat menutup seluruh jalur pemulihan melalui aplikasi. Endpoint hapus profil sendiri juga belum memeriksa apakah pengguna merupakan pengelola aktif terakhir. Larangan menghapus admin dari daftar alumni tidak berlaku pada endpoint profil ini.

Perbaikan: buat aturan yang menjaga keberadaan minimal satu pengelola aktif. Perubahan profil pengelola dapat memakai penanganan terpisah, misalnya menunda perubahan kritis sampai pengelola lain menyetujui. Penghapusan pengelola terakhir harus ditolak melalui semua endpoint terkait, dengan pengujian transaksi yang sesuai.

Bukti: [perubahan status profil baris 61](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/ProfileController.php#L61), [penolakan self-approve baris 46](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/VerificationController.php#L46), [gate aktif](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Providers/AuthServiceProvider.php#L15).

**10. Temuan RF-06 — Perubahan profil menghilangkan penanda suspended**

Prioritas: **P1 untuk ketegasan siklus status akun.**
Kekuatan bukti: alur pembaruan profil.

Kode yang mengubah wilayah/angkatan menjadi pending tidak membedakan status sebelumnya. Akun suspended tetap dapat masuk ke alur profil yang dilindungi auth dan dapat berubah menjadi pending setelah perubahan tersebut.

Ini belum otomatis mengaktifkan akun, sehingga tidak tepat disebut bypass login penuh. Dampak yang dapat disimpulkan dari kode adalah hilangnya penanda penangguhan dan masuknya akun kembali ke antrean persetujuan biasa. Peninjau wilayah dapat kehilangan konteks bahwa akun sebelumnya ditangguhkan.

Perbaikan: tetapkan transisi eksplisit untuk pending, active dan suspended. Perubahan data profil pengguna suspended sebaiknya mempertahankan status penangguhan; aktivasi kembali memerlukan kewenangan dan alasan yang tercatat.

Bukti: [route profil](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/routes/web.php#L99), [status profil baris 61](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/ProfileController.php#L61).

**11. Temuan RF-07 — CI berhasil, tetapi belum bersih dari peringatan**

Prioritas: **P1 untuk mutu bukti pengujian sebelum rilis.**

Run terbaru selesai dengan status success. Ringkasan PHPUnit yang sebenarnya adalah 280 warnings, 7 passed, 2.172 assertions. Banyak pesan yang ditampilkan menyebut file_get_contents, tetapi output ringkas memotong detailnya. Saya belum menetapkan akar masalah peringatan tersebut.

Dokumentasi yang menyebut 287 test lulus perlu diberi konteks hasil CI ini. Jumlah assertion menunjukkan pemeriksaan tetap dijalankan; peringatan tidak boleh otomatis dianggap kegagalan assertion maupun dianggap tidak berpengaruh.

Workflow menjalankan validasi Composer/platform, build, route:list dan tes. Tidak ada langkah audit keamanan dependency yang eksplisit. Pengujian Blade juga menonaktifkan @vite ketika environment testing, sehingga tes PHP tidak membuktikan JavaScript/menu/modal berfungsi di browser.

Perbaikan: tampilkan rincian peringatan dalam CI, perbaiki penyebabnya, lalu putuskan kategori warning/deprecation yang harus menggagalkan pipeline. Tambahkan uji interaksi untuk RF-01 dan alur penting yang bergantung pada JavaScript.

Bukti: [workflow](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/.github/workflows/tests.yml), [runner](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/run-ci.sh), [layout testing](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/resources/views/layouts/app.blade.php#L9).

**12. Temuan RF-08 — Paparan kunci historis masih memerlukan penutupan bukti**

Prioritas: **P1 bersyarat; menjadi mendesak jika kunci tersebut pernah dipakai pada lingkungan nyata.**

Snapshot sekarang tidak melacak berkas .env nyata maupun berkas kunci/dump yang cocok dengan pemeriksaan nama berkas yang dilakukan. Namun, AUDIT/TASK mencatat APP_KEY pernah masuk pelacakan dan penilaian pemakaiannya belum selesai.

Ini adalah risiko historis yang terdokumentasi, bukan temuan bahwa kunci produksi aktif saat ini bocor. Saya tidak mengambil atau menampilkan nilai kunci lama. Penghapusan dari snapshot terbaru belum menjawab apakah kunci lama dipakai pada server.

Perbaikan: inventarisasi pemakaian kunci dan data yang bergantung padanya. Bila kunci pernah digunakan, siapkan rotasi serta pemulihan data terenkripsi dan invalidasi sesi sesuai kebutuhan. Pastikan tindakan server didasarkan pada hasil inventarisasi tersebut.

Bukti: [risiko historis](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/docs/aturan_pengembangan/AUDIT.md#L75), [task rilis](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/docs/aturan_pengembangan/TASK.md#L60).

**13. Temuan RF-09 — Grafik aktif memakai CDN tanpa versi terkunci**

Prioritas: **P2, keandalan dan konsistensi dependency.**

README menyebut Chart.js, dan package.json memang memasangnya. Dashboard aktif justru mengambil ApexCharts dari cdn.jsdelivr.net/npm/apexcharts tanpa versi tertentu dan tanpa integrity. Dengan demikian, dependency grafik runtime tidak mengikuti lockfile.

CDN yang gagal dapat membuat grafik tidak muncul. Perubahan paket pada URL yang sama juga dapat mengubah perilaku tanpa commit aplikasi. Fetch data grafik pada view belum mempunyai pemeriksaan response.ok dan penanganan kegagalan yang menampilkan pesan pada pengguna.

Perbaikan: pilih satu library grafik. Chart.js yang sudah terpasang dapat dipertimbangkan; bila tetap memakai ApexCharts, kunci dan bundel versinya. Tambahkan state gagal dan alternatif angka/tabel agar grafik bukan satu-satunya cara membaca data.

Bukti: [CDN dashboard baris 218](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/resources/views/dashboard/index.blade.php#L218), [fetch grafik baris 297](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/resources/views/dashboard/index.blade.php#L297).

**14. Temuan RF-10 — Pemeriksaan role menghasilkan query berulang per baris**

Prioritas: **P2, efisiensi daftar alumni.**

Controller sudah eager-load roles. Namun, User.hasRole menjalankan query melalui roles() lalu exists(), sehingga tidak menggunakan relasi yang sudah dimuat. Tombol edit dan hapus memanggil helper yang sama berulang untuk setiap baris.

Untuk baris alumni biasa yang bukan akun sendiri pada sesi admin, dua kondisi tombol dapat menghasilkan empat query exists tambahan. Dengan 15 baris, estimasinya 60 query role tambahan; dengan 100 baris, estimasinya 400. Angka ini diturunkan dari jalur kode, bukan hasil benchmark SQL.

Perbaikan: gunakan data role yang sudah dimuat untuk keputusan tampilan atau hitung izin target satu kali sebelum render. Pertahankan pemeriksaan otorisasi server yang benar dan perhatikan penyegaran relasi setelah perubahan role.

Bukti: [helper role baris 84](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Models/User.php#L84), [eager loading baris 35](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/AlumniController.php#L35), [pemakaian dalam baris tabel](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/resources/views/alumni/index.blade.php#L211).

**15. Temuan RF-11 — Beberapa index database menduplikasi fungsi yang sama**

Prioritas: **P2, pemeliharaan database.**

Tiga migration membuat index dengan susunan wilayah_id, level_id, status pada users memakai nama berbeda. Index angkatan juga muncul lebih dari sekali. Email sudah memiliki unique index tetapi mendapat index tambahan.

Index berulang menambah ukuran penyimpanan dan pekerjaan saat insert/update tanpa memberikan cakupan kolom baru. Dampak waktunya perlu diukur pada data nyata; temuan ini tidak membuktikan aplikasi saat ini lambat.

Perbaikan: inventarisasi index database hasil migration, periksa kebutuhan query, lalu hapus index berlebih melalui migration baru. Pertahankan migration historis yang sudah dipakai.

Bukti: [migration profil baris 29](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/database/migrations/2025_09_15_223349_add_profile_fields_to_users_table.php#L29), [migration index kedua](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/database/migrations/2025_09_16_072801_add_search_indexes_to_users_table.php.php#L14), [migration index ketiga](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/database/migrations/2025_09_16_133110_add_indexes_for_alumni_list.php#L11).

**16. Temuan RF-12 — Mengubah judul dapat memutus URL tulisan yang sudah dibagikan**

Prioritas: **P2, stabilitas tautan publik.**

Model Post membuat slug baru ketika judul berubah, termasuk ketika tulisan sudah terbit. Route detail mencari slug tersebut secara langsung; pemetaan atau redirect slug lama tidak ditemukan. Akibatnya, tautan artikel/refleksi yang sudah dibagikan dapat menghasilkan 404 setelah judul diedit.

Perbaikan: pertahankan slug setelah publikasi atau simpan riwayat slug dengan redirect yang sesuai. Ini terpisah dari perlindungan collision yang sudah ditangani dengan ULID.

Bukti: [pembuatan slug baris 26](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Models/Post.php#L26), [pencarian detail](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/app/Http/Controllers/LandingController.php#L42).

**17. Pengalaman pengguna dan utang pemeliharaan**

Fondasi visual sudah memiliki arah tertulis: navy, cobalt, aksen merah logo, kanvas hangat, huruf sistem, ukuran aksi minimum dan dukungan reduced motion. Halaman aktif memakai skip link, focus-visible, label form, serta pengelolaan fokus drawer. Saya menilai ini dari sumber; analisis ini tidak memberi skor visual atau klaim lulus aksesibilitas seluruh aplikasi.

Hal yang masih perlu dirapikan:

1. Form profil desktop dan mobile menggandakan field dan struktur. Saat aturan form berubah, kedua versi harus dijaga serempak. Satu form responsif akan mengurangi peluang ketidaksamaan.
2. Modal tambah alumni masih dibuka dengan perubahan class sederhana; pola fokusnya belum setara dengan drawer yang memiliki focus trap dan pemulihan fokus.
3. Judul/statistik memakai istilah alumni untuk seluruh akun dalam scope, termasuk pengelola. Karena ini keputusan produk, cukup jelaskan definisinya pada antarmuka agar angka dibaca dengan tepat.
4. Widget statistik kajian masih belum tersedia meskipun modul laporan sudah berjalan. Ini fitur parsial yang ditampilkan jujur, bukan grafik data palsu.
5. Ekspor XLSX memakai lazy query tetapi seluruh sel tetap disimpan dalam workbook. Lazy query tidak membuat kebutuhan memori workbook menjadi tetap. Batas volume ekspor atau pemrosesan bertahap perlu ditentukan berdasarkan pengukuran.
6. Sebagian kontrak internal laporan masih menyebut alumni sebagai pengguna yang diizinkan, sementara route sudah menolaknya. Perlindungan route saat ini tetap berlaku; menyamakan request/helper dengan kontrak terbaru akan mengurangi risiko pada perubahan berikutnya.
7. Beberapa layout/partial lama tidak digunakan halaman aktif. Rujukan route atau aset yang rusak di template lama tidak otomatis membuktikan halaman aktif rusak, tetapi perlu dipisahkan agar tidak dipakai ulang tanpa sengaja.
8. Ada cache lokal PowerShell yang masuk repositori, serta sisa aset/template. Berkas tersebut tidak menjadi bagian arsitektur runtime dan dapat dibersihkan pada pemeliharaan.
9. composer.json masih membawa nama/deskripsi skeleton dan metadata lisensi MIT, sedangkan README menyebut proyek internal tanpa berkas LICENSE yang menjelaskan pilihan tersebut. Selaraskan identitas dan keterangan lisensinya.
10. Dokumen pusat banyak mengulang riwayat status yang saling menimpa. Status terkini, bukti CI terbaru, dan riwayat sebaiknya dibedakan supaya pengembang tidak mengulang task selesai.

**18. Keputusan produk yang perlu ditegaskan sebelum pemakaian rutin**

| Keputusan | Perilaku kode saat ini | Implikasi operasional |
|---|---|---|
| Verifikasi email vs persetujuan anggota | Izin memakai status active/role/level; middleware verified tidak dipasang pada fitur utama | Email terverifikasi belum menjadi syarat umum akses. Dokumentasi dan antarmuka perlu menyebut kebijakan yang dipilih dengan jelas. |
| Laporan rejected | Isi terkunci; tidak ada revisi/kirim ulang | Tetapkan prosedur koreksi yang dipakai pengelola. Perubahan workflow merupakan keputusan fitur tersendiri. |
| Materi YouTube | Aplikasi membatasi penemuan tautan menurut level | Pengguna yang mengetahui URL dapat membagikannya di luar aplikasi. Ini tidak setara dengan proteksi file video. |
| Akun hasil impor | Password acak; pengguna diminta memakai lupa password | SMTP eksternal merupakan kebutuhan nyata onboarding, bukan sekadar fitur tambahan. |
| Galeri | Disimpan langsung tampil publik | Pengelola harus mengetahui bahwa tombol simpan sekaligus membuat foto terbuka. |
| Pengelolaan role/wilayah | Belum tersedia melalui CRUD web | Perlu prosedur operator yang jelas; jangan menganggap menu pengelolaan sudah ada. |
| Definisi alumni dan level | Semua akun masuk hitungan; urutan akses materi membandingkan level_id | Pertahankan makna yang konsisten; jika struktur level diperluas, jangan mengandalkan ID database sebagai peringkat tanpa keputusan domain. |

**19. Urutan kerja yang disarankan**

| Urutan | Pekerjaan | Bukti selesai yang dapat diperiksa |
|---|---|---|
| 1 | Tutup RF-01 | Nama berapostrof tetap menampilkan konfirmasi; ekspresi sintetis tetap diperlakukan sebagai teks. |
| 2 | Triase dan perbarui dependency PHP/frontend | Lockfile baru, audit bertanggal, alasan applicability, build dan tes terkait berhasil. |
| 3 | Lindungi histori laporan saat akun dihapus | Kasus approved tetap tersimpan sesuai kebijakan; retensi file dan identitas pelapor/reviewer jelas. |
| 4 | Lindungi pengelola terakhir dan status suspended | Tes perubahan profil, self-delete, pengelola terakhir dan aktivasi ulang menghasilkan status yang ditetapkan. |
| 5 | Bersihkan warning dan perkuat CI | Penyebab warning diketahui, output lengkap tersimpan, dan aturan kegagalan CI sesuai kategori yang dipilih. |
| 6 | Tutup risiko kunci historis dan buktikan operasional | Inventaris kunci selesai; reset/verifikasi email dan onboarding impor berhasil pada lingkungan yang dituju; backup/restore diuji. |
| 7 | Rapikan CDN grafik, query role, index dan URL publik | Dependency terkunci; query terukur; index tidak berulang; tautan terbit tetap stabil setelah edit judul. |
| 8 | Selaraskan dokumentasi dan release | Commit rilis jelas; main/develop tidak diasumsikan sama; status terbaru dipisahkan dari catatan historis. |

Kebutuhan berikutnya adalah stabilisasi dan pembuktian operasional pada arsitektur yang sudah ada. Fondasi Laravel/Blade masih sesuai. Penambahan halaman atau perubahan framework belum menyelesaikan risiko yang ditemukan.

**20. Catatan sumber dan penafsiran**

Sumber kode ditautkan pada commit tetap agar bukti tidak berubah ketika develop menerima commit baru. Bukti CI diambil dari run commit yang sama. Pemeriksaan dependency dilakukan terhadap basis advisory yang tersedia saat analisis; jumlah entri, tingkat severity dan applicability dapat berbeda antar layanan dan waktu. Angka severity paket tidak saya gunakan sebagai bukti otomatis bahwa endpoint aplikasi dapat diserang.

Temuan status, cascade delete, query role, index dan slug disimpulkan dari jalur kode/schema. Injeksi JavaScript mempunyai tambahan bukti eksekusi handler terisolasi. Pengujian staging, beban, konfigurasi server, pengiriman SMTP eksternal, dan audit aksesibilitas browser penuh masih berada di luar bukti yang dikumpulkan pada analisis ini.


**Lampiran — Paket PHP runtime yang cocok dengan rentang advisory**

Jumlah unik di bawah dihitung berdasarkan GHSA pada setiap paket; satu entri Laravel yang digandakan oleh sumber berbeda digabung. Tabel ini merupakan triase awal berdasarkan kode, bukan hasil uji eksploitasi.

| Paket | Versi terkunci | Advisory unik | Konteks pemakaian |
|---|---|---:|---|
| guzzlehttp/guzzle | 7.10.0 | 9 | Advisory klien HTTP; integrasi pengambilan metadata YouTube belum aktif. Keterjangkauan perlu diperiksa per pemakaian. |
| guzzlehttp/psr7 | 2.8.0 | 4 | Komponen pesan/URI HTTP; perlu ditriase bersama pemakaian klien HTTP. |
| laravel/framework | v12.28.1 | 4 | Guard web memakai session; advisory TokenGuard kritis tidak terlihat aktif. Validasi email, signed URL dan debug dinilai terpisah. |
| league/commonmark | 2.7.1 | 14 | Artikel dirender sebagai teks yang di-escape, bukan Markdown pengguna. Periksa pemakaian Markdown/ekstensi lain sebelum menetapkan paparan. |
| league/flysystem | 3.30.0 | 1 | Path upload dibuat server dan beberapa pembacaan memakai pola path terbatas; advisory tetap perlu pembaruan/penilaian. |
| psy/psysh | v0.12.10 | 1 | Berhubungan dengan CLI/Tinker dan direktori kerja; tidak setara dengan endpoint publik. |
| symfony/http-foundation | v7.3.3 | 3 | Dekat dengan pengolahan request HTTP; prioritas triase tinggi. |
| symfony/mailer | v7.3.3 | 1 | Sebagian advisory memerlukan SendmailTransport; mailer contoh memakai log, transport nyata belum diverifikasi. |
| symfony/mime | v7.3.2 | 2 | Berkaitan dengan alamat/header email; evaluasi bersama alur reset/verifikasi dan transport nyata. |
| symfony/polyfill-intl-idn | v1.33.0 | 1 | Berkaitan dengan normalisasi domain IDN; periksa penggunaannya pada validasi email. |
| symfony/process | v7.3.3 | 1 | Advisory yang cocok menyasar escaping MSYS2/Git Bash pada Windows; konteks pengembang/server perlu dibedakan. |
| symfony/routing | v7.3.2 | 2 | Berkaitan dengan pembuatan URL; periksa parameter dan konfigurasi yang benar-benar dipakai. |
