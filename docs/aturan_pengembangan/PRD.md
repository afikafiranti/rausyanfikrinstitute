# PRD Website Alumni dan Kajian Rausyan Fikr

## Pembaruan aktif 2026-10-18

Pengguna mengizinkan push dan penggabungan Jalur B ke `develop`. PUB-04/PUB-03, modernisasi UIUX-02, dashboard pribadi alumni dan Refleksi saya bagi empat role sudah tersedia pada hasil gabungan. Commit implementasi `7408828` sudah dikirim ke dev-B; bukti verifikasi dan pengiriman hasil integrasi mengikuti CATATAN_JALUR_B. Staging dijadwalkan pada tahap akhir pengembangan sesuai arahan pengguna, bukan prasyarat merge saat ini. SMTP eksternal dan kesiapan produksi belum dinyatakan lulus. Pernyataan belum terintegrasi/belum diterapkan pada database kerja dalam snapshot lama di bawah dibaca sebagai riwayat.

Refleksi saya berbeda dari Refleksi publik: semua role dapat menyimpan draf pribadi; hanya penulis dapat membaca/mengedit/menghapus draf. Pengiriman opsional memerlukan akun aktif dan wilayah; isi terkunci setelah dikirim. Koorda wilayah kiriman serta admin/super admin aktif dapat membaca dan menanggapi, tanpa menanggapi kiriman sendiri. Tidak ada penerbitan publik otomatis. Laporan kajian hanya untuk pengelola aktif sesuai role/wilayah; alumni biasa ditolak pada menu maupun endpoint.

## 1. Ringkasan Produk

Riwayat integrasi 2026-10-17: A0–A5 dan B0/B1/B2 digabung pada develop; A3-VERIFY, A4-AUTH dan A5-FIX masuk melalui ecee5b4. Suite saat itu lulus 260 test/1921 assertion dan CI remote snapshot 13da2b5 lulus pada PHP 8.3/MariaDB 11.4. Bukti A3–A5 ada pada CATATAN_JALUR_A.md dan catatan integrasi Jalur B. Tindak lanjut operasional/produksi tetap terpisah. Status terkini PUB-04/PUB-03, dashboard dan refleksi mengikuti pembaruan aktif 2026-10-18 di atas.

Website Rausyan Fikr adalah aplikasi internal berbasis Laravel untuk mengelola data alumni kajian, proses verifikasi akun, akses materi kajian berdasarkan level, laporan kajian wilayah, serta konten publik seperti landing page, refleksi, dan galeri.

Produk ini harus mengutamakan:
- Akurasi data alumni.
- Workflow admin dan koordinator daerah yang jelas.
- Pengalaman pengguna yang sederhana, cepat, dan tidak membingungkan.
- Pengembangan bertahap dengan dokumentasi teknis yang selalu sinkron.

### Cakupan dan Status Implementasi

Perubahan cakupan pengguna 2026-10-17: halaman publik aktif disederhanakan menjadi Hero, maksimal empat cuplikan Artikel, maksimal empat foto Galeri dan footer; navbar menghubungkan halaman yang tersedia. Artikel memiliki daftar/detail publik dan draf/terbit melalui pengelolaan Post yang sama dengan Refleksi publik. Seluruh pengelolaan Post dan Galeri hanya untuk super admin aktif. URL Refleksi publik existing tetap tersedia dan berbeda dari Refleksi saya. Agenda, tim/kontak, tentang/statistik serta CTA terpisah tidak masuk homepage. Implementasi PUB-04/PUB-03 telah diuji, migration lokal diterapkan, dan kode masuk hasil penggabungan develop pada 2026-10-18. Persetujuan konten resmi dan rilis tetap terpisah dari integrasi.

Matriks ini menjadi acuan cakupan fitur per 2026-10-17. Tersedia berarti implementasi ada, sebagian berarti baru memenuhi sebagian alur, direncanakan berarti akan dibuat, dan ditunda berarti pengembangan lanjut belum dijalankan. Status task DONE/REVIEW dan persetujuan rilis tetap mengikuti TASK.md serta bukti verifikasi, bukan hanya keberadaan fitur.

