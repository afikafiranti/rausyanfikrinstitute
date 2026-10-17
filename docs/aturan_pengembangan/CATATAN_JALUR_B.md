# Catatan Jalur B

## B0.1 / P0 — Proteksi dashboard

- Status: REVIEW (2026-10-16); perbaikan dan test akses tamu selesai, B0 keseluruhan belum selesai.
- Branch: dev-B, dibuat dari baseline develop cc7ab2f.
- Tujuan: tamu diarahkan ke login sebelum controller dashboard membaca data.
- File: routes/web.php (blok dashboard), tests/Feature/DashboardAccessTest.php.
- Verifikasi pada PHP 8.3.33:
  - RED: dua test akses tamu gagal sebelum perubahan (HTTP 500 karena controller menjalankan query tanpa auth).
  - GREEN: `php vendor/phpunit/phpunit/phpunit --filter=DashboardAccessTest` lulus, 2 test / 3 assertion.
  - `php -l routes/web.php` dan `php -l tests/Feature/DashboardAccessTest.php` lulus.
  - `php artisan route:list --path=dashboard -v` menunjukkan middleware web dan auth; nama route tetap dashboard.
  - `git diff --check` lulus.
- Suite berbasis database belum dijalankan: MySQL melaporkan database rf_db_testing belum tersedia. Tidak menjalankan migrasi/RefreshDatabase atau menyentuh database kerja.
- Risiko: statistik dan scope dashboard belum diperbaiki, termasuk dalam B1.
- Perubahan pengguna pada package-lock.json dipertahankan tanpa diedit.

## B0.2 / P0 — Composer, CI dan isolasi database test

- Status: REVIEW (2026-10-16); implementasi dan verifikasi lokal selesai, workflow remote belum dijalankan.
- Scope: target PHP 8.3, service MariaDB CI, konfigurasi PHPUnit, pengaman database sebelum setup test, build frontend CI.
- File: composer.json/lock, phpunit.xml, .github/workflows/tests.yml, tests/TestCase.php, tests/Support/TestDatabaseSafety.php, tests/Unit/TestDatabaseSafetyTest.php.
- Perubahan:
  - Composer memakai require PHP ^8.3 dan config.platform.php 8.3.0; metadata lock diselaraskan dengan Composer, tanpa perubahan versi paket.
  - CI memakai PHP 8.3, service MariaDB 11.4, pdo_mysql, database rf_b_testing dan user service khusus rf_test; kredensial CI hanya untuk container sementara.
  - CI memakai npm ci, build frontend, validasi Composer/platform, pemeriksaan route, lalu test. APP_KEY dibuat acak per job tanpa menimpa .env/.env.testing.
  - PHPUnit default ke rf_b_testing; APP_ENV testing serta DB_URL/DB_SOCKET kosong dipaksakan untuk mencegah pengalihan dari environment lokal.
  - Pengaman pada createApplication test memeriksa environment dan konfigurasi koneksi yang sudah di-resolve sebelum trait RefreshDatabase berjalan. Hanya MySQL/MariaDB dengan nama database berakhiran _testing diterima.
- Verifikasi pada PHP 8.3.33:
  - Test baru dijalankan sebelum implementasi: gagal karena helper pengaman belum tersedia; setelah implementasi, test guard/dashboard lulus 7 test / 8 assertion.
  - Database lokal rf_b_testing diperiksa belum ada, lalu dibuat baru khusus Jalur B; database kerja tidak disentuh.
  - `php artisan test`: 61 lulus, 2 skipped (registrasi dinonaktifkan oleh test existing), 272 assertion. Migrasi oleh RefreshDatabase berhasil pada database test baru tersebut.
  - `composer validate --strict` dan `composer check-platform-reqs`: lulus.
  - `composer install --dry-run --no-scripts --no-interaction`: exit 0, tidak ada paket yang perlu dipasang/diubah; pemeriksaan metadata remote terbatas proxy sandbox.
  - `npm.cmd run build`: lulus, 167 modul; perlu eskalasi eksekusi karena sandbox menolak proses esbuild. Ada peringatan metadata Browserslist/baseline lama.
  - Lint tiga file PHP pengaman/test, `php artisan route:list --except-vendor` (57 route), dan `git diff --check`: lulus.
  - Review terpisah read-only atas B0.2: tidak menemukan blocker.
- Risiko tersisa:
  - Workflow GitHub Actions/MariaDB container belum dijalankan; hasil lokal tidak membuktikan CI remote lulus. Instalasi npm ci bersih kemudian berhasil diverifikasi pada B0.3.
  - Composer melaporkan 48 security advisory pada 14 paket terkunci saat sinkronisasi metadata; perlu triase terpisah sebelum rilis, tanpa update paket menyeluruh dalam batch ini.
  - Perbaikan .env.dev/.env.staging/.env.testing dan run-ci.sh dilanjutkan dan diverifikasi pada B0.3 di bawah.
  - Perubahan package-lock.json pengguna dipertahankan (diff tetap 23 baris dihapus).

## B0.3 / P0 — Environment dan runner CI aman

- Status: REVIEW (2026-10-16); implementasi dan runner lengkap lulus lokal pada PHP 8.3.33, CI remote belum dijalankan.
- Scope: hentikan pelacakan environment rahasia tanpa mengubah salinan lokal; template MySQL tanpa rahasia; runner CI memakai konfigurasi test eksplisit dan tidak menimpa environment/memanggil seeder akun.
- File: .gitignore, .env.example, template .env.dev.example/.env.staging.example/.env.testing.example, run-ci.sh, scripts/assert-test-database.php, tests/Tooling/run-ci-test.sh, .github/workflows/tests.yml, composer.json, catatan jalur B. Penghapusan tracking .env.dev/.env.staging/.env.testing sudah staged dengan git rm --cached; file lokal tetap ada.
- Perubahan:
  - Seluruh .env.* diabaikan, kecuali template *.example tanpa rahasia; template memakai MySQL dan kredensial kosong. Template staging menonaktifkan debug.
  - Runner mewajibkan TEST_DB_DATABASE berakhiran _testing dan TEST_DB_USERNAME; koneksi harus MySQL/MariaDB. Nilai DB_* aplikasi diabaikan sebagai target runner. APP_KEY acak hanya ada dalam proses test.
  - Runner memasang Composer terlebih dahulu, memvalidasi platform, memeriksa koneksi database test tanpa mutasi, memasang npm dari lock, build, memeriksa route dan menjalankan test. Tidak menyalin/menulis environment, migrate:fresh atau seed akun.
  - Runner menghindari cache konfigurasi aplikasi dengan path test terpisah dan menolak cache test yang sudah ada. Exception preflight tidak mencetak URL/kredensial.
  - CI memakai runner yang sama dan regression test pengamannya. Hook create-project Composer tidak lagi membuat SQLite atau otomatis melakukan migration sebelum konfigurasi database lokal siap.
- Verifikasi:
  - RED: test runner terhadap script lama gagal karena konfigurasi database kerja mencapai command eksternal. GREEN: regression test Bash lulus untuk database kerja, database/user kosong, SQLite, cache lama, preservasi environment, urutan command dan kegagalan preflight.
  - `bash -n run-ci.sh tests/Tooling/run-ci-test.sh`, lint PHP preflight, `composer validate --strict`, dan `git diff --check`: lulus.
  - Preflight nyata menolak rf_db sebelum query dan berhasil pada rf_b_testing (SELECT 1 tanpa mutasi).
  - Empat template berhasil di-parse dengan Dotenv; APP_KEY dan kredensial kosong/null.
  - Runner lengkap `COMPOSER_DISABLE_NETWORK=1 TEST_DB_DATABASE=rf_b_testing TEST_DB_USERNAME=root bash run-ci.sh`: exit 0 pada Git Bash/PHP 8.3.33. Composer install dari paket lokal terkunci, validasi/platform, preflight, npm ci (175 paket), build (167 modul), route:list (59 route termasuk vendor), dan test berhasil; 61 lulus, 2 skipped registrasi existing, 272 assertion.
  - Percobaan runner online dihentikan saat Composer lama berada pada generasi autoload; pengulangan dengan Composer offline akhirnya berhasil. Penyebab kelambatan autoload belum dipastikan; akses registry Composer online bukan bagian yang terbukti dari hasil ini.
  - `git ls-files '*env*'` kini hanya menampilkan .env.example; ketiga template baru tersedia sebagai file untracked untuk review. Keempat environment nyata diabaikan Git.
  - Hash SHA-256 .env/.env.dev/.env.staging/.env.testing dan package-lock.json cocok dengan baseline awal batch setelah seluruh verifikasi. Perubahan pengguna pada package-lock.json tetap persis sama.
  - Review terpisah read-only tidak menemukan blocker; tidak membaca environment nyata/deletion diff yang mengandung rahasia.
