# Audit UI/UX dan Arah Visual — Rausyan Fikr

Tanggal: 2026-10-18. Task: UIUX-01. Baseline: `dev-B`, commit `6961e18`.

Audit ini menilai tampilan, keterbacaan, hierarki, navigasi, formulir, dan responsivitas. Belum menerapkan redesign, mengubah backend, menambah fitur, atau mengganti framework. Preferensi pengguna: **modern ekspresif, warna lebih berani dan komposisi dinamis**.

## Kesimpulan

Fondasi publik sudah sederhana dan mudah diikuti: Hero → empat Artikel → empat foto Galeri → Footer. Arah visual belum menyatu dengan area anggota dan autentikasi. Modernisasi paling berguna dimulai dengan membenahi tampilan mobile yang hilang, menyatukan identitas dan komponen, lalu membuat homepage lebih ekspresif melalui tipografi dan komposisi.

Ekspresi visual ditonjolkan pada halaman publik dan judul halaman. Area pengelolaan tetap lebih tenang agar formulir dan data mudah dipindai. Keduanya memakai warna, huruf, tombol, dan ikon dari keluarga yang sama.

## Temuan berprioritas

P1 berarti menghambat penggunaan atau keterbacaan; P2 berarti konsistensi/hierarki; P3 berarti penyempurnaan. Nomor adalah identitas temuan, bukan urutan implementasi yang sudah disetujui.

### 1. P1 — Isi dashboard tidak tampil pada mobile

`resources/views/dashboard/index.blade.php:8` membungkus seluruh isi dengan `hidden md:block` tanpa padanan mobile. Browser pada 390 px menunjukkan header/menu dan area kosong; desktop menampilkan statistik/grafik. Ini masalah tampilan aktual, bukan data yang kosong.

Rekomendasi: ubah susunan statistik dan grafik menjadi satu/dua kolom pada layar kecil; pertahankan data, izin, dan endpoint existing. Hindari sekadar menghapus `hidden` lalu memaksakan grid desktop ke ponsel.

### 2. P1 — Beberapa warna aksi terlalu pucat untuk teks kecil

Sumber Galeri memakai `text-blue-500` untuk Edit, `text-red-500` untuk Hapus, serta tombol Simpan **modal** `bg-emerald-500 text-white` di `resources/views/management/galeri/index.blade.php:66`, `:72`, dan `:116`. Warna Edit/Hapus dikonfirmasi dari computed style browser; pasangan tombol modal diperiksa dari source, bukan sampel modal yang dibuka. Pasangan warna solid ini dihitung:

| Pasangan | Rasio kontras |
|---|---:|
| `#3B82F6` pada putih | 3,68:1 |
| `#EF4444` pada putih | 3,76:1 |
| Putih pada `#10B981` | 2,54:1 |
| `#9CA3AF` pada putih, label “Tambah” | 2,54:1 |

Ketiganya yang dipakai untuk aksi perlu versi warna lebih gelap. Tombol **Tambah Galeri** yang terlihat memakai putih pada indigo `#4F46E5`, rasio 6,29:1, sehingga bukan contoh gagal di atas. Penghitungan ini berlaku untuk pasangan solid tersebut, bukan sertifikasi aksesibilitas seluruh halaman. Acuan teks biasa adalah minimal 4,5:1; teks besar memiliki pengecualian. [W3C: Contrast Minimum](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html).

### 3. P2 — Identitas publik, anggota, dan login terpisah

Publik memakai navy dan logo merah, shell anggota memiliki bidang merah dominan (`resources/views/layouts/app.blade.php:26`), sedangkan login menampilkan kotak brand kosong sebagai penanda (`resources/views/layouts/guest.blade.php:18`). Perpindahan halaman terasa seperti berpindah produk.

Rekomendasi: pertahankan logo resmi di semua shell, gunakan satu palet identitas, dan batasi bidang warna kuat pada bagian yang punya fungsi. Permukaan formulir/tabel sebaiknya terang; header merah yang besar tidak perlu menjadi latar seluruh pengalaman pengelolaan.