| Fitur | Status | Bukti dan batas saat ini |
|---|---|---|
| Akun, profil, verifikasi, data alumni dan audit | Tersedia | A0/A1/A3/A4/A5-FIX; registrasi pending, scope wilayah/role verifikasi, transport email gagal tertangani dan label profil aksesibel. SMTP lokal lulus; pengiriman eksternal masih perlu QA |
| Impor/ekspor alumni CSV dan Excel .xlsx | Tersedia | A2/A35; template/nama wilayah, laporan baris gagal, unggah XLSX nyata/duplikat/filter/ekspor lulus QA browser lokal. Staging belum diuji |
| Dashboard dan grafik pengelola | Tersedia | Data database dan scope wilayah; hitungan mencakup seluruh akun dalam scope |
| Dashboard pribadi alumni | Tersedia | Status akun, level/wilayah, profil dasar, akses materi sesuai kelayakan, Refleksi saya dan notifikasi sendiri |
| Refleksi saya semua role | Tersedia | Draf privat; kiriman opsional dan tanggapan pengelola sesuai wilayah, terpisah dari konten publik |
| Playlist/video, pencarian dan akses materi berdasarkan status/level | Tersedia | A2; pengelolaan admin dan tautan YouTube, metadata judul/durasi manual |
| Pengambilan/cache metadata YouTube otomatis | Ditunda | Belum ada integrasi otomatis; PLAN A2 menundanya |
| Progres menonton, opsional | Ditunda | Model/tabel video_progress tersedia; endpoint dan UI pengguna belum tersedia |
| Draf/edit/kirim/review/rekap/ekspor laporan | Tersedia | B2 REVIEW; foto privat dan CSV; rejected terkunci tanpa revisi/kirim ulang |
| Homepage Hero/Artikel/Galeri/Footer | Tersedia pada hasil gabungan | Maksimal empat artikel terbit dan empat foto Galeri; konten resmi tetap menunggu pengelola |
| Artikel publik | Tersedia pada hasil gabungan | Daftar/detail dan draf/terbit melalui Post; pengelolaan hanya super admin. Refleksi publik existing tetap tersedia |
| Agenda kajian publik | Di luar cakupan aktif | PUB-01 tidak dilanjutkan dalam homepage ringkas atas keputusan pengguna |
| Profil tim dan informasi kontak resmi | Di luar cakupan aktif | PUB-02 tidak dilanjutkan dalam homepage ringkas atas keputusan pengguna |
| Navigasi publik | Tersedia pada dev-B | PUB-03 REVIEW; Artikel/Galeri, Hero membaca artikel dan Masuk/Area anggota. CTA Daftar tambahan di luar cakupan aktif; route register tetap tersedia |
| CRUD web khusus master role/wilayah/level | Direncanakan | Tujuan pengelolaan super admin; rincian task/acceptance criteria belum ditetapkan. Penetapan level akun sudah tersedia |

## 2. Tujuan Produk

1. Menjadi pusat data alumni Rausyan Fikr yang dapat dicari, difilter, diverifikasi, dan dirawat.
2. Menyediakan akses materi kajian sesuai level alumni.
3. Menyediakan laporan kajian wilayah yang dapat direkap dan ditinjau.
4. Menyediakan homepage ringkas, artikel dan galeri publik serta mempertahankan akses Refleksi existing.
5. Memberikan dasar sistem yang siap dikembangkan secara konsisten oleh tim web profesional.

## 3. Pengguna dan Role

### Super Admin
- Mengakses seluruh data.
- Mengelola user, role, wilayah, level, konten, dan konfigurasi penting.
- Mengambil keputusan final jika ada konflik data.

Cakupan pengelolaan master melalui CRUD web khusus masih direncanakan sesuai matriks. Role/wilayah/level awal disediakan seeder; keberadaan data master tidak berarti modul pengelolaannya sudah tersedia.

### Admin
- Mengelola data alumni lintas wilayah sesuai kebijakan.
- Mengelola materi sesuai gate existing; pengelolaan Post/Artikel/Refleksi dan Galeri hanya untuk super admin.
- Melakukan verifikasi akun.
- Melihat dashboard dan laporan.

### Koorda
- Melihat dan meninjau data alumni di wilayahnya.
- Melakukan verifikasi akun untuk wilayahnya.
- Meninjau laporan kajian wilayah.

### Alumni
- Registrasi dan login.
- Melengkapi serta memperbarui profil.
- Mengakses materi kajian sesuai level.
- Menerima notifikasi status akun.

### Pengunjung Publik
- Mengakses homepage Hero/Artikel/Galeri/Footer, daftar/detail artikel, Refleksi existing dan Galeri.

## 4. Modul Produk

### Modul Landing Page
Status: PUB-04/PUB-03 tersedia pada hasil penggabungan develop 2026-10-18. Migration lokal Post sudah diterapkan; persetujuan konten resmi dan rilis tetap terpisah dari integrasi kode.