- Insiden yang dipulihkan: versi awal test harness belum berpindah ke direktori sementara, sehingga script lama sempat menimpa .env/.env.testing. .env dipulihkan dari histori editor lokal yang hash-nya cocok; .env.testing dipulihkan dari HEAD. Keduanya kembali identik dengan hash awal. Harness diperbaiki agar cd ke direktori sementara sebelum menjalankan runner; tidak ada database nyata yang digunakan oleh regression test stub.
- Risiko tersisa:
  - Salinan rahasia tetap ada dalam histori Git; rotasi server memerlukan penilaian data terenkripsi dan tidak dilakukan otomatis.
  - npm ci melaporkan 20 kerentanan (4 moderate, 14 high, 2 critical), selain 48 advisory Composer yang dicatat B0.2. Tidak menjalankan audit fix/update paket; triase diperlukan sebelum rilis.
  - Build tetap memiliki peringatan metadata browser lama; npm memperingatkan install script esbuild belum tercakup allowScripts pada versi npm lokal, tetapi build berhasil.
  - Workflow GitHub Actions dan service MariaDB CI remote belum dijalankan. B0 belum dinyatakan siap rilis hanya dari verifikasi lokal.

### Pemakaian environment dan runner

- Instalasi baru: salin `.env.example` ke `.env` hanya jika `.env` belum ada, isi koneksi database lokal milik sendiri, lalu buat APP_KEY lokal. Template dev/staging tersedia sebagai `.env.dev.example` dan `.env.staging.example`; jangan menimpa konfigurasi server yang sudah berjalan.
- Test manual: salin `.env.testing.example` ke `.env.testing` hanya jika belum ada, isi akun database test milik sendiri, dan jalankan `php artisan key:generate --env=testing` hanya pada environment test yang baru dibuat. Sesuaikan DB_DATABASE bila perlu; nama wajib berakhiran `_testing`. Jangan berbagi database test antara A dan B.
- Buat database test kosong dan akun MySQL/MariaDB khusus dengan hak hanya pada database tersebut. Runner tidak membuat database atau menjalankan seeder. Test berbasis RefreshDatabase dapat menghapus/membuat ulang tabel di database test tersebut.
- Jalankan dari Bash dengan konfigurasi eksplisit (contoh hanya nama dummy, password disediakan lewat environment lokal, bukan Git):

  ```bash
  export TEST_DB_DATABASE=rf_b_testing
  export TEST_DB_USERNAME=rf_test
  # Isi TEST_DB_PASSWORD melalui environment lokal yang aman bila diperlukan.
  bash tests/Tooling/run-ci-test.sh
  bash run-ci.sh
  ```

- Opsional: TEST_DB_HOST, TEST_DB_PORT, TEST_DB_CONNECTION (mysql/mariadb). DB_* dari shell dan isi `.env` tidak dijadikan target runner. APP_KEY test dibuat acak dalam proses; file lokal tidak ditulis ulang.
- Runner menggunakan path cache konfigurasi terpisah `bootstrap/cache/config.ci.php` dan menolak jika file itu ada. Cache aplikasi normal tidak dihapus. GitHub Actions memakai runner yang sama agar urutan instalasi, preflight, build, route dan test konsisten.
- Sebelum rekan menarik commit penghapusan tracking, simpan salinan environment mereka sendiri di lokasi aman. Hanya file `*.example` yang boleh masuk Git; salinan lokal environment tetap diabaikan.
- Kunci yang pernah di-commit masih ada dalam histori. Pengelola server perlu menginventarisasi pemakaian APP_KEY (termasuk field/file terenkripsi, cookie dan sesi) serta kredensial DB/mail sebelum menetapkan rotasi. Tidak ada kunci server yang diganti dan histori Git tidak ditulis ulang dalam batch ini.

## B1.1 / P1 — Statistik dashboard sesuai wilayah

- Status: REVIEW (2026-10-16); implementasi, regression test, suite lengkap dan build lulus lokal pada PHP 8.3.33.
- Scope: dashboard dan endpoint chart mengikuti izin statistik (pengelola) dan wilayah koorda; hitungan bulan mencakup tahun; hapus grafik kajian dummy dan request grafik global yang tidak dipakai.
- File: app/Http/Controllers/DashboardController.php, app/Http/Controllers/ChartController.php, blok charts routes/web.php, resources/views/dashboard/index.blade.php, resources/js/app.js, tests/Feature/DashboardStatisticsTest.php.
- Definisi dipertahankan: populasi statistik adalah record User seperti daftar alumni existing, termasuk pengelola yang juga tercatat sebagai anggota; tidak mengubah arti role akun menjadi filter level/keanggotaan.
- Perubahan:
  - Total, aktif, baru, pending, persentase, tren bulanan dan distribusi dashboard memakai scope wilayah yang sama. Admin/super admin lintas wilayah; koorda tanpa wilayah ditolak sebelum query statistik. Parameter wilayah kiriman koorda tidak mengubah scope.
  - Hitungan anggota baru mencocokkan bulan dan tahun; tren tahunan mengirim angka integer dengan 12 bulan termasuk bulan kosong.
  - Seluruh endpoint chart dilindungi auth, gate view-alumni existing dan kelas ScopeWilayah langsung; semua query termasuk AB memakai scope yang sama.
  - Dashboard meneruskan akun non-pengelola ke profile.edit agar tujuan default login alumni tetap dapat digunakan tanpa membuka statistik. Endpoint chart alumni tetap 403, termasuk alumni yang sudah memiliki wilayah.
  - Hapus data/grafik kajian dummy dari controller dan view; tampilkan teks Statistik kajian belum tersedia. Angka laporan nyata menunggu workflow B2.
  - ApexCharts existing tetap menjadi grafik halaman aktif. Import/init Chart.js global dilepas sehingga halaman lain tidak mengirim request monthly chart yang tidak dipakainya. Route dan signature method publik dipertahankan; gate/model/middleware A tidak diedit.
- Verifikasi:
  - RED sebelum perbaikan: 4 test gagal karena angka lintas wilayah, alumni baru memasukkan tahun sebelumnya, akses koorda tanpa wilayah, dan chart tanpa scope; test tamu sudah lulus dari auth existing.
  - Test statistik akhir mencakup koorda dua wilayah dan query override, admin/super admin (termasuk tanpa wilayah), alumni dengan/tanpa wilayah, tamu, periode tahun berbeda, distribusi dan empat endpoint chart.
  - `php artisan test`: 67 lulus, 2 skipped registrasi existing, 359 assertion, pada database rf_b_testing. Tidak menjalankan test pada database kerja.
  - `npm.cmd run build`: lulus (162 modul), app JS 212.64 KB / gzip 60.88 KB, dari 420.48 KB / gzip 131.72 KB sebelum pelepasan Chart.js global. Peringatan metadata browser lama masih ada.
  - Lint kedua controller dan test baru, `php artisan route:list --path=charts -v`, dan `git diff --check`: lulus. Empat route chart memperlihatkan auth, can:view-alumni dan ScopeWilayah.
  - Review terpisah read-only tidak menemukan blocker.
- Risiko integrasi/batas verifikasi:
  - Gate view-alumni dan ScopeWilayah tetap milik A; pengujian ulang gate/endpoint pada merge diperlukan. A dapat menyederhanakan redirect login alumni ke profil pada alur auth miliknya; fallback dashboard B tetap aman.
  - Browser/eksekusi grafik CDN tidak diuji dalam batch ini. Layout dashboard existing masih khusus desktop (hidden md:block); perbaikan responsivitas dan QA browser dicatat untuk tahap QA berikutnya.
  - Risiko rilis B0 (CI remote, advisory dependency, inventarisasi kunci server) tetap terbuka; statistik kajian belum tersedia sampai B2.