### 4. P2 — Hierarki tombol, bentuk, dan ukuran belum konsisten

`.rf-btn` di `resources/css/app.css:13`, tombol inline di manajemen, dan komponen `resources/views/components/primary-button.blade.php:1` memakai radius, warna, kapitalisasi, dan ukuran berbeda. Simpan utama, Batal, navigasi, dan Hapus belum selalu terbaca sebagai keluarga aksi yang konsisten.

Rekomendasi: tetapkan primary/secondary/text/destructive pada komponen Blade/helper yang sudah ada, beserta hover, focus, disabled, dan loading bila proses terkait memilikinya. Tombol penting ditargetkan tinggi 44–48 px untuk kenyamanan sentuh. Ini target desain; WCAG 2.2 AA menetapkan minimum 24×24 CSS px beserta pengecualian jarak dan konteks, bukan kewajiban universal 44 px. [W3C: Target Size Minimum](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html).

### 5. P2 — Form tambah mendahului daftar terlalu jauh pada mobile

Pada manajemen Post, formulir panjang berada sebelum daftar (`resources/views/management/refleksi/index.blade.php:10`). Pada viewport 390 px, hampir seluruh layar pertama berisi input, sehingga pengguna yang datang untuk mencari/mengedit tulisan perlu menggulir jauh.

Rekomendasi: tampilkan judul halaman, aksi Tambah, dan daftar sebagai orientasi awal; buka formulir existing melalui pengungkapan sederhana. Alternatif paling kecil adalah tautan lompat ke daftar/form dengan judul yang jelas. Pemilihan pola dilakukan saat desain diterapkan; tidak memerlukan endpoint baru.

Pada tabel Post mobile, lebar kontainer 326 px dan isi 416 px; tombol Edit mulai pada x=372, di luar batas kontainer yang terlihat. Gulir horizontal menahan overflow halaman, tetapi aksi tidak langsung ditemukan. Tampilkan informasi/aksi utama pada susunan mobile yang jelas atau beri petunjuk gulir; jangan menyembunyikan kolom/aksi penting demi tampilan ringkas.

### 6. P2 — Ritme homepage masih seragam untuk arah ekspresif

Hero memusatkan semua teks dan dua tombol (`resources/views/landingpage/sections/jumbotron.blade.php:1`), lalu Artikel dan Galeri memakai grid setara. Struktur sudah jelas, tetapi ukuran, bobot, dan ritme antarbagian belum menonjolkan karakter lembaga.

Rekomendasi: hero asimetris pada desktop, judul lebih tegas, kutipan existing sebagai elemen pendamping, serta satu artikel terbaru lebih menonjol dengan tiga cuplikan pendamping. Galeri memiliki ritme foto yang berbeda dari Artikel. Tetap maksimal empat item per bagian, tanpa section baru atau carousel wajib. Pada mobile, urutan baca tetap linear dan proporsi kembali sederhana.

### 7. P2 — Placeholder gambar pengelolaan mengarah ke aset yang tidak ada

Fallback Post/Galeri mengarah ke `assets/img/default-img.jpg` di `resources/views/management/refleksi/index.blade.php:98` dan `resources/views/management/galeri/index.blade.php:53`. File tujuan tidak ada; baris tanpa gambar memperlihatkan ikon gambar rusak pada screenshot pengelolaan.

Rekomendasi: gunakan keadaan “Tanpa gambar” yang rapi dengan dimensi stabil dan label, atau aset existing yang benar-benar tersedia. Artikel publik sudah menghilangkan cover kosong; jangan menyimpulkan seluruh gambar publik rusak dari temuan pengelolaan ini. Permintaan `/storage` yang diblokir khusus harness QA juga bukan bukti kerusakan produk.

### 8. P2/P3 — Tipografi dan bahasa belum memiliki aturan bersama

Layout aktif tidak menetapkan keluarga font khusus; konfigurasi Figtree lama dikomentari pada `tailwind.config.js`. Ini belum membuktikan font yang berbeda benar-benar dimuat, tetapi arahan huruf/hierarki belum menjadi kontrak bersama. Label kecil, kapital navigasi, judul, dan metadata diatur per halaman. “See all” di dashboard (`resources/views/dashboard/index.blade.php:93`) juga tersisa di antarmuka Indonesia.

