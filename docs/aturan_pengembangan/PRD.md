# PRD Website Alumni dan Kajian Rausyan Fikr

## 1. Ringkasan Produk

Status implementasi 2026-10-17: A0–A5 dan B0/B1/B2 sudah digabung pada develop; A3-VERIFY, A4-AUTH dan A5-FIX masuk melalui ecee5b4. Suite gabungan terbaru PHP 8.3/MySQL lulus 260 test/1921 assertion; bukti A3–A5 ada pada CATATAN_JALUR_A.md, integrasi sebelumnya pada CATATAN_JALUR_B.md. A0–A4 dan A5-FIX DONE secara lokal; A5-QA, B0–B2 dan rilis tetap REVIEW untuk tindak lanjut operasional. Staging dan SMTP organisasi belum tersedia; CI remote/advisory dependency tetap perlu bukti. Agenda, tim, kontak, artikel dan pelengkapan publik tetap akan dibuat oleh B.

Website Rausyan Fikr adalah aplikasi internal berbasis Laravel untuk mengelola data alumni kajian, proses verifikasi akun, akses materi kajian berdasarkan level, laporan kajian wilayah, serta konten publik seperti landing page, refleksi, dan galeri.

Produk ini harus mengutamakan:
- Akurasi data alumni.
- Workflow admin dan koordinator daerah yang jelas.
- Pengalaman pengguna yang sederhana, cepat, dan tidak membingungkan.
- Pengembangan bertahap dengan dokumentasi teknis yang selalu sinkron.

### Cakupan dan Status Implementasi

Matriks ini menjadi acuan cakupan fitur per 2026-10-17. Tersedia berarti implementasi ada, sebagian berarti baru memenuhi sebagian alur, direncanakan berarti akan dibuat, dan ditunda berarti pengembangan lanjut belum dijalankan. Status task DONE/REVIEW dan persetujuan rilis tetap mengikuti TASK.md serta bukti verifikasi, bukan hanya keberadaan fitur.

| Fitur | Status | Bukti dan batas saat ini |
|---|---|---|
| Akun, profil, verifikasi, data alumni dan audit | Tersedia | A0/A1/A3/A4/A5-FIX; registrasi pending, scope wilayah/role verifikasi, transport email gagal tertangani dan label profil aksesibel. SMTP lokal lulus; pengiriman eksternal masih perlu QA |
| Impor/ekspor alumni CSV dan Excel .xlsx | Tersedia | A2/A35; template/nama wilayah, laporan baris gagal, unggah XLSX nyata/duplikat/filter/ekspor lulus QA browser lokal. Staging belum diuji |
| Dashboard dan grafik alumni | Tersedia | B1 REVIEW; data database dan scope wilayah. Hitungan saat ini mencakup seluruh akun dalam scope |
| Playlist/video, pencarian dan akses materi berdasarkan status/level | Tersedia | A2; pengelolaan admin dan tautan YouTube, metadata judul/durasi manual |
| Pengambilan/cache metadata YouTube otomatis | Ditunda | Belum ada integrasi otomatis; PLAN A2 menundanya |
| Progres menonton, opsional | Ditunda | Model/tabel video_progress tersedia; endpoint dan UI pengguna belum tersedia |
| Draf/edit/kirim/review/rekap/ekspor laporan | Tersedia | B2 REVIEW; foto privat dan CSV; rejected terkunci tanpa revisi/kirim ulang |
| Homepage, tentang, statistik publikasi, refleksi dan galeri | Tersedia | B1 REVIEW; post terbit dan galeri dari database |
| Artikel publik | Direncanakan | Halaman daftar/detail artikel belum tersedia; PUB-04. Refleksi existing tetap tersedia |
| Agenda kajian publik | Sebagian | Placeholder belum tersedia; akan dilengkapi melalui PUB-01 |
| Profil tim dan informasi kontak resmi | Direncanakan | Template belum dipasang pada homepage aktif; PUB-02 |
| Navigasi/CTA pendaftaran publik dan konten final | Sebagian | Route registrasi tersedia, tetapi tautan Daftar belum ada di homepage/login aktif; PUB-03 |
| CRUD web khusus master role/wilayah/level | Direncanakan | Tujuan pengelolaan super admin; rincian task/acceptance criteria belum ditetapkan. Penetapan level akun sudah tersedia |

## 2. Tujuan Produk

1. Menjadi pusat data alumni Rausyan Fikr yang dapat dicari, difilter, diverifikasi, dan dirawat.
2. Menyediakan akses materi kajian sesuai level alumni.
3. Menyediakan laporan kajian wilayah yang dapat direkap dan ditinjau.
4. Menyediakan halaman publik untuk profil organisasi, berita/refleksi, artikel, agenda, tim, galeri, dan kontak.
5. Memberikan dasar sistem yang siap dikembangkan secara konsisten oleh tim web profesional.

## 3. Pengguna dan Role

### Super Admin
- Mengakses seluruh data.
- Mengelola user, role, wilayah, level, konten, dan konfigurasi penting.
- Mengambil keputusan final jika ada konflik data.

Cakupan pengelolaan master melalui CRUD web khusus masih direncanakan sesuai matriks. Role/wilayah/level awal disediakan seeder; keberadaan data master tidak berarti modul pengelolaannya sudah tersedia.

### Admin
- Mengelola data alumni lintas wilayah sesuai kebijakan.
- Mengelola konten refleksi dan galeri.
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
- Mengakses landing page, informasi organisasi, berita/refleksi, artikel, agenda, tim, galeri, dan kontak.

## 4. Modul Produk

### Modul Landing Page
Status: sebagian tersedia; pelengkapan publik dikonfirmasi pengguna pada 2026-10-17 dan tercatat sebagai PUB-01–PUB-04.

Fitur tersedia:
- Homepage, tentang, statistik jumlah refleksi terbit/foto galeri, daftar/detail refleksi, galeri, CTA masuk dan footer.
- Post yang terbit dan galeri diambil dari database.

Fitur yang akan dibuat:
- PUB-01: agenda kajian dengan informasi nyata dan empty state saat belum ada agenda.
- PUB-02: profil tim dan informasi kontak resmi yang diverifikasi pengelola, menggantikan template yang belum aktif.
- PUB-03: pelengkapan navigasi, CTA Daftar menuju registrasi existing dan konten publik final.
- PUB-04: daftar dan detail artikel publik, berikut tautan navigasi. Artikel dicatat sebagai kebutuhan tersendiri dari refleksi existing; penggunaan model Post bersama ditetapkan saat implementasi.

Batas saat ini: agenda masih placeholder; tim/kontak tidak di-include oleh homepage aktif; tautan Daftar belum ada di homepage/login. Jenis pengelolaan konten agenda/tim/kontak dan target rilis ditetapkan saat implementasi; tidak mengasumsikan kalender, pendaftaran acara atau formulir kontak baru.

Kriteria UX:
- Informasi utama langsung jelas.
- CTA pendaftaran/login mudah ditemukan.
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
Status: tersedia pada hasil integrasi; B1 REVIEW untuk tindak lanjut rilis.

Fitur:
- CRUD refleksi/post.
- CRUD galeri.
- Upload gambar.

Kriteria:
- File lama terhapus saat diganti.
- Validasi gambar aman.
- Pesan sukses dan error tampil jelas.

Batas saat ini: post baru langsung terbit dan galeri tidak memiliki status publikasi terpisah. Alur editorial draf/penjadwalan belum ditetapkan sebagai fitur tambahan.

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