## B1.2 / P1 — Penyimpanan gambar dan route konten

- Status: REVIEW (2026-10-16); implementasi, test kegagalan, suite lengkap dan build lulus lokal pada PHP 8.3.33.
- Scope: file baru dibersihkan bila penyimpanan record gagal; gambar lama bertahan hingga transaksi berhasil; slug post unik; resource route hanya action yang tersedia; feedback validasi dan input lama pada form.
- File: app/Http/Controllers/Management/PostController.php, GalleryController.php, app/Models/Post.php, blok management routes/web.php, resources/views/management/refleksi/index.blade.php, galeri/index.blade.php, feedback.blade.php, tests/Feature/ContentManagementTest.php.
- Perubahan:
  - Create/update menyimpan file baru sebelum transaksi record. Exception termasuk event setelah SQL menggagalkan transaksi dan membersihkan file baru; gambar lama baru dihapus setelah transaksi berhasil. Delete juga menghapus record lebih dulu dalam transaksi, lalu gambar.
  - Nilai false dari store/save/delete ditangani sebagai kegagalan, bukan flash sukses. Gagal membersihkan gambar dilaporkan lewat logger; path URL eksternal atau di luar folder upload domain tidak dihapus.
  - Input post/galeri memakai hasil validasi; upload JPG/JPEG/PNG maksimal 2 MB, bukan path kiriman pengguna. Update tanpa upload mempertahankan gambar lama.
  - Slug otomatis memakai judul yang dibatasi 228 karakter ditambah akhiran ULID 26 karakter, maksimal 255. Judul tanpa slug memakai basis refleksi; slug tidak diganti saat judul tetap. Slug eksplisit existing pada create dipertahankan, constraint unique database tetap berlaku.
  - Route post/galeri dibatasi index/store/update/destroy. Method create Gallery tetap ada dengan redirect ke index untuk menjaga signature publik, tetapi route create yang view-nya tidak tersedia tidak diekspos.
  - Kedua form menampilkan flash sukses dan error validasi. Input lama dipertahankan; backend menetapkan _editing_id dari target update agar modal gagal validasi dibuka kembali dengan action/field yang benar. Error juga terlihat di dalam modal.
  - Edit post memakai data attribute ter-escape dan event listener, menggantikan penyisipan konten ke JavaScript inline. Form action memakai nama route, preview galeri mendukung URL gambar existing. Tidak ada dependency/migration baru.
- Verifikasi:
  - RED awal: 10 test gagal karena rollback/file lifecycle, feedback validasi, slug duplikat dan route action kosong; 4 skenario sukses existing sudah lulus. Fixture file invalid kemudian disesuaikan memakai UploadedFile nyata agar MIME diambil dari isi, bukan MIME hasil tebakan ekstensi pada fake.
  - 22 skenario konten mencakup create/replace/delete sukses, exception setelah insert/update/delete, write disk false, save dibatalkan, file palsu/GIF/>2 MB ditolak, update teks tanpa gambar, old input/modal target, escaping payload JavaScript, judul duplikat/panjang/tanpa huruf, dan route tidak tersedia.
  - Review menemukan race pada alokasi slug check-then-insert. Test baru menyisipkan insert judul sama setelah alokasi slug pertama dan sebelum SQL: gagal dengan duplicate key sebelum perbaikan ULID, lalu lulus setelah perbaikan. Review ulang menyatakan temuan terselesaikan.
  - `php artisan test` akhir: 89 lulus, 2 skipped registrasi existing, 554 assertion pada rf_b_testing; tidak menyentuh database kerja.
  - `npm.cmd run build`: lulus, 162 modul; peringatan metadata browser lama masih ada. Perubahan terakhir setelah build hanya pada model/test slug, bukan asset.
  - Lint kedua controller/model/test, `php artisan route:list --path=management` (8 route valid), `composer validate --strict`, dan `git diff --check`: lulus.
  - Pemeriksaan 700 baris terakhir log tanpa menampilkan isinya: 9 entry error testing, semuanya fault injection yang diharapkan; tidak ada error testing lain pada rentang itu.
  - Hash .env/.env.dev/.env.staging/.env.testing dan package-lock.json tetap cocok dengan baseline preservasi B0.3.
- Batas integrasi: gate manage-content tetap milik A0. Test bisnis memakai fixture admin; test izin koorda ke endpoint ini harus diulang pada hasil merge karena gate existing masih mengizinkannya.
- Batas operasional: disk yang menolak penghapusan dapat menyisakan file yatim yang tercatat di log; transaksi SQL dan filesystem bukan transaksi tunggal. Kegagalan infrastruktur mengembalikan error server, sedangkan validasi input kembali ke form dengan pesan. Interaksi modal belum diuji dalam browser; HTTP/DOM escaping dan old input sudah diuji. Layout management existing masih khusus desktop dan menunggu QA responsif.

## B1.3 / P1 — Konten publik dari database

- Status: REVIEW (2026-10-16); implementasi, suite lengkap, build dan review kode lulus lokal pada PHP 8.3.33.
- Scope: homepage aktif memakai controller dan post published; galeri database; daftar/detail publik; empty state; ganti materi/tautan template aktif dengan informasi yang dapat dipertanggungjawabkan.
- File: app/Http/Controllers/LandingController.php, app/Models/Post.php/Gallery.php, app/Support/PublicImageUrl.php, blok homepage/public routes/web.php, resources/views/landingpage/home.blade.php, layouts/landing.blade.php, sections navbar/jumbotron/statistik/tentang/berita/galeri/agenda/cta/footer, partials/navigation.blade.php, posts/index.blade.php, posts/show.blade.php, gallery.blade.php, tests/Feature/PublicContentTest.php dan ExampleTest.php.
- Pendekatan: pertahankan nama route landing_page; detail via slug dan query published di server, daftar dipaginasi; konten artikel berupa teks ter-escape sesuai textarea pengelolaan existing. Galeri mengikuti schema existing tanpa field draft/publication baru.
- Batas integrasi: registrasi publik masih milik A1; gunakan CTA login yang sudah bekerja. Informasi tim/kontak/agenda resmi belum tersedia sehingga materi demo tidak ditampilkan sebagai data organisasi.
- Perubahan:
  - Root tetap bernama landing_page, kini memanggil LandingController@index dan view homepage aktif. Query SQL dibatasi ke 3 post terbit terbaru dan 6 galeri terbaru, hanya kolom yang dipakai kartu. Statistik publik menghitung post terbit dan galeri, bukan data User.
  - Route baru public.posts.index (/refleksi), public.posts.show (/refleksi/{slug}), public.gallery.index (/galeri). Kedua daftar dipaginasi 12 item dengan tie-break id; post diurutkan berdasarkan published_at dan id.
  - Semua query post publik memakai scopePublished existing, termasuk detail. Draft, future dan slug tidak ada mengembalikan 404; publikasi tepat now diterima; menarik published_at langsung menutup akses.
  - Cast published_at ke datetime untuk waktu terstruktur dan tanggal Indonesia. Isi artikel teks ter-escape dan whitespace dipertahankan; HTML dari textarea tidak dieksekusi.
  - Resolver gambar bersama hanya menerima HTTP/HTTPS valid atau path upload lokal domain yang sesuai, termasuk format storage/legacy; skema data/javascript serta traversal ditolak. Gambar lokal memakai URL disk public; gambar eksternal existing tetap didukung tanpa fetch server.
  - Materi template pada halaman aktif diganti dengan nama/logo/kutipan existing dan penjelasan berbasis PRD. Galeri menjadi dokumentasi database, bukan testimoni rekaan. Tidak menampilkan tim/contact palsu; agenda diberi status belum tersedia, CTA mengarah login existing.
  - Layout publik memakai stylesheet Vite/Tailwind existing dan navigasi HTML native (termasuk details menu mobile, skip link, alt image dan landmark). Script template WOW/Swiper/menu yang bergantung elemen demo tidak dimuat pada halaman aktif. Tidak ada dependency baru.
  - Berkas template lama yang tidak di-include (mis. tim/kontak dan layout historis) tetap disimpan tetapi bukan bagian homepage/route publik aktif.
  - ExampleTest homepage sekarang memakai RefreshDatabase agar berdiri sendiri pada database test kosong; sebelumnya homepage tidak menjalankan query database.