Rekomendasi: satu keluarga sans untuk UI dan judul dengan bobot berbeda; kata merek tetap sesuai identitas resmi. Gunakan bahasa Indonesia secara konsisten untuk aksi. Halaman daftar artikel juga mengulang tautan “Semua artikel” ke halaman yang sama; hapus pengulangan navigasi ini pada konteks daftar tanpa menghilangkan tautannya dari homepage. Jangan mengubah kutipan/isi resmi hanya untuk mengejar gaya.

## Arah modern ekspresif yang direkomendasikan

### Warna

Palet ini adalah usulan, belum mengganti warna produksi. Merah mempertahankan hubungan dengan logo; cobalt membedakan aksi utama dari aksi destruktif; navy memberi kedalaman; putih hangat memberi ruang baca.

| Peran | Warna usulan | Pemakaian |
|---|---|---|
| Tinta/struktur | `#14213D` | Teks utama, judul, bagian gelap |
| Aksi utama | `#284BDA` | CTA, Simpan, state aktif |
| Aksen identitas | `#C5282F` | Penekanan judul, garis/elemen identitas, bukan seluruh latar formulir |
| Kanvas | `#F8F6F1` | Latar halaman; kartu/form tetap putih |
| Teks sekunder | `#536173` | Metadata dan keterangan yang tetap terbaca |
| Aksen pendamping terbatas | `#EABF63` | Sorotan kecil dengan teks navy; tidak wajib hadir di semua layar |

Hasil hitung: putih/cobalt 6,75:1; putih/merah 5,65:1; navy/kanvas 14,79:1; teks sekunder/kanvas 5,85:1; navy/aksen pendamping 9,23:1. Hover, focus, border, overlay, status, dan kombinasi lain tetap harus diperiksa saat implementasi. Warna status sukses/peringatan/gagal dipisahkan secara semantik dan selalu disertai teks.

### Tipografi, bentuk, dan gerak

- Judul publik tegas: kisaran 48–64 px desktop dan 32–44 px mobile, diuji dengan nama lembaga dan judul panjang nyata. Body 16–18 px, tinggi baris 1,5–1,7; artikel dibatasi sekitar 60–70 karakter per baris.
- Mulai dengan satu keluarga sans yang konsisten. Kandidat heading ekspresif seperti Outfit boleh dibandingkan pada tahap prototype; belum menjadi keputusan instalasi/font eksternal. Fallback sistem tetap disiapkan.
- Radius terkendali 8–12 px, tombol sekitar 8 px; garis pemisah dan kontras permukaan lebih dominan daripada bayangan tebal di setiap kartu. Spasi mengikuti kelipatan 4/8 dengan versi mobile yang lebih rapat.
- Gerak 150–220 ms untuk hover, menu, dan transisi state; dukung `prefers-reduced-motion`. Ekspresi berasal terutama dari komposisi/warna/huruf, tanpa kewajiban animasi scroll, parallax, atau efek kaca.
- Ikon satu keluarga yang sudah tersedia, bobot dan ukuran seragam; setiap aksi penting tetap memiliki label.

### Penerapan per area

| Area | Arah |
|---|---|
| Hero | Komposisi desktop asimetris: judul/aksi dominan dan kutipan existing sebagai pendamping. Mobile ditumpuk dengan prioritas baca jelas. Tidak perlu gambar/ilustrasi baru untuk memulai. |
| Artikel | Satu cuplikan terbaru lebih besar + tiga pendamping; tanggal, judul dan ringkasan tetap mudah dipindai. Detail artikel lebih tenang, lebar baca terbatas. |
| Galeri | Foto menjadi fokus, ratio/crop konsisten dengan ritme yang berbeda dari artikel; tidak mengubah jumlah atau membuat foto resmi fiktif. |
| Login/register | Logo resmi dan tipografi yang sama dengan publik; form ringkas, label jelas, satu CTA utama. Desktop boleh memiliki bidang identitas, mobile mengutamakan form. |
| Area anggota | Header lebih ringkas, navigasi aktif jelas, kartu data dan tabel terang. Ekspresi cukup pada judul/aksen dan ringkasan penting. |
| Pengelolaan | Daftar, aksi utama, status dan filter mudah terlihat; form tidak menenggelamkan orientasi daftar pada mobile. |