Fitur tersedia:
- Hero, Galeri, footer serta daftar/detail Refleksi existing menjadi dasar yang dipakai ulang.
- Artikel dan Galeri diambil dari database; homepage menampilkan maksimal empat item masing-masing, bukan data contoh.

Fitur batch PUB-04/PUB-03:
- PUB-04: jenis Artikel pada Post, draf/terbit, daftar/detail publik serta empat cuplikan di homepage; seluruh pengelolaan Post dan Galeri dibatasi pada super admin aktif.
- PUB-03 terkait: navigasi Artikel/Galeri, tautan membaca artikel dari Hero, serta tautan Masuk/Area anggota existing menuju halaman yang tersedia.
- PUB-01/PUB-02 agenda dan tim/kontak berada di luar cakupan aktif saat ini, bukan bagian batch homepage.

Batas cakupan: homepage hanya Hero/Artikel/Galeri/Footer. Tidak menambahkan kalender, agenda, tim/kontak, statistik/tentang atau CTA terpisah. Registrasi existing tetap tersedia tanpa perubahan kebijakan auth pada batch B ini.

Kriteria UX:
- Informasi utama langsung jelas.
- Tautan Artikel/Galeri dan Masuk/Area anggota mudah ditemukan.
- Layout responsif dan tidak terlalu padat di mobile.

### Modul Autentikasi
Status: tersedia.

Fitur:
- Login.
- Register.
- Reset password.
- Verifikasi email.
- Logout.

Kriteria:
- Error validasi harus tampil jelas.
- Setelah register publik, user masuk status pending.
- Setelah tambah alumni oleh admin, admin tidak boleh otomatis berubah login menjadi akun baru.

### Modul Data Alumni
Status: tersedia; A0/A1/A2 DONE pada hasil integrasi.

Fitur:
- Daftar alumni.
- Detail alumni.
- Filter dan pencarian.
- Sorting dan pagination.
- Tambah alumni oleh admin/role yang diizinkan.
- Edit/hapus data dan ubah level sesuai izin, serta impor/ekspor CSV dan Excel .xlsx.
- Relasi role, wilayah, dan level.

Kriteria:
- Alumni baru tersimpan ke database.
- Alumni baru mendapat role alumni.
- Data baru terlihat di daftar sesuai filter aktif.
- Error validasi terlihat jelas.
- Admin tetap berada pada sesi admin setelah menambah alumni.

### Modul Profil Alumni
Status: tersedia.

Fitur:
- View profil.
- Edit profil.
- Update password.
- Upload foto profil.
- Perubahan wilayah/angkatan mengubah status menjadi pending.

Kriteria:
- Field penting tervalidasi.
- Perubahan kritikal tercatat di audit log.
- User mendapat notifikasi jika status kembali pending.

### Modul Verifikasi Akun
Status: tersedia.

Fitur:
- Daftar user pending.
- Approve user.
- Reject user.
- Set level awal.
- Tambah role alumni saat approve.
- Notifikasi status akun.
- Audit log approve/reject.

Kriteria:
- User tidak boleh memverifikasi akun sendiri.
- Koorda hanya boleh memverifikasi target non-pengelola di wilayahnya. Target dengan role koorda/admin/super_admin, termasuk role ganda, tidak muncul di antrean dan ditolak pada approve/reject.
- Admin dan super admin dapat memverifikasi akun lain lintas wilayah, termasuk akun super_admin pending agar pemulihan tetap tersedia; diri sendiri dan target non-pending tetap ditolak. Akun legacy tanpa role tetap dapat disetujui dan mendapat role alumni.

### Modul Dashboard
Status: tersedia pada hasil integrasi; B1 REVIEW untuk tindak lanjut rilis.

Dashboard statistik di bawah khusus admin/super admin/koorda aktif. Alumni mendapat dashboard pribadi: sapaan, status akun, level/wilayah, data profil dasar, akses materi sesuai status/level existing, Refleksi saya dan notifikasi miliknya sendiri. Akun tanpa role tetap diarahkan ke profil.

Fitur:
- Total alumni.
- Alumni aktif.
- Alumni baru bulan berjalan.
- Persentase verifikasi.
- Grafik pertumbuhan alumni.
- Sebaran angkatan.
- Sebaran wilayah.
- Pending verification count.

Kriteria:
- Angka dashboard konsisten dengan data tabel.
- Grafik tidak memakai dummy data untuk fitur produksi.