- Verifikasi:
  - RED sebelum implementasi: 7 test publik gagal karena homepage masih template, daftar/detail tidak ada dan konten tersimpan tidak tampil.
  - Test publik: 7 lulus / 93 assertion, mencakup batas publikasi, draft/future/withdrawal, urutan/pagination, empty state, tautan/anchor tanpa demo, payload HTML berbahaya, URL gambar, serta create admin -> baca sebagai guest dengan gambar upload.
  - `php artisan test`: 96 lulus, 2 skipped registrasi existing, 647 assertion pada rf_b_testing; database kerja tidak digunakan.
  - `npm.cmd run build`: lulus, 162 modul; stylesheet publik 171.42 KB / gzip 36.51 KB, kelas layout/konten terkait ikut dihasilkan. Peringatan metadata browser lama masih ada.
  - Lint controller/resolver/model/test, route:list untuk tiga route public dan landing_page, serta git diff --check: lulus.
  - Review read-only terpisah tidak menemukan blocker; tidak membaca environment rahasia atau menjalankan mutasi database.
- Batas verifikasi/risiko:
  - Tampilan nyata/browser, menu native dan pemuatan gambar lewat symlink public/storage belum diuji di browser pada batch ini; QA browser tetap diperlukan sebelum rilis.
  - Gallery tidak memiliki field status publikasi existing, sehingga seluruh record galeri dianggap publik. Pengelolaan post existing langsung menerbitkan post baru; UI draft/scheduling tidak ditambah di batch ini, tetapi filter field published_at existing diterapkan.
  - Materi artikel ditampilkan sebagai teks; bila rich text dibutuhkan nanti, perlu sanitizer/kontrak editor yang jelas. Nama tim, kontak dan agenda resmi memerlukan sumber dari pengelola, bukan data contoh.
  - Risiko rilis B0 serta izin manage-content A0 tetap mengikuti catatan batch sebelumnya.

## B1.4 / P1 — Status laporan yang belum tersedia

- Status: REVIEW (2026-10-16); implementasi, enam test domain, suite lengkap dan build lulus lokal pada PHP 8.3.33.
- Scope: hapus form/riwayat laporan demo, tampilkan status belum tersedia yang bisa dibaca desktop/mobile, lindungi halaman laporan dengan auth, dan perbaiki review placeholder tanpa alias middleware yang belum terdaftar.
- File: resources/views/laporan/index.blade.php, resources/views/laporan/review.blade.php, app/Http/Controllers/ReportController.php, blok laporan/review routes/web.php, tests/Feature/ReportAvailabilityTest.php.
- Kontrak: nama route laporan.index/laporan.review dan signature index(Request) dipertahankan. Belum ada endpoint penyimpanan, approve/reject atau export; tidak mengubah model/migration/gate milik A.
- Perubahan:
  - Laporan index hanya dapat dibuka setelah login dan menampilkan status Fitur laporan kajian belum tersedia; form desktop/mobile, pilihan wilayah hardcoded, tabel/kartu riwayat demo dan link aksi fallback # dihapus.
  - Review memakai view status yang nyata, bukan view hilang. Persetujuan, penolakan, rekap dan ekspor tidak ditampilkan sebagai tombol yang seolah berfungsi.
  - Group route review menggunakan kelas ScopeWilayah langsung, bukan alias scope.wilayah yang belum terdaftar. Gate review-report existing dipertahankan. Group ini juga memuat debug.scope existing; nama, parameter dan response endpoint diagnostik tersebut dipertahankan.
  - Controller membaca wilayah efektif dari request attributes yang ditetapkan middleware, bukan request get() yang dapat mendahulukan query kiriman koorda.
  - Kedua status tampil tanpa wrapper desktop-only dan menyediakan tautan kembali yang valid. Tidak menampilkan data laporan existing seolah sudah menjadi workflow aktif, tidak menghapus record, dan tidak menambah endpoint mutasi.
- Verifikasi:
  - RED sebelum implementasi: 5 test gagal (halaman tamu terbuka, form demo/status, alias/view review dan scope), 1 test POST unsupported sudah lulus.
  - GREEN `php artisan test --filter=ReportAvailabilityTest`: 6 lulus / 60 assertion; mencakup empat role pada halaman status, auth tamu, admin/super admin/koorda review, koorda mencoba query wilayah lain, alumni dan koorda tanpa wilayah ditolak, POST 405 tanpa insert, serta record existing tetap utuh/tidak dipresentasikan sebagai history.
  - `php artisan test`: 102 lulus, 2 skipped registrasi existing, 707 assertion pada rf_b_testing. Database kerja tidak digunakan.
  - `npm.cmd run build`: lulus, 162 modul; peringatan metadata browser lama masih ada.
  - Lint controller/test, route:list --path=laporan -v (index auth; review auth + ScopeWilayah + can:review-report), dan git diff --check: lulus.
  - Hash keempat environment nyata dan package-lock.json tetap cocok dengan baseline preservasi; diff pengguna pada package-lock.json tetap 23 baris dihapus.
- Batas verifikasi/lanjutan: status ini memang sementara; test availability harus disesuaikan saat workflow B2 aktif. Interaksi/tampilan browser belum diuji. Endpoint debug.scope existing perlu dievaluasi pada kesiapan staging/rilis. Risiko integrasi gate A serta risiko rilis B0 tetap terbuka.

## B2.1 / P2 — Draf laporan tersimpan dan detail privat

- Status: REVIEW (2026-10-16); implementasi dan verifikasi lokal selesai pada PHP 8.3.33.
- Acuan: PRD modul Laporan Kajian dan PLAN Rencana Aktif B2; jalankan langsung per langkah dalam batch ini.
- Scope: create/store/list/detail memakai KajianReport existing; hanya simpan draf, tanpa submit/review/export aktif. Foto opsional disimpan privat dengan endpoint baca yang memeriksa akses.
- Kebijakan akses: akun aktif dengan role existing; admin/super admin lintas wilayah dan memilih wilayah valid saat create. Non-admin wajib punya wilayah dari akun, kiriman wilayah diabaikan. Alumni membaca laporan sendiri dalam wilayahnya. Koorda membaca draf sendiri dan laporan non-draft wilayahnya. Draf orang lain tetap privat terhadap non-admin.
- File rencana: ReportController (index review existing dipertahankan), StoreKajianReportRequest baru, blok route laporan, view list/create/show dan feedback, test laporan baru serta penyesuaian ReportAvailabilityTest.
- Rencana eksekusi:
  1. Test RED untuk penyimpanan, pemalsuan field, scope list/detail/foto, validasi, status akun, dan kegagalan upload/database.
  2. Validasi schema dan hak akun di FormRequest; total peserta serta field identitas/status ditetapkan server. Pertahankan model/gate/middleware A.
  3. Tambah query laporan yang sama untuk list/detail/foto, create dan store transaksional; bersihkan foto baru jika transaksi gagal.
  4. Form/list/detail responsif dengan hasil simpan/validasi dan label draf; review tetap status belum tersedia. Sesuaikan test availability sementara dari B1.4.
  5. Test domain, suite lengkap PHP 8.3, lint, route:list, build, review keamanan; catat hasil sebelum menutup batch.
