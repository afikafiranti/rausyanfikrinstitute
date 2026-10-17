# Aturan Agen Pengembangan Rausyan Fikr

## Mulai Sesi dan Kenali Jalur

1. Jalankan `git branch --show-current` dan `git status --short` sebelum mengubah file. Pertahankan perubahan pengguna; jangan melakukan reset, clean, atau checkout paksa.
2. Baca dokumen berikut di `docs/aturan_pengembangan`: `PRD.md`, bagian **Rencana Aktif** pada `PLAN.md`, `TASK.md`, baseline audit terbaru pada `AUDIT.md`, dan `SOP.md`. Rencana Aktif menggantikan prioritas tahap historis di bawahnya.
3. Tentukan penugasan berdasarkan instruksi pengguna dan branch:

   | Konteks | Jalur | Urutan pekerjaan |
   |---|---|---|
   | Branch `dev-A` | A, milik pengguna yang memulai pembagian ini | Setelah A0–A2: A3-VERIFY, lalu QA A tersisa |
   | Branch `dev-B` | B, milik rekan pengembang | PUB-04 -> PUB-01 -> PUB-02 -> PUB-03; B3-RELEASE paralel bila independen |
   | Sesi baru tanpa penugasan, di luar `dev-A` | Default Jalur B | Baca arahan terbaru CATATAN_JALUR_B, pastikan branch kerja B, lalu mulai PUB-04 |

4. Sebutkan jalur dan task aktif pada update awal. Jika pengguna memberi penugasan eksplisit yang berbeda, ikuti penugasan tersebut dan selaraskan branch secara aman. Jangan menganggap teman harus melanjutkan Jalur A.
5. Branch kerja B adalah `dev-B`. Buat dari commit baseline bersama di `develop` yang sudah berisi aturan dan rencana ini, bukan dari perubahan implementasi A. Jangan berpindah branch dengan membawa perubahan jalur lain yang belum di-commit; tetap lakukan analisis yang aman sambil menjelaskan kondisi branch.

