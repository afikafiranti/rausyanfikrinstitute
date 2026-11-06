# PRD Website Alumni dan Kajian Rausyan Fikr

Acuan kontrak produk per 6 November 2026. Dokumen ini menjelaskan perilaku pengguna dan batas fitur; progres serta hasil uji dicatat pada [TASK](TASK.md#register-rfix).

## 1. Ringkasan Produk

Website komunitas untuk data alumni, verifikasi akun, materi kajian berdasarkan level, laporan wilayah, serta tulisan dan galeri publik. Pengalaman penggunaan harus jelas bagi pengguna nonteknis dan pengolahan data harus akurat.

### Cakupan dan Status Implementasi

| Area | Cakupan produk |
|---|---|
| Akun, alumni, profil dan verifikasi | Tersedia; kontrak pada modul terkait di bawah |
| Materi, dashboard dan laporan | Tersedia; akses mengikuti role/status/level/wilayah |
| Tulisan publik dan Galeri | Tersedia; pengelolaan pada menu Artikel dan Galeri |
| Refleksi saya/Tinjau refleksi | Dihapus; bukan backlog atau sumber publikasi otomatis |
| Progres menonton dan metadata YouTube otomatis | Ditunda; struktur progres yang tidak digunakan sudah dikeluarkan |
| Revisi/kirim ulang laporan rejected | Belum tersedia |
| CRUD web khusus master role/wilayah/level | Direncanakan, belum memiliki spesifikasi pelaksanaan; penetapan level akun sudah tersedia |
| Agenda, tim/kontak, section tentang/statistik dan CTA tambahan | Di luar cakupan homepage aktif |

Keputusan perubahan kebutuhan ada pada [ARAH/KEP di dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md#1-keputusan-yang-menjadi-dasar-pekerjaan). Ketersediaan fitur tidak menyatakan kesiapan rilis.

## 2. Tujuan Produk

Menjaga data alumni yang dapat dirawat, menyediakan materi sesuai kelayakan, memungkinkan pengelola mencatat/meninjau kegiatan wilayah, dan menyediakan konten resmi untuk pengunjung.

## 3. Pengguna dan Role

Satu akun mendukung beberapa role. Role menentukan kewenangan; level menentukan kelayakan materi. Izin pengelolaan membutuhkan status active. Akun pending/suspended tetap dapat menggunakan profilnya sendiri sesuai autentikasi.

| Role | Kewenangan utama |
|---|---|
| super_admin | Pengelolaan lintas wilayah serta penerbitan Post dan Galeri |
| admin | Pengelolaan alumni, verifikasi, materi dan laporan lintas wilayah; tidak menerbitkan Post/Galeri |
| koorda | Pengelolaan dan verifikasi anggota serta laporan sesuai wilayahnya |
| alumni | Profil sendiri, dashboard pribadi, notifikasi sendiri dan materi sesuai kelayakan; tidak mengakses laporan |
| Pengunjung tanpa akun | Membaca konten publik yang tersedia |

Status akun adalah pending, active, suspended. Penolakan verifikasi mempertahankan pending dengan alasan tercatat. Ketentuan khusus setiap tindakan ada pada modul berikut; role ganda tidak boleh menjadi jalan melewati pembatasan target atau self-review.

## 4. Modul Produk

### Modul Landing Page

- Homepage berurutan Hero, maksimal empat cuplikan Artikel terbit, maksimal empat foto Galeri, lalu Footer; seluruh daftar berasal dari database dan mempunyai kondisi kosong.
- Navigasi publik menghubungkan Artikel/Galeri dan Masuk/Area anggota. URL daftar/detail Refleksi publik tetap tersedia walau belum menjadi tautan menu utama/footer.
- Artikel membahas suatu topik; Refleksi publik membagikan pengalaman/renungan untuk umum. Perbedaan keduanya editorial, tanpa kriteria isi otomatis yang berbeda.

### Modul Autentikasi

- Registrasi, login/logout, reset password dan verifikasi email tersedia. Verifikasi email belum menjadi syarat umum seluruh modul.
- Registrasi publik membuat akun pending dengan role alumni; role, status dan level tidak dapat ditentukan melalui payload publik.
- Menambah alumni melalui pengelola tidak mengganti sesi pengelola menjadi akun baru.
- Kegagalan pengiriman email ditangani tanpa menyatakan pesan terkirim; bukti transport lingkungan tujuan mengikuti TASK.

### Modul Data Alumni

- Daftar/detail, pencarian, filter, sorting, pagination, tambah/edit/hapus akun dan penetapan level mengikuti izin serta wilayah aktor.
- Impor CSV UTF-8 dan XLSX menerima header terdefinisi, maksimal 200 baris data dan berkas 1 MB. XLS lama dan rumus Excel tidak didukung; nomor telepon harus tetap menjadi teks.
- Nama wilayah dicocokkan dengan data terdaftar. Nama ambigu/tidak dikenal atau wilayah di luar izin dilaporkan gagal. Template legacy dengan wilayah_id tetap diterima.
- Impor hanya menambah akun pending dengan role alumni, tanpa level/verifikasi email; password awal tidak dibagikan dan akun menyetelnya melalui lupa password. Email/telepon unik serta kegagalan per baris dijelaskan.
- Ekspor mengikuti seluruh filter dan scope, bukan halaman pagination. XLSX maksimal 5.000 hasil; kelebihan data ditolak dengan pilihan mempersempit filter atau memakai CSV streaming. Nilai teks, Unicode, nol telepon di depan dan perlindungan formula dipertahankan.

### Modul Profil Alumni

- Pengguna membaca/mengubah profil, foto dan password sendiri; validasi menjaga panjang/format/keunikan data.
- Perubahan wilayah/angkatan memerlukan verifikasi kembali dan mengubah akun yang memenuhi syarat menjadi pending; suspended tetap suspended.
- Tindakan yang menghilangkan pengelola global aktif terakhir ditolak. Bila sudah ada super admin aktif, sekurangnya satu tetap tersedia; multi-role dihitung per akun.
- Akun penulis atau penanggap laporan kajian tidak dapat dihapus agar laporan dan atribusinya tetap utuh. Penghapusan akun lain tetap mencatat audit dan membersihkan avatar lokal milik aplikasi setelah berhasil.
- Perubahan kritis dicatat di audit; pesan dan notifikasi mengikuti status nyata.

### Modul Verifikasi Akun

- Pengelola melihat akun pending, menyetujui/menolak, dan menetapkan level awal yang sah. Hasil tindakan dicatat serta diberitahukan kepada akun terkait.
- Aktor tidak dapat memverifikasi dirinya atau akun non-pending.
- Koorda hanya meninjau target non-pengelola dalam wilayahnya. Target dengan role koorda/admin/super_admin, termasuk role ganda, disembunyikan dari antrean koorda dan ditolak pada endpoint.
- Admin/super admin dapat meninjau akun lain lintas wilayah, termasuk super admin pending. Akun legacy tanpa role dapat disetujui dan memperoleh role alumni.

### Modul Dashboard

- Pengelola aktif mendapat statistik dalam scope-nya: total/aktif/baru, persentase verifikasi, pending, pertumbuhan bulanan, serta sebaran angkatan/wilayah dan kategori yang ditampilkan.
- Istilah hitungan alumni saat ini mencakup seluruh akun dalam scope, termasuk pengelola; jumlah kajian bulanan masih diberi keterangan belum tersedia.
- Alumni mempunyai dashboard pribadi berisi status, level/wilayah, data profil, akses materi yang memenuhi syarat dan notifikasi sendiri. Akun tanpa role diarahkan ke profil.
- Angka/grafik memakai data nyata, menyediakan tabel angka, serta penjelasan ketika data kosong atau gagal dimuat.

### Modul Manajemen Konten

- Menu dan istilah antarmuka pengelolaan memakai **Artikel** secara konsisten: Tambah artikel, Daftar artikel, Edit artikel, label, tombol, konfirmasi dan pesan hasil. Artikel dan Refleksi publik tetap dua jenis Post; daftar/detail publik membatasi jenis serta hanya menampilkan konten yang sudah terbit.
- Draf/terbit menggunakan published_at. Request legacy tanpa jenis/status tetap kompatibel dan konten lama berjenis Refleksi; penjadwalan publikasi baru tidak ditambahkan.
- Hanya super admin aktif mengelola Post dan Galeri. Galeri langsung publik setelah disimpan dan tidak mempunyai status draf.
- Unggah gambar divalidasi; penggantian/penghapusan konten membersihkan file lokal terkait dengan aman. Mengganti judul tulisan terbit mempertahankan URL yang sudah dibagikan.

### Modul Materi Kajian

- Pengelola yang berwenang mengelola playlist/video; pengguna dapat mencari dan membuka detail materi.
- Akses memerlukan akun active dengan level yang memenuhi syarat; level kosong ditolak. Level berbeda dari role akun.
- Judul/durasi diisi manual, sedangkan menonton membuka tautan YouTube. Pembatasan aplikasi tidak mencegah pembagian tautan YouTube yang sudah diketahui.

### Modul Laporan Kajian

- Hanya pengelola aktif sesuai kewenangan/wilayah dapat mengakses endpoint laporan; alumni ditolak, termasuk bila mempunyai laporan historis.
- Status laporan adalah draft, submitted, approved, rejected. Penulis mengubah/menghapus draf sendiri; identitas, wilayah dan total disahkan server saat menyimpan/mengirim.
- Review mengecualikan laporan sendiri, mematuhi scope, membutuhkan alasan penolakan, dan menolak keputusan berulang. Isi laporan submitted/approved/rejected terkunci.
- Rekap/ekspor CSV mencakup laporan yang sudah dikirim dalam filter wilayah/status/tanggal, bukan draf. Jumlah peserta adalah kehadiran, bukan jumlah orang unik; total harus konsisten dengan peserta laki-laki/perempuan.
- Foto berada pada storage privat dan hanya disajikan melalui endpoint berizin.

## 5. Prinsip UX

Setiap tindakan memberi hasil yang jelas. Filter dapat dikenali dan di-reset, input lama dipertahankan ketika validasi gagal, label/fokus/keyboard berfungsi, dan formulir tetap terbaca di layar kecil. Halaman pengelola mengutamakan efisiensi.

## 6. Kebutuhan Non-Fungsional

Validasi input, CSRF, autentikasi/gate, scope wilayah, audit dan integritas transaksi wajib dipertahankan. Pilih kode dan struktur yang mudah dirawat. Runtime, dependensi serta prosedur verifikasi mengikuti [SOP](SOP.md#1-lingkungan-dan-dependensi); keputusan penyimpanan mengikuti dokumen induk.

## 7. Definisi Done

Fitur memenuhi kontrak di atas serta kriteria RFIX terkait dan diverifikasi menurut [SOP](SOP.md#4-verifikasi). Hasil dicatat di TASK; kesiapan rilis membutuhkan bukti lingkungan tujuan.