- Risiko: status/level/role akun berbeda dari status laporan. Tidak mengaktifkan aksi submit sampai edit/transisi B2.2 tersedia. Tidak ada migration atau perubahan database kerja.
- Hasil implementasi:
  - Route index beralih ke listing nyata dengan pagination dan urutan tanggal/id stabil. Route baru create/store/show/photo memakai auth + kelas ScopeWilayah langsung; parameter detail numerik tidak mengambil alih route review existing. Signature index(Request) review dipertahankan.
  - StoreKajianReportRequest memvalidasi field sesuai ukuran schema, tanggal valid, angka nonnegatif dan foto JPG/PNG maksimal 2 MB. Non-admin memakai wilayah akun; field user, total, status, reviewer dan path foto kiriman klien tidak dipakai.
  - Query akses dipakai bersama untuk list/detail/foto: akun aktif ber-role existing saja; alumni hanya laporan sendiri dalam wilayahnya; koorda draf sendiri dan non-draft wilayahnya; admin/super admin lintas wilayah termasuk record legacy tanpa wilayah.
  - Simpan draf dalam transaksi; upload ke disk local storage/app/private, tanpa URL publik. Kegagalan/event pembatalan simpan membersihkan foto baru dan tidak menampilkan sukses. Endpoint foto memeriksa akses, path generated report-photos, keberadaan file, serta header private/no-store dan nosniff. Path legacy eksternal/tidak aman tidak disajikan.
  - Form mempertahankan old input dan menampilkan pesan validasi; judul/catatan di-escape. List/detail menampilkan status nyata dan jumlah peserta dari server. Submit/review/ekspor tetap diberi status belum tersedia.
- File aktual: app/Http/Controllers/ReportController.php, app/Http/Requests/StoreKajianReportRequest.php, blok laporan routes/web.php, resources/views/laporan/{index,create,show,feedback,status}.blade.php, tests/Feature/{KajianReportDraftTest,ReportAvailabilityTest}.php, catatan jalur ini. Model/schema/gate A tidak diubah.
- Verifikasi:
  - RED awal: 9 test draf gagal karena endpoint/form/store belum diimplementasikan, 14 assertion.
  - GREEN suite terakhir `php artisan test`: 111 lulus, 2 skipped registrasi existing, 792 assertion pada database terisolasi rf_b_testing; termasuk 11 test draf dan 4 test availability review. Mencakup pemalsuan field, lintas wilayah/penulis, role/status akun, foto privat, path traversal/external legacy, rollback/event pembatalan dan cleanup foto.
  - `npm.cmd run build`: lulus, 162 modul. Peringatan metadata browser lama tetap ada.
  - Lint controller, request, kedua test; `php artisan route:list --path=laporan -v` (6 route); `git diff --check`: lulus.
  - Review independen readonly tidak menemukan blocker keamanan/kebenaran pada batch ini.
  - Hash .env/.env.dev/.env.staging/.env.testing dan package-lock.json cocok baseline; perubahan pengguna di package-lock.json tetap 0 tambahan/23 baris dihapus.
- Batas verifikasi: belum diuji dalam browser atau CI remote. Foto legacy di luar format/path privat tidak dimigrasikan. Edit draf, submit, approve/reject, rekap dan ekspor belum diaktifkan. Risiko rilis B0/integrasi A0 tetap mengikuti catatan sebelumnya.

## B2.2 / P2 — Edit draf dan pengiriman

- Status: REVIEW (2026-10-16); implementasi dan verifikasi lokal selesai pada PHP 8.3.33.
- Scope: hanya pemilik draf yang dapat mengedit/mengirim, tetap mengikuti status akun dan wilayah B2.1. Admin dapat membaca lintas wilayah, tetapi tidak mengedit/mengirim atas nama pelapor lain. Wilayah laporan tidak berubah saat edit. Foto opsional dapat diganti dengan cleanup aman.
- Pengiriman: validasi ulang data tersimpan, transisi atomik draft ke submitted dengan row lock; kiriman field status/identitas/review diabaikan. Setelah submitted, update/submit ulang ditolak. Approve/reject, rekap, ekspor belum tersedia.
- Langkah: test RED akses/transisi/rollback/foto; implementasi request/controller/routes; pakai ulang form dengan feedback dan konfirmasi kirim; test domain/suite/build/lint serta review sebelum REVIEW.
- Risiko: tidak ada version column; lock dan pemeriksaan ulang status mencegah edit setelah submit, sedangkan dua edit draf berturut-turut memakai hasil simpan terakhir. Tidak menambah migration atau mengubah file A.
- Perubahan:
  - Tambah GET laporan/{report}/edit, PUT laporan/{report}, POST laporan/{report}/submit (nama laporan.edit/update/submit); semua memakai auth dan ScopeWilayah existing. Pemilik harus aktif dan tetap memenuhi scope wilayah; pelapor lain termasuk admin tidak dapat mengambil alih edit/pengiriman.
  - UpdateKajianReportRequest memakai aturan field/upload existing, tanpa menerima perubahan wilayah/identitas/status/review. Edit dan submit memeriksa ulang status draft memakai SELECT FOR UPDATE di dalam transaksi. Konflik status menghasilkan HTTP 409, akses laporan di luar kepemilikan/scope menghasilkan 404.
  - Submit memvalidasi ulang field yang tersimpan, menghitung ulang total peserta, mengosongkan metadata review lama, dan hanya mengubah status ke submitted. Payload kiriman tidak mengganti isi laporan. Kegagalan simpan membatalkan transaksi.
  - Foto pengganti tersimpan di disk privat; foto lama baru dihapus setelah transaksi berhasil. Tanpa upload baru foto lama tetap dipakai; jika transaksi gagal foto baru dibersihkan. Cleanup terbatas pada path report-photos generated yang aman.
  - Form create dipakai ulang untuk edit dengan nilai existing/old input dan feedback. Detail draf milik sendiri menampilkan edit serta form kirim dengan konfirmasi pemeriksaan dan penjelasan bahwa laporan akan terkunci. Detail submitted menghapus aksi edit/kirim dan menampilkan status terkunci. Review/ekspor tetap diberi label belum tersedia.
- File: app/Http/Controllers/ReportController.php, app/Http/Requests/UpdateKajianReportRequest.php, blok laporan routes/web.php, resources/views/laporan/{create,index,show}.blade.php, tests/Feature/KajianReportSubmissionTest.php, catatan ini.
- Verifikasi:
  - RED: 8 test awal gagal / 10 assertion karena endpoint edit/update/submit belum tersedia.
  - GREEN `php artisan test --filter=KajianReportSubmissionTest`: 10 lulus / 112 assertion. Mencakup empat role pemilik, penolakan pengambilalihan, payload palsu, transisi sekali, visibilitas koorda setelah kirim, validasi data tersimpan, ganti foto/rollback, akun nonaktif/pindah wilayah, serta simulasi status berubah setelah route binding sebelum lock.
  - `php artisan test`: 121 lulus, 2 skipped registrasi existing, 904 assertion pada rf_b_testing; exit 0. Tidak memakai database kerja.
  - `npm.cmd run build`: lulus, 162 modul; peringatan metadata browser lama tetap ada.
  - PHP lint controller/request/test, route:list --path=laporan -v (9 route), dan git diff --check: lulus.
  - Review independen readonly tidak menemukan blocker. Hash keempat environment nyata dan package-lock.json tetap sama dengan baseline preservasi.
- Batas verifikasi: browser dan CI remote belum dijalankan; test interleaving status bukan pengujian beban dua koneksi database bersamaan. Laporan legacy tanpa wilayah tetap tidak dapat dikirim sebelum datanya diperbaiki; batch ini tidak menyediakan pemindahan wilayah. Risiko B0/A0 yang dicatat sebelumnya tetap terbuka.

## B2.3 / P2 — Antrean review dan keputusan laporan

