# PRD Website Alumni dan Kajian Rausyan Fikr

## 1. Ringkasan Produk

Website Rausyan Fikr adalah aplikasi internal berbasis Laravel untuk mengelola data alumni kajian, proses verifikasi akun, akses materi kajian berdasarkan level, laporan kajian wilayah, serta konten publik seperti landing page, refleksi, dan galeri.

Produk ini harus mengutamakan:
- Akurasi data alumni.
- Workflow admin dan koordinator daerah yang jelas.
- Pengalaman pengguna yang sederhana, cepat, dan tidak membingungkan.
- Pengembangan bertahap dengan dokumentasi teknis yang selalu sinkron.

## 2. Tujuan Produk

1. Menjadi pusat data alumni Rausyan Fikr yang dapat dicari, difilter, diverifikasi, dan dirawat.
2. Menyediakan akses materi kajian sesuai level alumni.
3. Menyediakan laporan kajian wilayah yang dapat direkap dan ditinjau.
4. Menyediakan halaman publik untuk profil organisasi, berita/refleksi, agenda, galeri, dan kontak.
5. Memberikan dasar sistem yang siap dikembangkan secara konsisten oleh tim web profesional.

## 3. Pengguna dan Role

### Super Admin
- Mengakses seluruh data.
- Mengelola user, role, wilayah, level, konten, dan konfigurasi penting.
- Mengambil keputusan final jika ada konflik data.

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
- Mengakses landing page, informasi organisasi, berita/refleksi, agenda, galeri, dan kontak.

## 4. Modul Produk

### Modul Landing Page
Status: sudah ada, perlu review UX dan konten.

Fitur:
- Homepage publik.
- Section organisasi, statistik, berita/refleksi, agenda, galeri, tim, kontak, CTA, footer.

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
Status: MVP aktif.

Fitur:
- Daftar alumni.
- Detail alumni.
- Filter dan pencarian.
- Sorting dan pagination.
- Tambah alumni oleh admin/role yang diizinkan.
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
- Koorda hanya boleh memverifikasi wilayahnya.
- Admin dan super admin dapat mengelola lintas wilayah sesuai policy.

### Modul Dashboard
Status: tersedia, perlu validasi data dan visual.

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

### Modul Manajemen Konten
Status: tersedia.

Fitur:
- CRUD refleksi/post.
- CRUD galeri.
- Upload gambar.

Kriteria:
- File lama terhapus saat diganti.
- Validasi gambar aman.
- Pesan sukses dan error tampil jelas.

### Modul Materi Kajian
Status: awal, perlu perapian.

Fitur target:
- Playlist YouTube berdasarkan level.
- Akses materi berdasarkan level alumni.
- Detail playlist/video.
- Metadata video.
- Progress menonton opsional.

Catatan teknis saat ini:
- Ada indikasi inkonsistensi field playlist antara controller dan migration.
- Perlu audit migration sebelum lanjut.

### Modul Laporan Kajian
Status: awal, perlu definisi ulang workflow.

Fitur target:
- Form laporan kajian.
- Data judul, pemateri, peserta, catatan, wilayah, tanggal, foto.
- Review koorda/admin.
- Approve/reject.
- Dashboard rekap.
- Export.

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