Arahan setelah integrasi disetujui pengguna pada 2026-10-17: [PLAN — Tindak Lanjut](docs/aturan_pengembangan/PLAN.md#tindak-lanjut-setelah-integrasi) dan [serah terima Jalur B](docs/aturan_pengembangan/CATATAN_JALUR_B.md#arahan-berikutnya-untuk-pemilik-jalur-b). Daftar A0–A2/B0–B2 di bawah adalah cakupan awal, bukan perintah mengulang pekerjaan selesai. A3-VERIFY menutup batas role verifikasi; B memulai artikel publik dan menyiapkan bukti rilis. Rilis tetap menunggu penutupan risiko dan pengujian gabungan.

## Tugas Jalur A

- **A0 / P0:** batasi wilayah koorda pada daftar/detail/tindakan, perketat izin konten, tutup akses materi tanpa status/level yang memenuhi syarat, dan amankan seeder akun dari reset password/profil nyata.
- **A1 / P1:** registrasi/role/wilayah, profil/alumni tervalidasi, sorting, audit atomik, dan notifikasi yang sesuai hasil tindakan.
- **A2 / P2:** pengelolaan dan penggunaan materi serta impor/ekspor alumni.
- Pemilik controller/request auth, alumni, profile, verification, materi, notification; model User/Role/Level/Wilayah/AuditLog/Playlist/Video/VideoProgress; gate/provider aplikasi, ScopeWilayah, dan `bootstrap/app.php`.
- Pemilik `routes/auth.php`, view/test domain A, User/Role/Level/WilayahSeeder dan DatabaseSeeder. Detail batas file mengikuti tabel kepemilikan dalam PLAN.

## Tugas Jalur B

- **B0 / P0:** samakan target PHP 8.3, MySQL/MariaDB dan CI; pisahkan database test; amankan konfigurasi lingkungan; lindungi dashboard dengan auth.
- **B1 / P1:** angka dashboard nyata dan terscope, grafik yang digunakan halaman aktif, upload post/galeri aman, slug unik, resource route yang valid, dan homepage terhubung konten terbit.
- **B2 / P2:** laporan kajian tersimpan, review sesuai wilayah, rekap/export, QA publik, serta kesiapan staging/rilis. Aksi laporan yang belum berfungsi harus disembunyikan atau diberi status belum tersedia.
- Pemilik Dashboard/Chart/Landing/Report/Management controller dan request barunya; model Post/Gallery/KajianReport; view/test domain B; Post/GallerySeeder.
- Pemilik toolchain: Composer/npm beserta lockfile, CI, phpunit.xml, run-ci.sh, Vite/Tailwind, template environment, dan .gitignore. Pemilik layout/komponen/navigasi bersama, CSS, dan JavaScript.
- Gunakan method role dan schema yang sudah tersedia. Untuk laporan, gunakan kelas ScopeWilayah langsung jika alias belum tersedia. Jangan menunggu helper atau migration baru dari A untuk mulai B0/B1.
- Gate manage-content dimiliki A0. Test bisnis konten B dapat dikerjakan dengan fixture admin existing; batas role endpoint wajib diuji kembali pada hasil merge.

## Hindari Tabrakan dan Jaga Kontrak

- `routes/web.php` dibagi per blok: A memiliki alumni/profil/verifikasi/materi/notifikasi; B memiliki homepage/dashboard/charts/laporan/management. Jangan memformat ulang atau memindahkan seluruh blok. Route baru memakai nama kelas lengkap agar blok import tidak diperebutkan.
- Jangan mengubah file milik jalur lain atau membatalkan edit pengembang lain. Catat kebutuhan lintas jalur sebagai catatan integrasi dan lanjutkan pekerjaan mandiri yang tersedia.
- Pertahankan nama route, parameter, signature method publik, dan field database existing. Migration baru terpisah menurut domain dengan nama/timestamp unik; jangan mengedit migration yang sudah dijalankan.
- Role tetap `super_admin`, `admin`, `koorda`, `alumni`. Status akun tetap `pending`, `active`, `suspended`; penolakan akun tetap pending dengan alasan tercatat. Status laporan tetap `draft`, `submitted`, `approved`, `rejected`. Role akun berbeda dari level materi.
- Target sintaks dan runtime bersama adalah **PHP 8.3**. Pengembang lokal PHP 8.4 tetap menulis kode kompatibel 8.3. Instalasi dependensi mengikuti lockfile; jalur A tidak mengubah konfigurasi/lockfile milik B.
- Setiap orang memakai database lokal dan database test sendiri. Jangan menjalankan RefreshDatabase/migrate:fresh pada database kerja bersama atau produksi, dan jangan mencatat rahasia ke Git/log.

## Catatan Pekerjaan dan Verifikasi

- Selama implementasi paralel, dokumen pusat PLAN/PRD/TASK/AUDIT/SOP serta AGENTS ini tidak diedit kedua jalur. Pengecualian: pengguna secara eksplisit meminta pembaruan aturan/rencana, atau penggabung sedang menyinkronkan hasil merge.
- A membuat/memperbarui `docs/aturan_pengembangan/CATATAN_JALUR_A.md`; B membuat/memperbarui `docs/aturan_pengembangan/CATATAN_JALUR_B.md`. Catat ID task, prioritas, status, perubahan file, risiko, serta perintah dan hasil verifikasi. Tandai IN_PROGRESS sebelum implementasi, lalu REVIEW/DONE sesuai bukti.
- Kerjakan perubahan terkecil yang menyelesaikan masalah. Pakai pola/dependensi existing dan tambahkan regression test untuk logika berisiko. Jangan menambah abstraksi atau fitur di luar task.
- Gunakan tier eksekusi terendah yang dapat menyelesaikan tugas dengan andal. Perubahan dokumentasi/isolated edit ditangani langsung; delegasi, eskalasi, dan review mengikuti kompleksitas/risiko serta instruksi pengguna yang berlaku.
- Jalankan test domain sendiri. Jangan menyebut test/build lulus bila belum dijalankan atau terhalang dependensi. Lint lokal PHP 8.4 tidak membuktikan runtime PHP 8.3.
- Ajukan perubahan per batch A0/A1/A2 atau B0/B1/B2 agar perbaikan P0 tidak menunggu fitur P2. Urutan integrasi yang disarankan B0 lalu A0 tidak membatasi waktu mulai coding masing-masing jalur.
- Penggabung memeriksa blok route, gate vs endpoint, migration, dan lockfile; kemudian menjalankan validasi Composer/platform, route:list, migration pada database uji, seluruh test, dan build frontend pada PHP 8.3. Sinkronkan dokumen pusat setelah merge; semua P0 harus tertutup sebelum rilis.