- Status: REVIEW (2026-10-16); implementasi dan verifikasi lokal selesai pada PHP 8.3.33.
- Scope: antrean submitted, approve/reject untuk akun reviewer aktif. Koorda wilayah sendiri; admin/super admin lintas wilayah. Pelapor tidak boleh mereview laporannya sendiri. Alasan reject wajib string maksimal 16383 karakter (batas aman TEXT utf8mb4).
- Keputusan: row lock dan cek ulang status submitted; tetapkan status/reviewed_by/reviewed_at/reject_reason dari server dalam transaksi. Keputusan ulang/draft ditolak 409. Laporan rejected tetap terkunci; revisi/pengajuan ulang di luar batch ini. Tidak mengubah gate/model A atau schema existing.
- Langkah: test RED akses/antrean/metadata/validasi/rollback/konflik; implementasi controller dan blok route B; form keputusan dan hasil review pada detail; suite/build/lint/review; catat bukti sebelum REVIEW.
- Perubahan:
  - index(Request) existing kini menampilkan antrean submitted dengan pagination 12 dan urutan tanggal/id ascending, mengecualikan laporan sendiri. Admin/super admin melihat semua wilayah secara default; query wilayah_id opsional divalidasi dan dipertahankan saat pagination. Koorda mengabaikan wilayah kiriman dan tetap terscope wilayah akun. Wilayah aktif serta tautan reset filter admin ditampilkan.
  - Route laporan.approve/reject baru (POST laporan/{report}/approve dan /reject) memakai auth, ScopeWilayah, dan gate review-report existing. Controller kembali memeriksa gate, status akun aktif, scope, larangan self-review dan status submitted dalam transaksi dengan row lock.
  - Keputusan dan reviewed_by/reviewed_at ditetapkan server. Approval membersihkan reject_reason; rejection memvalidasi alasan wajib string maksimal 16383 karakter. Payload judul/identitas/status/reviewer tidak mengubah record. Event save false/exception tidak menghasilkan sukses dan membatalkan perubahan.
  - Detail hanya menyediakan form keputusan kepada reviewer yang berhak untuk submitted orang lain; hasil review berisi nama/waktu reviewer dan alasan reject yang di-escape. Pelapor dapat membaca keputusan melalui detail existing. Link antrean tersedia pada daftar laporan untuk reviewer; teks placeholder review lama diperbarui, rekap/ekspor tetap belum tersedia.
  - Draft orang lain tetap tidak terlihat bagi koorda (404); status selain submitted yang boleh dibaca reviewer tidak dapat diputuskan (409). Rejected tetap terkunci; pengajuan revisi ulang belum tersedia.
- File: app/Http/Controllers/ReportController.php, blok laporan routes/web.php, resources/views/laporan/{review,show,index}.blade.php, tests/Feature/{KajianReportReviewTest,ReportAvailabilityTest}.php, catatan ini. Tidak mengubah gate/model/middleware A maupun migration.
- Verifikasi:
  - RED awal: 7 gagal, 1 test penolakan wilayah sudah lulus; 11 assertion. Endpoint keputusan dan antrean belum tersedia saat itu.
  - GREEN `php artisan test --filter=KajianReportReviewTest`: 10 lulus / 127 assertion. Mencakup scope antrean, tiga role reviewer, pemalsuan metadata, alasan wajib/ukuran/tipe/escaping, tamu/alumni/akun nonaktif/koorda tanpa wilayah, self-review, status terkunci, rollback/event pembatalan, status berubah setelah binding, filter admin dan pagination.
  - `php artisan test`: 131 lulus, 2 skipped registrasi existing, 1031 assertion, exit 0 pada rf_b_testing.
  - `npm.cmd run build`: lulus, 162 modul; peringatan metadata browser lama tetap ada.
  - PHP lint controller/kedua test, route:list --path=laporan -v (11 route), dan git diff --check: lulus.
  - Review independen readonly tidak menemukan blocker. Hash keempat environment nyata dan package-lock.json cocok baseline preservasi.
- Batas verifikasi: browser/CI remote belum dijalankan; simulasi interleaving bukan load test dua koneksi serentak. Rekap/ekspor serta alur revisi rejected di luar batch ini. Tidak menambah notifikasi atau audit lintas domain; metadata keputusan tersimpan pada schema laporan existing. Risiko rilis B0/integrasi A0 tetap mengikuti catatan sebelumnya.

## B2.4 / P2 — Rekap dan ekspor laporan

- Status: REVIEW (2026-10-16); implementasi dan verifikasi lokal selesai pada PHP 8.3.33.
- Scope: rekap reviewer aktif (admin/super admin global atau filter wilayah; koorda hanya wilayah akun), hanya submitted/approved/rejected. Filter tanggal kegiatan dari/sampai inklusif dan status; statistik jumlah per status serta total komponen peserta untuk seluruh hasil filter, bukan halaman saja. Peserta adalah jumlah kehadiran, bukan orang unik.
- CSV memakai query/filter yang sama, seluruh hasil secara bertahap, UTF-8, header private/no-store, sanitasi formula spreadsheet pada teks. Tidak memuat draf, email/profil, catatan bebas atau path foto. Kolom: ID/tanggal/judul/wilayah/pelapor/pemateri/tempat/peserta/durasi/status/reviewer/waktu/alasan reject.
- Rencana: test RED scope/filter/agregat/CSV/injeksi/validasi; request + controller rekap terpisah; route dan view filter/rekap/pagination/export; suite/build/lint/review lalu catat hasil. Tidak ada migration/dependensi baru atau perubahan file A.
- Perubahan:
  - Tambah ReportSummaryRequest untuk otorisasi reviewer aktif dan validasi query wilayah/status/tanggal. Koorda selalu memakai wilayah akun walaupun query dipalsukan; admin/super admin lintas wilayah secara default. Status draft tidak dapat dipilih dan tidak masuk query.
  - ReportSummaryController berbagi query filter antara rekap dan ekspor. Rekap memakai agregasi SQL per status untuk seluruh hasil filter, dengan total peserta dari peserta_l + peserta_p, bukan total_peserta legacy yang dapat tidak konsisten. List 12 per halaman berurutan tanggal/id descending dan mempertahankan filter tervalidasi.
  - Route laporan.summary GET /laporan/rekap dan laporan.export GET /laporan/ekspor memakai auth, ScopeWilayah dan can:review-report. Link tersedia pada list/review/detail bagi reviewer; placeholder lama diganti tautan nyata.
  - View menyediakan filter wilayah admin, status, rentang tanggal inklusif, reset filter, kartu angka nyata, empty state, pagination, dan unduh seluruh hasil sesuai filter. Jumlah peserta dijelaskan sebagai kehadiran, bukan orang unik.
  - CSV UTF-8 dengan BOM memakai lazyById 500 dan eager-load nama relasi terpilih, urutan ID ascending. Quoting memakai fputcsv dengan escape kosong. Teks berawalan formula (=,+,-,@ termasuk whitespace/control) diberi apostrof; respons attachment private/no-store dan nosniff. Tidak mengekspor email, catatan kegiatan atau path foto privat.
- File: app/Http/Requests/ReportSummaryRequest.php, app/Http/Controllers/ReportSummaryController.php, blok laporan routes/web.php, resources/views/laporan/{summary,index,review,show}.blade.php, tests/Feature/KajianReportSummaryTest.php, catatan ini.
- Verifikasi:
  - RED: 7 test gagal / 7 assertion karena route rekap/ekspor belum tersedia.
  - GREEN `php artisan test --filter=KajianReportSummaryTest`: 7 lulus / 85 assertion, mencakup wilayah palsu/draf, admin global/filter, batas tanggal/status, total semua halaman, CSV semua halaman, quoting/formula/privacy, validasi filter, role/status akun dan hasil kosong.
  - `php artisan test`: 138 lulus, 2 skipped registrasi existing, 1116 assertion, exit 0 pada rf_b_testing.
  - `npm.cmd run build`: lulus, 162 modul; peringatan metadata browser lama tetap ada.
  - PHP lint controller/request/test, route:list --path=laporan -v (13 route), dan git diff --check: lulus.
  - Review independen readonly tidak menemukan blocker. Hash keempat environment nyata dan package-lock.json tetap cocok baseline preservasi.
- Batas verifikasi: browser/CI remote belum diuji. CSV merupakan pembacaan data berjalan, bukan snapshot terkunci; keputusan review yang berlangsung selama ekspor dapat memengaruhi hasil filter status. CSV dan halaman memiliki urutan berbeda tetapi himpunan/filter sama. Pengujian format melalui parser CSV lokal; belum membuka file di aplikasi spreadsheet. Risiko B0/integrasi A0 tetap mengikuti catatan sebelumnya.

## B2.5 / P2 — QA publik, mobile, keyboard dan alur laporan