## Urutan perbaikan yang disarankan

1. **Dasar penggunaan:** dashboard mobile, kontras aksi, placeholder rusak, target sentuh/fokus yang perlu diperbaiki berdasarkan pengukuran.
2. **Fondasi visual bersama:** palet semantik, font/hierarki, tombol, input, status, logo dan shell. Gunakan Blade, `resources/css/app.css`, dan Tailwind existing; tidak perlu React, shadcn, atau dependency baru.
3. **Prototype publik dan login:** terapkan arah modern ekspresif pada dua area representatif untuk dibandingkan desktop/mobile, sambil mempertahankan scope Hero/Artikel/Galeri/Footer.
4. **Penyelarasan anggota/pengelolaan:** gunakan komponen yang sama pada profil, alumni, materi, laporan dan management; tinjau tabel padat serta form panjang per konteks.

Daftar ini adalah rekomendasi, bukan implementasi yang sudah dijalankan. Penerapan visual berikutnya tidak sekaligus mengubah role, database, aturan publikasi, alur laporan, atau fitur yang ditunda.

## Verifikasi dan batas audit

- Sumber aktif dibaca untuk membedakan route/layout yang dirender dari template lama. Drawer aktif tetap menyediakan Materi/Laporan/Review/Verifikasi sesuai izin; jangan menganggap link hilang dari partial lama yang tidak dipanggil.
- Pemeriksaan browser memakai server/Chrome dan database sintetis lokal terisolasi. Konten QA hanya bukti pengujian, bukan bahan publikasi resmi. Bukti gambar disimpan lokal pada direktori ignored `storage/framework/testing/pub04qa/`; tidak ikut commit implementasi.
- Metrik kontras dihitung dengan `antislop-human/contrast-check.py`. Bukan audit WCAG penuh. Acuan tambahan: [Focus Not Obscured](https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum) dan [Error Identification](https://www.w3.org/WAI/WCAG22/Understanding/error-identification.html).
- Pencarian lokal ui-ux-pro-max dipakai untuk membandingkan karakter editorial/bold typography. Hasil database adalah ide desain, bukan bukti bahwa suatu gaya otomatis memenuhi aksesibilitas.
- Tidak menjalankan audit backend, keamanan, performa server atau dependency. Suite 266 test/1996 assertion dan build pada batch implementasi tetap merupakan bukti baseline; tidak diulang hanya karena penambahan dokumen audit.
- Browser desktop 1366 px dan mobile 390 px diperiksa pada homepage, daftar/detail artikel, login/register, dashboard, profil, alumni, materi yang tampil untuk super admin, daftar/form laporan dan pengelolaan Post/Galeri. Tampilan materi khusus alumni tidak diperiksa karena fixture sesi super admin diarahkan ke pengelolaan. Verifikasi/notifikasi, semua role/state/error dan seluruh rentang tablet/zoom belum diaudit secara visual lengkap.
- Full-page capture dapat menampilkan area gambar lazy-load yang belum masuk viewport; gambar digulir ke layar sebelum dinilai. Jangan menandai ruang kosong pada capture penuh sebagai kerusakan gambar produk.
- Judul profil mobile berwarna hitam di atas merah diamati, tetapi tidak diberi verdict WCAG karena klasifikasi ukuran/bobot huruf perlu pemeriksaan lebih lanjut. Sampel awal yang mengenai antialiasing glyph dibuang, bukan dipakai sebagai warna latar.
- Chrome/server QA ditutup setelah audit; server pengguna port 8000 dan database kerja tidak disentuh. Tidak ada CSS/view produk yang diubah oleh audit ini.

### Bukti lokal pilihan

Tautan berikut memerlukan berkas QA lokal yang diabaikan Git; bukan aset produksi atau konten resmi:

- [Dashboard desktop](../../storage/framework/testing/pub04qa/audit-dashboard-desktop.png) dan [dashboard mobile kosong](../../storage/framework/testing/pub04qa/audit-dashboard-mobile.png).
- [Homepage desktop](../../storage/framework/testing/pub04qa/audit-home-desktop-full.png), [bagian Artikel mobile](../../storage/framework/testing/pub04qa/audit-home-articles-mobile.png), dan [Galeri mobile](../../storage/framework/testing/pub04qa/audit-home-gallery-mobile.png).
- [Login desktop](../../storage/framework/testing/pub04qa/audit-login-desktop.png) dan [register mobile](../../storage/framework/testing/pub04qa/audit-register-mobile.png).
- [Pengelolaan Post mobile](../../storage/framework/testing/pub04qa/audit-management-post-mobile.png), [Galeri desktop](../../storage/framework/testing/pub04qa/audit-management-gallery-desktop.png), serta [alumni mobile](../../storage/framework/testing/pub04qa/audit-alumni-mobile.png).
- [Profil desktop](../../storage/framework/testing/pub04qa/audit-profile-desktop.png) dan [form laporan mobile](../../storage/framework/testing/pub04qa/audit-report-create-mobile.png).

## Tindak lanjut implementasi UIUX-02 — 2026-10-18

Audit di atas merekam baseline sebelum perbaikan. Pengguna kemudian mengizinkan implementasinya; hasil lokal:

| Temuan | Perbaikan |
|---|---|
| Dashboard mobile kosong | Wrapper `hidden md:block` dihapus; statistik, grafik dan tabel tampil responsif. |
| Kontras aksi/label lemah | Aksi utama cobalt, Hapus merah gelap, sekunder bertepi; label/input memakai warna terbaca. |
| Identitas berbeda antarshell | Logo resmi, font sistem, kanvas, header ringkas dan palet bersama di publik/auth/anggota. |
| Tombol tidak konsisten | Komponen tombol bersama, minimum target 44 px, fokus keyboard dan reduced motion. |
| Daftar Post sulit ditemukan di mobile | Daftar tampil lebih dulu dengan kartu/aksi; form tambah dibuka lewat tombol, tetap terbuka saat validasi gagal. |
| Komposisi publik seragam | Hero asimetris, cuplikan artikel adaptif dan Galeri dua baris dinamis; scope empat artikel/empat galeri tetap. |
| Fallback gambar hilang | Pengelolaan memakai helper URL existing dan placeholder teks, tanpa referensi aset yang hilang. |
| Tipografi/copy tidak seragam | Kontrak visual `DESIGN.md`, lebar baca artikel, label Indonesia dan penghapusan tautan daftar artikel ke dirinya sendiri. |

Browser QA terisolasi memeriksa desktop 1366, tablet 820, mobile 390/320 px. Homepage/dashboard/pengelolaan tidak overflow halaman; gambar publik terverifikasi setelah scroll. Assert interaksi lulus untuk form tambah, pemulihan validasi, modal Tab/Shift+Tab/Escape, drawer Escape dan login menuju register. Suite PHP 8.3 terisolasi 266 test/1996 assertion, build Vite, kompilasi Blade dan pemeriksaan diff lulus. Bukan sertifikasi aksesibilitas atau pengujian seluruh role/state/perangkat fisik. Review independen tertunda karena kuota agen; review mandiri diff selesai.

Bukti lokal ignored: [homepage desktop setelah perbaikan](../../storage/framework/testing/pub04qa/uiux02-home-final-1366.png), [homepage 320 px](../../storage/framework/testing/pub04qa/uiux02-home-final-320.png), [dashboard 320 px](../../storage/framework/testing/pub04qa/uiux02-dashboard-320.png), [Post 390 px](../../storage/framework/testing/pub04qa/uiux02-posts-390.png), dan [register 320 px](../../storage/framework/testing/pub04qa/uiux02-register-320.png). Assert interaksi dapat diulang memakai `storage/framework/testing/pub04qa/uiux02-checks.py` melalui browser-use pada sesi QA sintetis yang sudah login.