Definisi hitungan saat ini adalah seluruh akun dalam scope, termasuk pengelola; perubahan definisi menjadi role alumni saja memerlukan keputusan produk dan pembaruan test.

### Modul Manajemen Konten
Status: CRUD, pembeda jenis, draf/terbit dan pembatasan pengelola super admin tersedia melalui PUB-04.

Fitur:
- CRUD Artikel/Refleksi pada Post dan Galeri oleh super admin aktif saja.
- Pilihan draf/terbit memakai published_at; admin biasa, koorda dan alumni tidak dapat mengakses endpoint pengelolaan Post/Galeri.
- Upload gambar.

Kriteria:
- File lama terhapus saat diganti.
- Validasi gambar aman.
- Pesan sukses dan error tampil jelas.

Batas: Galeri tidak memiliki status publikasi terpisah; item yang disimpan pengelola ditampilkan publik. Form Post mendukung draf/terbit, tanpa fitur penjadwalan baru. Request legacy tanpa jenis/status tetap kompatibel; konten lama berjenis Refleksi.

### Modul Materi Kajian
Status: fitur inti tersedia; A2 DONE. Pengambilan metadata otomatis dan progres menonton ditunda.

Fitur tersedia:
- CRUD playlist/video oleh admin, pencarian dan detail playlist berisi daftar video.
- Akses materi bagi akun aktif berdasarkan level; level kosong ditolak.
- Judul/durasi video diisi manual; tautan playlist/video dibuka di YouTube.

Pekerjaan yang ditunda:
- Pengambilan/cache metadata YouTube otomatis.
- Progres menonton opsional; model/tabel belum terhubung endpoint/UI pengguna.

Inkonsistensi schema/controller pada baseline awal sudah ditangani dalam A0–A2. Pembatasan level aplikasi tidak mencegah tautan YouTube yang sudah diketahui dibagikan.

### Modul Laporan Kajian
Status: implementasi lokal tersedia; B2 REVIEW untuk tindak lanjut rilis.

Akses hanya untuk admin, super admin dan koorda aktif dengan scope wilayah existing. Alumni biasa menggunakan Refleksi saya dan tidak dapat mengakses endpoint Laporan; laporan historis tetap tersimpan.

Fitur tersedia:
- Draf, edit dan kirim laporan kajian.
- Data judul, pemateri, peserta, catatan, wilayah, tanggal, foto.
- Review koorda/admin.
- Approve/reject.
- Rekap sesuai filter wilayah/status/tanggal.
- Ekspor CSV dan penyajian foto privat melalui endpoint berizin.

Batas saat ini: laporan berstatus submitted/approved/rejected tidak dapat diedit atau dikirim ulang; alur revisi rejected belum tersedia. Rekap memuat laporan yang sudah dikirim, bukan draf; jumlah peserta adalah total kehadiran, bukan jumlah orang unik.

Kriteria:
- Laporan mengikuti scope wilayah.
- Status laporan terlihat jelas.
- Catatan reject wajib diisi.

## 5. Prinsip UX

1. Setiap aksi simpan harus menghasilkan feedback jelas: sukses, gagal validasi, atau error.
2. Filter aktif harus terlihat dan mudah di-reset.
3. Tabel data harus mudah discan: kolom utama, status badge, dan aksi jelas.
4. Form harus mempertahankan input lama saat validasi gagal.
5. Role dan permission tidak boleh membuat user terjebak tanpa penjelasan.
6. Halaman admin harus mengutamakan efisiensi, bukan tampilan marketing.
7. Landing page publik boleh lebih ekspresif, tetapi tetap cepat dan informatif.

## 6. Kebutuhan Non-Fungsional

- Framework: Laravel 12.
- Frontend: Blade, Tailwind, Vite.
- Database: MySQL/MariaDB.
- Auth: Laravel auth/Breeze style.
- Auditability: perubahan kritikal dicatat di audit log.
- Maintainability: perubahan fitur wajib memperbarui dokumen di `docs/aturan_pengembangan`.
- Security: validasi input, CSRF, authorization gate, scope wilayah.

## 7. Definisi Done

Sebuah fitur dianggap selesai jika:
- Route, controller, model, migration, dan view konsisten.
- Validasi sukses dan gagal diuji manual atau otomatis.
- Permission/role diuji minimal untuk admin dan non-admin terkait.
- UX error/success terlihat jelas.
- Dokumen PRD, PLAN, TASK, dan AUDIT diperbarui jika scope berubah.
- Tidak ada error baru di log Laravel untuk workflow yang disentuh.