- Status: REVIEW (2026-10-16); dua sesi browser cloud dan verifikasi lokal selesai.
- Scope: browser nyata untuk halaman publik dan alur laporan pada data sintetis terisolasi; periksa viewport mobile, keyboard, feedback/validasi, console/network dan ekspor. Perbaikan hanya pada domain/UI B, kebutuhan A dicatat untuk integrasi.
- Persiapan: CLI browser-harness/browser-use dan tunnel belum tersedia pada PATH; connector Browser Use tersedia. Siapkan database QA terpisah, akun sementara dan server hanya untuk QA; jangan memakai database kerja atau mengubah environment nyata. Tutup server/tunnel setelah pemeriksaan.
- Pelaksanaan: memakai connector Browser Use sebagai driver browser cloud; binary cloudflared resmi disimpan sementara pada storage/app/qa-b25 yang diabaikan Git. Database baru rf_b_qa_<acak>_testing, APP_KEY, session dan disk privat/publik QA terpisah. Router sementara membatasi halaman/asset QA serta menolak /storage/file aplikasi lain. Environment nyata tidak dimuat/diubah oleh harness.
- Perbaikan UI B: layout app menambah skip link/fokus main dan memperbaiki margin negatif konten mobile; tombol drawer/akun memiliki nama, controls dan expanded; drawer memakai dialog/modal/hidden, fokus awal/trap Tab+ShiftTab/Escape/restore fokus dan background inert yang dipulihkan. Pencarian dummy dihapus, duplikasi alt avatar dibersihkan, favicon existing ditautkan pada layout app/guest.
- File produk: resources/views/layouts/{app,guest}.blade.php, resources/views/partials/nav/{topnav,drawer}.blade.php, resources/js/notus.js. Test baru tests/Feature/NavigationAccessibilityTest.php. Laporan bukti docs/aturan_pengembangan/QA_JALUR_B_B25.md. Harness/akun/database sementara tidak ditambahkan ke Git.
- Verifikasi:
  - RED navigasi: 1 gagal / 2 assertion; GREEN: 1 lulus / 9 assertion untuk markup kontrol/dialog/skip link dan penghapusan pencarian dummy.
  - Browser sesi QA-B25-20261016-01: 4/5 sebelum perbaikan favicon; publik/refleksi/galeri empty state, menu keyboard mobile, login sintetis, validasi dan create/edit/submit laporan lulus. Tidak ada draf publik, overflow horizontal 390x844 atau exception JS. Temuan favicon internal 404 diperbaiki.
  - Browser sesi QA-B25-20261016-02: 5/5 pada alur yang diuji; favicon guest/app 200, approve ID 4 dan reject ID 1 dengan validasi alasan, rekap approved 2 laporan/12 kehadiran, CSV sesuai status, drawer fokus keyboard, menu akun desktop, skip link dan konten mobile tanpa overlap lulus. Tidak ada error browser belum terselesaikan pada alur tersebut.
  - Suite akhir `php artisan test`: 139 lulus, 2 skipped registrasi existing, 1125 assertion, exit 0 pada rf_b_testing.
  - `npm.cmd run build`: lulus, 162 modul; peringatan metadata browser lama tetap ada. node --check resources/js/notus.js, lint test dan git diff --check lulus.
  - Review independen readonly tidak menemukan blocker. Sesi reviewer logout/browser ditutup menurut hasil tool; server/tunnel dihentikan dan database QA khusus dihapus setelah verifikasi nama state.
- Integrasi A: label Email/Password di auth/login belum memiliki asosiasi for/id lengkap dan tombol toggle password belum bernama aksesibel. Dicatat pada laporan QA; file A tidak diubah. Skor browser bukan klaim aksesibilitas menyeluruh atau persetujuan rilis.
- Batas: artifact screenshot/CSV tetap pada outputs sesi Browser Use, tidak disalin lokal; laporan Markdown merekam hasil tool dan URL tunnel yang kini ditutup. Login/profile A secara menyeluruh, pembaca layar lintas platform, CI remote, staging, backup/pemulihan dan risiko B0/A0 belum tertutup.

## B2.6 / P2 — Kesiapan staging/rilis dan latihan pemulihan

- Status: REVIEW (2026-10-17) untuk persiapan dan drill lokal; verifikasi staging nyata belum dilakukan.
- Scope: panduan deploy/backup/restore/rollback dan latihan lokal pada dua database baru khusus uji; dump SQL, file publik/privat, checksum dan dekripsi dengan kunci sintetis dipulihkan bersama. Tidak deploy, seed atau restore ke lingkungan kerja/staging/produksi.
- Langkah: periksa alat/kontrak deployment; siapkan drill terisolasi dengan guard dan fixture sintetis; jalankan dump/restore serta pembuktian data/file/kunci; tulis runbook dan hasil verifikasi/review. Staging nyata menunggu target/akses yang tersedia dan hasil integrasi A.
- Ruling: gunakan latihan lokal sintetis untuk memverifikasi mekanisme backup/pemulihan sekarang; hasilnya tidak dianggap bukti staging atau izin rilis, karena target staging belum teridentifikasi dan risiko integrasi A0/dependensi tetap terbuka.
- Perubahan:
  - RUNBOOK_STAGING_JALUR_B.md mencakup prasyarat merge/P0/advisory/CI, environment dan APP_KEY existing, artefak lockfile, maintenance/drain worker untuk konsistensi SQL+file, dump/backup terenkripsi di luar public, checksum, migration tanpa seed, smoke per role, rollback kode vs restore database/file/key, pemulihan pada database kosong dan pengukuran RTO/RPO staging.
  - scripts/rehearse-recovery.php membuat dua database baru acak berakhiran _testing pada host MySQL 127.0.0.1 dari konfigurasi phpunit.xml; tidak membaca .env nyata dan tidak menerima database existing. Migration tanpa seed dan seluruh fixture/data/kunci bersifat sintetis.
  - Drill menjalankan mysqldump/client MySQL melalui Process argv dengan credential file privat sementara, arsip SQL+file+canary key dan manifest checksum, restore ke database baru, lalu verifikasi baris/relasi role reviewer/status/peserta/foto privat/dekripsi sebelum cleanup database serta direktori miliknya.
  - Review menemukan cleanup awal berhenti ketika satu DROP gagal. RecoveryCleanup sekarang mencoba setiap resource secara independen dan melaporkan agregat kegagalan setelah semua dicoba; regression test mereproduksi perilaku fail-fast lama sebelum perbaikan. Reviewer mengonfirmasi perbaikan dan tidak menemukan isu tersisa pada fokus review.
  - README instalasi memakai npm ci, membedakan generate key instalasi baru vs existing, menghapus migrate --seed dari langkah umum dan merujuk runbook staging.
- File: scripts/rehearse-recovery.php, scripts/RecoveryCleanup.php, tests/Unit/RecoveryCleanupTest.php, README.md, docs/aturan_pengembangan/{RUNBOOK_STAGING_JALUR_B,BUKTI_PEMULIHAN_B26}.md dan catatan ini. Tidak mengubah runtime aplikasi, file A, migration, lockfile atau environment nyata.
- Verifikasi:
  - Drill final exit 0 pada PHP 8.3.33/MySQL 8.4.3: 22 migration, 2 user, 2 role, 2 pivot role, 1 wilayah/post/gallery/laporan; 3 file cocok checksum; metadata reviewer/role, dekripsi, deteksi byte rusak dan cleanup PASS. Restore fixture 1,979 detik; durasi total 7,4 detik (bukan target RTO).
  - Argumen wajib/binary tidak tersedia ditolak exit 2 sebelum modifikasi database. Tidak ada direktori recovery-drill tersisa setelah run final.
  - Test regresi cleanup: RED 1 gagal / 1 assertion pada perilaku fail-fast; GREEN 1 lulus / 2 assertion.
  - `php artisan test`: 140 lulus, 2 skipped registrasi existing, 1127 assertion, exit 0 pada rf_b_testing.
  - `composer validate --strict`, `composer check-platform-reqs`, PHP lint kedua script/test, dan git diff --check: lulus.
  - Hash .env/.env.dev/.env.staging/.env.testing dan package-lock.json tetap cocok baseline preservasi. Build frontend tidak diulang karena batch ini tidak mengubah frontend/dependensi; bukti build terakhir B2.5 tetap berlaku dalam batas perubahan tersebut.
- Batas/lanjutan: belum ada target/akses staging yang diterima pada sesi ini. Belum deploy, uji MariaDB dump/client, pemulihan staging/offsite terenkripsi, retensi/RTO/RPO nyata, CI remote atau integrasi A. Key/env aktual tidak dicadangkan oleh drill. Rilis tetap menunggu P0 A0/B0, advisory dependency dan verifikasi hasil merge. Detail hasil tersimpan pada BUKTI_PEMULIHAN_B26.md.

## Integrasi Jalur B ke develop bersama Jalur A — 2026-10-17

- Atas instruksi pengguna, target development dipetakan ke branch develop yang memang tersedia. Fetch menemukan origin/develop c411747 sudah berisi A0/A1/A2, termasuk Excel. Commit B a31cecf menyimpan B0–B2.6; perubahan asli pengguna package-lock.json (23 baris dihapus) dikecualikan dan tetap sebagai working change.
- origin/develop digabung ke B terlebih dahulu. Blok route menyatu otomatis dan tetap memuat alumni/materi A serta dashboard/chart/publik/konten/laporan B. Satu konflik composer.lock hanya metadata hash/platform; seluruh paket/versi Excel A dipertahankan bersama PHP ^8.3 dan config.platform.php 8.3.0 B. Tidak ada paket lama diperbarui; lima paket Excel dipasang mengikuti lockfile.
- Review integrasi menemukan dashboard masih mengizinkan managerial pending/suspended membaca statistik. Test baru gagal menerima 200, kemudian controller memeriksa gate view-alumni sebelum query dan test lulus pada ketiga role pengelola serta kedua status. Redirect alumni ke profil dipertahankan. CI menambahkan gd/zip eksplisit untuk Excel.
- Verifikasi snapshot gabungan PHP 8.3.33/MySQL 8.4.3 pada rf_b_testing: **239 test lulus, 1686 assertion, tanpa skip**. Termasuk test A untuk seeder, scope wilayah, audit, registrasi, materi, CSV/Excel dan gate konten, serta seluruh test B.
- `npm.cmd run build`: lulus 162 modul. Composer install mengikuti lockfile, optimized autoload 6888 kelas, package discovery, composer validate --strict, check-platform-reqs dan route:list lulus. Network filter advisory Composer tidak diperbarui dalam pemeriksaan offline; ini bukan bukti advisory tertutup.
- Drill migration/dump/restore pada dua database acak baru juga lulus: 22 migration, relasi role, metadata laporan, 3 file/checksum/dekripsi/cleanup PASS. Database kerja dan environment nyata tidak digunakan untuk drill/test. Direktori SQL sementara tidak masuk Git.
- PLAN/PRD/TASK/AUDIT/SOP disinkronkan oleh penggabung sesuai pengecualian aturan paralel. Identitas commit memakai Codex <codex@localhost> per perintah karena mesin belum memiliki user Git; konfigurasi global tidak diubah.
- Integrasi hanya lokal; tidak push, tidak deploy dan tidak menjalankan CI remote. Branch dev-B dipertahankan. Staging belum tersedia, advisory/penilaian kunci historis serta QA browser A/email nyata tetap tindak lanjut sebelum rilis.

## DOC-SYNC — Sinkronisasi dokumentasi setelah integrasi

- Status: DONE (2026-10-17), atas instruksi pengguna pada develop.
- Scope: README dan dokumen pusat; pengguna mengonfirmasi agenda, tim/kontak dan navigasi publik tetap akan dibuat, serta menambahkan artikel publik. Dicatat sebagai PUB-01–PUB-04; tidak mengimplementasikan UI atau mengubah pembagian kepemilikan kode.
- File: README.md, docs/aturan_pengembangan/{PRD,PLAN,TASK,AUDIT,SOP}.md dan catatan ini.
- Hasil: matriks cakupan PRD/README, tindak lanjut PLAN, task publik TODO, status materi/laporan, konteks riwayat audit dan aturan database test SOP diselaraskan. B0–B2 dan integrasi tetap REVIEW; tidak menetapkan fitur publik atau rilis selesai.
- Verifikasi: 56 pemeriksaan ID task, cakupan artikel, tautan lokal/anchor, referensi seeder dan status task lulus; git diff --check lulus. Pemeriksaan kode sumber membedakan route/view aktif dari template yang belum dipasang. Test aplikasi/build tidak dijalankan ulang untuk perubahan dokumentasi.

## Tahap berikutnya

### Arahan Berikutnya untuk Pemilik Jalur B

- Pembaruan integrasi A3–A5 oleh penggabung 2026-10-17: A3-VERIFY/A4-AUTH/A5-FIX sudah masuk develop pada ecee5b4; jangan menganggap label TODO A3 historis di bawah sebagai pekerjaan ulang. Suite PHP 8.3/MySQL terbaru 260 test/1921 assertion, build/Composer/platform/80 route dan QA browser lokal impor/batas wilayah lulus; rincian pada CATATAN_JALUR_A bagian A35-INTEGRATE. Prioritas B tetap PUB-04 -> PUB-01 -> PUB-02 -> PUB-03 dan B3-RELEASE. Staging/SMTP organisasi belum tersedia; B3, CI remote dan email eksternal tetap perlu bukti sebelum rilis.

- Keputusan pengguna 2026-10-17: arah lanjutan disetujui dan perlu diketahui pemilik B. Acuan aktif: [PLAN](PLAN.md#tindak-lanjut-setelah-integrasi) dan [TASK](TASK.md#ringkasan-status-saat-ini). Arahan ini menggantikan urutan mulai B0 historis untuk pekerjaan setelah integrasi; B0–B2 tetap REVIEW sesuai bukti sebelumnya.
- **Task fitur pertama: PUB-04 (TODO), artikel publik.** Tentukan pembeda artikel/refleksi dan aturan penerbitan admin, evaluasi reuse Post existing, lalu buat pengelolaan serta daftar/detail publik. Pertahankan refleksi/URL existing dan uji konten belum terbit tidak dapat diakses lewat daftar maupun URL detail.
- Berikutnya **PUB-01 agenda -> PUB-02 tim/kontak -> PUB-03 navigasi/Daftar final**. Gunakan informasi resmi, empty state, tampilan mobile dan akses keyboard. Koordinasikan perubahan view auth dengan A; jangan memperluas menjadi kalender interaktif, pendaftaran acara atau formulir kontak.
- **B3-RELEASE (TODO)** dapat berjalan paralel pada pekerjaan independen: advisory Composer, inventarisasi kunci historis, CI remote dan staging/backup. Audit npm tetap dilewati. Jalur A memiliki **A3-VERIFY (TODO/P0)**; B tidak mengedit controller/request/gate verifikasi milik A. Integrasi/rilis wajib memverifikasi hasil A.
- Awal sesi B: baca AGENTS/PRD/PLAN/TASK/AUDIT/SOP, cek branch/status, gunakan dev-B dari baseline develop gabungan yang sudah tersimpan; jangan membawa perubahan develop yang belum di-commit. Catat task IN_PROGRESS di file ini sebelum implementasi, verifikasi per batch, lalu REVIEW/DONE sesuai bukti. Jangan mengulang A0–A2 atau menimpa lockfile gabungan.
- Status serah terima **NEXT-PLAN: DONE** untuk penyusunan arahan; AGENTS.md mengarahkan sesi B ke PUB-04. File: AGENTS.md, PLAN.md, TASK.md dan catatan ini. Konsistensi ID/urutan/tautan lokal serta git diff --check lulus. Perubahan hanya dokumentasi; test aplikasi/build tidak diulang. Belum dikirim ke remote atau dikonfirmasi dibaca rekan pengembang.

- B0.2 masuk REVIEW; tunggu verifikasi CI remote saat perubahan diajukan.
- B0.3 masuk REVIEW; runner lokal lulus, tracking environment rahasia dihentikan.
- Sebelum rilis: jalankan CI remote, triase advisory dependency, dan inventarisasi pemakaian kunci server sebelum menetapkan rotasi.
- B1.1–B1.4 masuk REVIEW; hasil lokal B1 lulus, verifikasi gabungan/CI/browser tetap diperlukan sebelum rilis.
- B2.1–B2.6 dan integrasi lokal A+B masuk REVIEW. Tahap berikutnya: push/CI remote bila diminta serta penerapan runbook/drill pada staging setelah lingkungannya tersedia; jangan menyatakan siap rilis sebelum bukti lingkungan nyata dan risiko tersisa ditutup.
