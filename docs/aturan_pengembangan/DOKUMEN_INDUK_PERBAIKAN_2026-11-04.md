# Dokumen Induk Perbaikan Rausyan Fikr

Versi 1.1 — 4 November 2026. Edisi repositori, diselaraskan dengan source.

Status rancangan database: **masih dikembangkan; skema akhir belum dipilih**.

| Identitas | Acuan |
|---|---|
| Repositori | https://github.com/afikafiranti/rausyanfikrinstitute |
| Baseline pemeriksaan | develop — bd9cdc1bc728916060e50d4566eccc64a172df9b |
| Tanggal commit baseline | 18 Oktober 2026 |
| Tujuan dokumen | Menyatukan temuan, keputusan, prioritas, batas perubahan, rencana kerja, pengujian dan syarat selesai |
| Status dokumen | Acuan perbaikan telah dicatat; implementasi aplikasi/database belum dilakukan dalam pembahasan ini |
| Hubungan dengan audit | Melengkapi audit 3 November 2026 dengan arahan pengguna sesudah audit, terutama prioritas database |

Dokumen ini menggunakan kata “sudah” hanya untuk keadaan yang memiliki bukti. Persetujuan arah perbaikan menunjukkan pekerjaan perlu diprioritaskan; pilihan teknis yang belum ditetapkan tetap dibedakan dari keputusan pengguna.

## Rujukan agent dan status kode

- Progres implementasi berjalan dicatat satu kali di [TASK.md](TASK.md). Checklist pada dokumen induk ini merupakan spesifikasi dan snapshot awal, bukan register progres terpisah.
- Urutan mulai ada di [PLAN.md](PLAN.md#rencana-aktif). Status risiko dan tingkat buktinya ada di [AUDIT.md](AUDIT.md).
- Source diverifikasi ulang pada 4 November 2026 terhadap bd9cdc1bc728916060e50d4566eccc64a172df9b: 24 migration/21 tabel masih tersedia, referensi video_progress masih ada, dan jalur RF-01/04/05/06/09/10/12 belum berubah.
- Pada source tersebut, perbaikan aplikasi RFIX belum diterapkan. Sinkronisasi dokumentasi menutup subtugas pencatatan RFIX-20 saja; keseluruhan RFIX-20 berstatus PARTIAL di TASK. Temuan aplikasi tetap terbuka.
- Aturan penyelamatan dataset develop lama serta larangan menata migration lama digantikan oleh ARAH-06–08. Periksa target develop yang benar; database testing dan lingkungan lain tetap dibedakan.
- Hasil audit dependency dan log pengujian 3 November adalah bukti bertanggal. Jangan mengklaim audit/tes aplikasi baru hanya karena dokumen diterbitkan.

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

Konsekuensi ARAH-02: penataan index, hubungan data, konsistensi aturan data, dan efisiensi query dimajukan. Prioritas lama RF-10/RF-11 yang tertulis P2 pada audit awal dibaca bersama prioritas baru P1 dalam dokumen ini. ID temuan lama dipertahankan agar bukti tetap dapat dilacak.

Batas teknis awal: Laravel 12; target bersama PHP 8.3; Blade/Tailwind/Vite; MySQL atau MariaDB. Kebutuhan fungsi mencakup role super_admin, admin, koorda, alumni; multi-role; serta perbedaan kewenangan akun dan level materi. Nama/jumlah tabel, kolom, model, relasi dan susunan migration baseline tidak ditetapkan sebagai desain final.

Rancangan boleh berubah selama memenuhi kebutuhan web atau disertai keputusan perubahan kebutuhan yang jelas. Jalur berkas pada rincian pekerjaan menunjukkan titik awal pemeriksaan; bila rancangan baru mengubahnya, peta berkas dan semua pemakainya diperbarui bersama.

Penyusunan ulang schema/migration dan reset data develop diizinkan dalam arah perbaikan ini. Data lama develop tidak menjadi batas desain dan tidak memerlukan jalur upgrade kompatibel. Dokumen ini mencatat izin tersebut; reset belum dijalankan. Target reset harus benar-benar lingkungan develop yang terkait proyek, sedangkan database pengujian tetap terpisah. Izin ini tidak mencakup penghapusan lingkungan produksi atau sistem lain.

Keputusan reset develop tidak menghapus kebutuhan aturan aplikasi yang benar untuk pemakaian berikutnya: konsistensi laporan, privasi catatan, hak akses dan hubungan data tetap diuji. Migrasi framework, penulisan ulang seluruh aplikasi dan penambahan fitur tetap di luar kebutuhan perapian ini.

## 2. Cara membaca status dan bukti

### 2.1 Jenis bukti

| Kode | Makna | Batas kesimpulan |
|---|---|---|
| B1 | Kode sumber dan uji mekanisme terisolasi | Mekanisme tertentu terkonfirmasi; alur aplikasi penuh tetap perlu pengujian |
| B2 | Kode sumber, schema migration, dan penelusuran alur | Perilaku dapat diturunkan dari kode; belum sama dengan reproduksi pada database/server tujuan |
| B3 | Log CI atau hasil perintah yang tercatat | Berlaku pada commit dan lingkungan yang disebut |
| B4 | Pencocokan versi dependency dengan basis advisory | Menunjukkan rentang versi terdampak; keterjangkauan di aplikasi ditriase terpisah |
| B5 | Catatan historis proyek | Perlu pemeriksaan pemakaian saat ini sebelum tindakan pada server |
| B6 | Batas fitur atau perilaku produk yang terdokumentasi | Dicatat sebagai cakupan; tidak otomatis menjadi bug |
| B7 | Arahan perapian yang memerlukan inventarisasi | Tidak boleh disebut kerusakan data yang sudah terjadi tanpa bukti tambahan |

### 2.2 Status pekerjaan

1. Tercatat: masalah dan arah tindak lanjut telah dijelaskan.
2. Siap dikerjakan: baseline, target, lingkungan uji dan pilihan teknis yang diperlukan telah jelas.
3. Sedang dikerjakan: perubahan sedang berlangsung pada changeset tertentu.
4. Implementasi tersedia: patch/migration ada, tetapi belum memenuhi seluruh pengujian.
5. Terverifikasi: kriteria penerimaan dipenuhi dengan bukti pada commit tertentu.
6. Diterapkan: perubahan telah dipasang pada lingkungan yang disebut, dengan bukti penerapan dan pemeriksaan hasil.
7. Menunggu keputusan/data: pekerjaan memiliki pertanyaan atau bukti tertentu yang disebut secara konkret.

Pada saat dokumen ini dibuat, pekerjaan perbaikan aplikasi masih berstatus Tercatat. Bukti audit sebelumnya tidak boleh dipindahkan menjadi bukti patch baru.

### 2.3 Prioritas

| Prioritas | Arti dalam rencana ini |
|---|---|
| P0 | Celah keamanan mendesak; ditutup sebelum pemakaian nyata |
| P1 | Stabilitas awal, integritas database, kesederhanaan pengolahan data, akun pengelola, dependency dan bukti rilis |
| P2 | Perbaikan pengalaman penggunaan dan pemeliharaan setelah risiko utama terkendali |
| P3 | Penataan identitas/arsip teknis yang tidak menghalangi fungsi inti |

Risiko kunci historis naik menjadi mendesak bila terbukti kunci tersebut dipakai pada lingkungan nyata. Prioritas ditentukan dari dampak dan bukti, bukan hanya label severity paket.

## 3. Baseline yang sudah diperiksa

| Bukti | Hasil saat audit awal | Penafsiran yang benar |
|---|---|---|
| GitHub Actions | Run 39086196915, commit bd9cdc1, completed/success | Pipeline commit tersebut selesai; bukan bukti seluruh risiko aplikasi tertutup |
| PHPUnit pada CI | 280 warnings, 7 passed, 2.172 assertions; total 287 kasus | Tidak boleh diringkas menjadi 287 test bersih tanpa konteks warning |
| Build frontend lokal | Berhasil, 162 modul, Vite 7.2.4 | Membuktikan aset dapat dibangun pada pemeriksaan tersebut |
| Pengaman runner CI lokal | Berhasil menolak konfigurasi uji tidak aman dan menjaga environment | Pengaman tersebut harus dipertahankan |
| Audit npm | 19 entri paket: 4 low, 4 moderate, 9 high, 2 critical | Hasil bertanggal; dua entri critical mempunyai hubungan dependency |
| Pemeriksaan paket PHP runtime | 44 entri cocok pada 12 paket; 43 advisory unik setelah deduplikasi | Pencocokan composer.lock dengan API Packagist; bukan hasil eksekusi composer audit |
| Injeksi JavaScript | Nama biasa aman pada harness; apostrof menimbulkan SyntaxError; ekspresi penanda sintetis dieksekusi | Uji handler terisolasi, belum uji HTTP Laravel dan browser aplikasi penuh |
| Struktur database | 24 berkas migration membentuk 21 tabel bernama pada source | Belum merupakan inventaris database terpasang, ukuran, jumlah baris atau kondisi datanya |
| Kondisi source | Salinan pemeriksaan berada pada baseline dan tidak berisi patch aplikasi | Implementasi belum dimulai |

PHPUnit tidak dijalankan ulang secara lokal pada audit awal karena PHP, Composer dan database tidak tersedia. Binary Chromium lokal juga tidak tersedia. Lingkungan implementasi berikutnya harus diperiksa kembali; keterbatasan lama tidak dianggap tetap berlaku selamanya.

## 4. Register temuan dan hubungan dengan pekerjaan

| Temuan | Pokok temuan | Bukti | Prioritas baru | Pekerjaan |
|---|---|---|---|---|
| RF-01 | Nama alumni disisipkan ke JavaScript konfirmasi hapus | B1/B2 | P0 | RFIX-01 |
| RF-02 | Dependency PHP runtime cocok dengan advisory keamanan | B4 | P1 | RFIX-10 |
| RF-03 | Dependency frontend/tooling cocok dengan advisory keamanan | B3/B4 | P1 | RFIX-11 |
| RF-04 | Aturan hapus akun dapat menghapus laporan melalui cascade; berkas dapat tertinggal | B2; risiko perilaku aplikasi setelah dipakai | P1 | RFIX-03, RFIX-22 |
| RF-05 | Pengelola terakhir dapat kehilangan akses lewat perubahan profil atau self-delete | B2, bersyarat susunan pengelola | P1 | RFIX-08 |
| RF-06 | Perubahan wilayah/angkatan dapat mengganti suspended menjadi pending | B2 | P1 | RFIX-09 |
| RF-07 | CI berhasil dengan banyak warning; belum ada gate audit dependency eksplisit | B3 | P1 | RFIX-12 |
| RF-08 | Penilaian pemakaian APP_KEY historis belum tertutup | B5 | P1, kondisional | RFIX-13 |
| RF-09 | Dashboard memakai ApexCharts CDN tanpa versi terkunci; dokumentasi menyebut Chart.js | B2 | P2 | RFIX-14 |
| RF-10 | Pemeriksaan role mengulang query meski relasi sudah dimuat | B2; belum benchmark | P1 dalam perapian DB/kode | RFIX-07 |
| RF-11 | Index dengan susunan sama dibuat berulang | B2; belum inventaris DB terpasang | P1 dalam perapian DB | RFIX-04 |
| RF-12 | Judul Post berubah dapat mengganti slug dan memutus tautan lama | B2 | P2 | RFIX-15 |
| Tambahan arahan DB | Susun ulang schema/migration sederhana sesuai kebutuhan; data develop boleh dibuat ulang | B7/ARAH-06/07 | P1 | RFIX-02, RFIX-04–06, RFIX-22, RFIX-23 |
| Perapian kontrak | Helper/request laporan masih memuat alumni; route utama sudah menolak | B2 | P1 saat menyentuh alur tersebut | RFIX-18 |
| Batas ekspor | XLSX menyimpan workbook dalam memori walau query bertahap | B2/B6 | P2 | RFIX-16 |
| Perapian UX | Form profil ganda, modal tambah alumni, definisi statistik | B2/B7 | P2 | RFIX-17, RFIX-21 |
| Operasional | SMTP eksternal, staging, backup/restore dan commit rilis memerlukan bukti | B5/B6 | P1 untuk rilis | RFIX-19, RFIX-22 |
| Dokumentasi/arsip | Status historis berulang, aset/template sisa, metadata proyek/lisensi | B2/B5 | P2/P3 | RFIX-20 |

Tidak ada klaim bahwa seluruh advisory dapat dieksploitasi pada aplikasi ini, data nyata sudah rusak, atau server produksi memakai kunci yang bocor. Ketiga hal tersebut memerlukan bukti tersendiri.

## 5. Kontrak fungsi yang harus dipertahankan

### 5.1 Bahasa sederhana untuk tiga jenis tulisan

| Fitur | Untuk apa | Siapa pembacanya |
|---|---|---|
| Artikel | Membagikan penjelasan atau pembahasan suatu topik | Semua pengunjung setelah diterbitkan |
| Refleksi publik | Membagikan pengalaman atau renungan yang sengaja disiapkan untuk pembaca umum | Semua pengunjung setelah diterbitkan |
| Refleksi saya | Mencatat pengalaman belajar sendiri dan, bila diinginkan, meminta tanggapan pengelola | Penulis; pengelola berwenang setelah dikirim |

Artikel dan Refleksi publik merupakan dua jenis tulisan dalam satu pengelolaan publik. Pembedaan isi di atas merupakan penjelasan editorial; kode belum menerapkan kriteria isi khusus yang berbeda antara keduanya.

Refleksi saya mempunyai kepemilikan dan kerahasiaan tersendiri. Mengirimnya untuk mendapat tanggapan tidak menerbitkannya kepada masyarakat. Status “Sudah ditanggapi” tidak berarti lolos publikasi.

### 5.2 Hak setiap role

| Role | Tulisan publik | Refleksi sendiri | Refleksi orang lain |
|---|---|---|---|
| Alumni | Membaca tulisan terbit | Menulis, menyimpan draf, mengirim jika memenuhi syarat | Tidak dapat meninjau kiriman pengguna lain |
| Koorda | Membaca tulisan terbit; tidak mengelola Post/Galeri | Hak pribadi yang sama | Menanggapi kiriman sesuai wilayah kiriman, selain miliknya |
| Admin | Membaca tulisan terbit; tidak mengelola Post/Galeri | Hak pribadi yang sama | Menanggapi kiriman lintas wilayah, selain miliknya |
| Super admin | Mengelola dan menerbitkan Post/Galeri saat aktif | Hak pribadi yang sama | Menanggapi kiriman lintas wilayah, selain miliknya |
| Publik tanpa akun | Membaca tulisan terbit | Tidak memiliki ruang pribadi | Tidak dapat membaca catatan internal |

Kewenangan pengelolaan materi oleh admin tetap tersedia sesuai gate yang ada. Pembatasan Post/Galeri kepada super admin tidak diperluas secara keliru ke materi.

### 5.3 Privasi dan transisi Refleksi saya

1. Draf hanya dibaca, diubah dan dihapus penulis, termasuk terhadap admin/super admin lain.
2. Membuat draf tidak mensyaratkan status aktif; pengguna harus memiliki role anggota yang didukung.
3. Pengiriman memerlukan akun aktif dan wilayah. Ketentuan ini berlaku untuk semua role, termasuk super admin.
4. Wilayah saat pengiriman disimpan pada kiriman dan menjadi acuan koorda pembaca.
5. Setelah dikirim, isi dikunci; pengelola tidak dapat menyunting isi penulis.
6. Pengelola yang berwenang hanya memberi tanggapan pada kiriman orang lain.
7. Cakupan koorda berdasarkan wilayah kiriman, bukan jabatan penulis. Koorda dapat menanggapi kiriman pengelola lain di wilayahnya.
8. Saat ini satu kiriman menerima satu tanggapan pengelola; belum berupa percakapan.
9. Tidak ada penerbitan otomatis catatan pribadi sebagai tulisan publik. Nama model/tabel baseline bukan batas rancangan fisik baru.
10. Penerbitan catatan pribadi ke publik merupakan fitur/keputusan terpisah, dengan kejelasan persetujuan penulis bila kelak dirancang.

### 5.4 Batas produk lain yang tetap berlaku

1. Role akun: super_admin, admin, koorda, alumni. Satu user dapat memiliki beberapa role.
2. Status akun: pending, active, suspended. Penolakan verifikasi akun tetap pending dengan alasan tercatat.
3. Level materi berbeda dari role. Akses materi mensyaratkan active dan level yang memenuhi syarat.
4. Laporan hanya untuk pengelola aktif sesuai wilayah/kewenangan; alumni biasa tetap ditolak pada menu dan endpoint.
5. Laporan memiliki draft, submitted, approved, rejected. Self-review dan keputusan berulang ditolak.
6. Rejected belum mempunyai revisi/kirim ulang. Perapian database tidak membuka alur itu secara diam-diam.
7. Hitungan alumni saat ini mencakup seluruh akun dalam scope, termasuk pengelola. Perubahan definisi hitungan memerlukan keputusan produk.
8. Homepage tetap Hero → maksimal empat Artikel → maksimal empat Galeri → Footer.
9. URL Refleksi publik tetap tersedia. Saat baseline, menu utama/footer belum menautkannya; penambahan navigasi dibedakan dari perbaikan struktur.
10. Galeri langsung publik setelah disimpan. Status draf Galeri belum termasuk cakupan.
11. Verifikasi email tersedia tetapi belum menjadi syarat umum akses modul. Jangan menambahkan middleware verified ke semua modul tanpa menilai perubahan alur pengguna.
12. Metadata YouTube otomatis, progres menonton, CRUD web khusus master, agenda/tim/kontak tetap mengikuti cakupan tertunda/di luar cakupan yang sudah tercatat.

## 6. Arti database ringkas dan kode yang mudah dirawat

Ringkas dinilai dari fungsi yang jelas, duplikasi yang dapat dipertanggungjawabkan, hubungan data yang terjaga, dan aturan pengolahan yang konsisten. Jumlah tabel bukan satu-satunya ukuran.

### 6.1 Aturan desain

1. Gunakan satu identitas user untuk semua role; tidak membuat tabel akun terpisah untuk setiap jabatan.
2. Pertahankan kemampuan multi-role selama menjadi kontrak produk. Relasi roles–role_user merupakan kandidat sederhana yang sudah tersedia; rancangan fisiknya tetap boleh ditinjau. Struktur satu role tunggal tidak memenuhi kebutuhan multi-role.
3. Artikel dan Refleksi publik dapat memakai satu kelompok data dengan pembeda jenis; posts merupakan kandidat baseline. Pengelompokan tabel dipilih karena kebutuhan data, bukan semata-mata perbedaan nama menu.
4. Catatan pribadi mempunyai kepemilikan dan privasi berbeda dari tulisan publik. Kandidat tabel terpisah maupun kandidat pengelompokan lain harus membuktikan draf privat, status dan pembaca tidak tercampur. Tabel/model tertentu tidak dikunci lebih dahulu.
5. Laporan kajian menyimpan catatan kegiatan organisasi dan jumlah peserta; jurnal refleksi menyimpan pemaknaan pribadi. Rancangan harus menjaga perbedaan fungsi, relasi dan aturan tersebut. Penggabungan atau pemisahan fisik dinilai dari kejelasan dan kompleksitas kode yang dihasilkan.
6. Tabel framework dinilai dari konfigurasi dan pemakaiannya sebelum diusulkan dihapus.
7. Kandidat yang tidak diperlukan dapat dihapus dari schema develop setelah seluruh referensi model, relasi, tes dan seeder ditangani; data lama develop boleh dibuang sesuai ARAH-06.
8. Index ditambahkan atau dihapus menurut query dan constraint yang ditopangnya. Kebutuhan foreign key dan unique constraint tetap diperiksa.
9. Nilai turunan yang disimpan harus memiliki aturan konsistensi tunggal. Kehadirannya belum membuktikan overengineering.
10. Pengolahan data memakai Eloquent, FormRequest, Gate/policy, transaksi dan komponen Laravel yang sudah tersedia.
11. Abstraksi bersama dibuat ketika ada aturan yang benar-benar digunakan ulang atau transaksi membutuhkan batas yang jelas.
12. Tidak membuat lapisan repository/service/interface untuk setiap model secara otomatis; tidak membangun mesin status, permission atau konten generik untuk kebutuhan yang belum ada.
13. Query database tidak disebarkan ke view untuk keputusan yang dapat dihitung sekali. Pengamanan endpoint tetap ada.
14. Refactor dilakukan pada alur yang disentuh, dengan perubahan yang dapat ditinjau; tidak memformat ulang seluruh proyek atau mengganti seluruh folder sekaligus.

### 6.2 Inventaris awal dari migration

| Kelompok | Tabel yang ditemukan | Perlakuan awal |
|---|---|---|
| Identitas | users | Satu sumber akun dan profil |
| Role | roles, role_user | Kandidat baseline untuk multi-role; fungsi dan keunikan hubungan harus dipenuhi |
| Materi dan wilayah | levels, wilayah | Pertahankan fungsi terpisah; pastikan makna level dan wilayah konsisten |
| Tulisan publik | posts | Kandidat satu kelompok tulisan dengan pembeda jenis; schema akhir masih terbuka |
| Catatan pribadi | member_reflections | Penuhi pemisahan kepemilikan, draf dan tanggapan; bentuk tabel akhir masih ditinjau |
| Dokumentasi publik | galleries | Pertahankan fungsi Galeri dan status publikasi existing |
| Materi kajian | playlists, videos | Hubungan playlist–video memang diperlukan |
| Progres yang ditunda | video_progress | Kandidat dikeluarkan dari schema aktif karena fitur pengguna ditunda; tangani relasi Video::progresses dan tes yang masih memakainya, lalu catat keputusan |
| Laporan organisasi | kajian_reports | Prioritaskan pelestarian histori dan aturan penghapusan penulis/reviewer |
| Jejak perubahan | audit_logs | Jaga keterlacakan; tidak otomatis menambah duplikasi seluruh data pribadi |
| Notifikasi | notifications | Pertahankan notifikasi aplikasi dan kepemilikannya |
| Autentikasi | password_reset_tokens, sessions | Periksa driver dan alur reset/sesi; jangan dianggap tabel sisa |
| Cache | cache, cache_locks | Periksa konfigurasi cache database sebelum perubahan |
| Antrean | jobs, job_batches, failed_jobs | Periksa konfigurasi antrean dan kebutuhan kegagalan/retry sebelum perubahan |

Daftar tersebut berjumlah 21 tabel baseline, bukan target jumlah tabel akhir. Desain akhir boleh mengurangi atau menata ulang tabel menurut kebutuhan. Inventaris berfokus pada fungsi, relasi, driver framework, tipe kolom, constraint dan query. Tidak perlu menyelamatkan baris lama develop. Target akhirnya harus dapat dibentuk dari database kosong dan seeder.

Tabel framework tidak menambah kerumitan hanya karena jumlahnya banyak; periksa apakah driver yang dipilih membutuhkannya. Perubahan driver cache/sesi/antrean harus sesuai lingkungan hosting dan perilaku aplikasi. Tidak menetapkan target penurunan jumlah tabel yang mengorbankan fungsi.

Inventaris tersebut menjelaskan keadaan source yang dianalisis, bukan daftar tabel yang diwajibkan pada skema akhir. Kandidat penggabungan/pemisahan dinilai kembali pada proses bagian 6.4.

### 6.3 Kandidat duplikasi yang sudah terlihat

| Area | Bukti source | Langkah berikutnya |
|---|---|---|
| users(wilayah_id, level_id, status) | Dibuat dengan tiga nama index berbeda | Periksa index database nyata dan kebutuhan FK; sisakan struktur yang cukup untuk query/constraint |
| users(angkatan) | Muncul dengan dua nama index | Periksa kesamaan lengkap, lalu konsolidasikan bila setara |
| users(email) | Unique index sudah ada dan ada index tambahan | Pastikan index tambahan tidak mempunyai perbedaan penting sebelum dihapus |
| Total peserta | total_peserta disimpan di samping peserta_l dan peserta_p | Petakan pembaca/penulis dan buktikan invariant penjumlahannya; keputusan penyimpanan mengikuti kebutuhan nyata |
| Keunikan telepon | Validasi aplikasi meminta unik; kolom awal tidak memperlihatkan unique constraint | Selaraskan schema baru, aturan bisnis dan fixture seeder; tidak perlu membersihkan data lama develop |

Daftar ini tidak menyatakan bahwa sudah ada baris duplikat, total peserta salah, atau seluruh index yang disebut aman dihapus tanpa pemeriksaan.

### 6.4 Proses mencari skema yang paling sesuai

Status database saat ini adalah tahap perancangan. Tujuan pekerjaan adalah menemukan struktur yang mendukung alur web dengan beban pengembangan dan pemeliharaan yang wajar. Tidak ada keputusan bahwa seluruh tabel baseline, maupun usulan penyederhanaan pertama, merupakan rancangan final.

**Langkah perancangan per siklus:**

1. Daftar kebutuhan pengguna: akun/role, wilayah, level materi, tulisan publik, jurnal pribadi, laporan, notifikasi dan audit.
2. Untuk setiap kebutuhan, tetapkan data yang disimpan, pemilik, pembaca, pihak yang dapat mengubah, status dan akibat penghapusan.
3. Susun kandidat berdasarkan kebutuhan tersebut. Gunakan baseline sebagai pembanding, bukan batas desain.
4. Bandingkan bagian yang mempunyai tradeoff nyata: pemisahan/penggabungan data, nilai turunan, hubungan role, struktur wilayah, level, index dan tabel framework.
5. Uji bagian pembeda kandidat pada database develop/test yang boleh dibuat ulang, memakai fixture yang sama.
6. Catat kelebihan, kekurangan, query/aturan yang diperlukan, serta hasil pengujian.
7. Pilih skema acuan untuk siklus berikutnya, lalu selaraskan migration, kode, seeder dan tes.
8. Bila uji fungsi atau pemeliharaan menunjukkan masalah, revisi kandidat dan ulangi pengujian terkait.

Perbandingan cukup berupa rancangan dan percobaan bagian yang berbeda; tidak perlu memelihara dua versi aplikasi lengkap. Penggabungan tabel maupun pemisahan tabel mempunyai alasan yang dicatat. Nama model dan tabel baseline boleh berubah bersama seluruh pemakainya.

| Kriteria | Pertanyaan pemeriksaan | Bukti penerimaan |
|---|---|---|
| Kesesuaian fungsi | Apakah semua alur aktif mempunyai data dan hubungan yang cukup? | Matriks fungsi ke struktur dan tes alur |
| Privasi dan izin | Apakah draf pribadi, wilayah dan publikasi dapat dibatasi secara jelas? | Tes akses langsung setiap role/status/wilayah |
| Integritas | Apakah relasi, keunikan, status dan penjumlahan dapat dijaga? | Constraint dan tes transaksi/perilaku |
| Kesederhanaan | Berapa aturan khusus, percabangan dan duplikasi yang diperlukan? | Peta tanggung jawab serta peninjauan kode kandidat |
| Efisiensi | Apakah query penting, filter, daftar dan ekspor cukup ringan? | Pengukuran pada fixture yang sama |
| Keterulangan | Dapatkah schema dan contoh data dibentuk lagi dengan mudah? | Migrate dari kosong dan seed berulang |
| Pemeliharaan | Apakah satu perubahan kebutuhan dapat ditelusuri ke berkas/data yang jelas? | Catatan dampak dan tidak ada referensi yang tertinggal |

Kesesuaian fungsi, privasi dan integritas harus terpenuhi lebih dahulu. Setelah itu, pilih kandidat dengan struktur dan kode paling sederhana yang memenuhi kebutuhan. Jumlah tabel/kolom yang lebih sedikit baru bernilai bila keseluruhan pengolahan data ikut menjadi jelas.

**Catatan penilaian kandidat wajib memuat:** ID kandidat, kebutuhan yang didukung, gambar/daftar relasi, tabel/kolom/index yang berubah, aturan akses, contoh query utama, seeder yang dipakai, hasil pengujian, tradeoff dan alasan memilih atau merevisi kandidat. Angka performa hanya diisi dari pengukuran nyata.

| Status skema | Makna |
|---|---|
| Baseline | Struktur source yang diperiksa; belum merupakan pilihan akhir |
| Kandidat | Usulan struktur yang mempunyai alasan dan peta kebutuhan |
| Prototipe | Bagian terkait sudah dicoba pada database yang boleh dibuat ulang |
| Teruji | Kriteria penerimaan terkait terpenuhi dengan bukti |
| Dipilih per siklus | Menjadi acuan implementasi berikutnya dan tetap dapat direvisi |

Tabel pada bagian 6.2 merupakan inventaris baseline. Bagian 5 menetapkan kebutuhan fungsi yang diketahui saat ini. Keduanya tidak boleh disamakan menjadi kewajiban mempertahankan bentuk fisik database.

## 7. Keputusan teknis yang harus dicatat sebelum langkah terkait

| ID | Hal yang perlu ditetapkan | Arah minimum yang disarankan | Pengaruh pada pelaksanaan |
|---|---|---|---|
| KEP-01 | Perilaku hapus akun setelah aplikasi digunakan | Pilihan awal paling sederhana: tolak penghapusan akun yang memiliki laporan organisasi yang harus dipertahankan; arsip/anonimisasi hanya bila kebutuhannya ditetapkan | Tidak membatasi reset develop; menentukan FK schema baru, pesan dan tes RFIX-03 |
| KEP-02 | Retensi refleksi pribadi, identitas reviewer/audit dan unggahan ketika akun dihapus | Pisahkan kebijakan jurnal pribadi dari arsip laporan; jelaskan data yang tetap ada dan yang dibersihkan | Tidak menyalin seluruh catatan pribadi ke audit demi mempertahankan laporan |
| KEP-03 | Definisi pengelola terakhir dan perubahan profil kritisnya | Jaga keberadaan pengelola global aktif; nilai juga keberadaan super admin aktif sebagai satu-satunya penerbit konten | Tentukan perlakuan admin/super admin/multi-role dan instalasi awal; tidak otomatis menaikkan role pengguna |
| KEP-04 | Library grafik yang dipertahankan | Bandingkan memakai Chart.js existing dengan mengunci/membundel ApexCharts; pilih perubahan paling kecil yang memenuhi fungsi | Hapus jalur yang tidak dipakai setelah pilihan terverifikasi |
| KEP-05 | Batas volume ekspor XLSX | Tentukan dari pengukuran memori/waktu pada lingkungan target; CSV streaming tetap alternatif | Angka batas tidak dikarang sebelum pengukuran |
| KEP-06 | Pemakaian kunci historis pada server | Operator memeriksa identitas/pemakaian tanpa menampilkan nilai rahasia; rotasi mengikuti dampak data terenkripsi | Penilaian fakta diperlukan sebelum tindakan server |
| KEP-07 | Keunikan dan normalisasi telepon/data profil | Selaraskan validasi, constraint dan data seeder. Nilai apakah nomor bersama merupakan kebutuhan yang sah sebelum memilih unique constraint | Data lama develop boleh dibuang; aturan pengguna berikutnya tetap harus jelas |
| KEP-08 | Identitas dan lisensi proyek | Pemilik menetapkan keterangan yang berlaku; selaraskan metadata skeleton dengan pilihan tersebut | Pelaksana tidak menetapkan lisensi atas nama pemilik |
| KEP-09 | Nama menu dan akses navigasi Refleksi publik | Penjelasan sederhana dapat diperjelas; perubahan nama/menu atau penambahan tautan dicatat sebagai pilihan UX | Tidak mengubah pembaca/izin/publikasi hanya karena label diganti |
| KEP-10 | Kandidat schema yang dipakai pada siklus pengembangan berikutnya | Bandingkan rancangan menurut bagian 6.4; gunakan yang paling sederhana yang memenuhi fungsi dan pengujian | Belum ada nama/jumlah tabel atau rancangan fisik final yang disetujui |

Pilihan di tabel bukan keputusan pengguna yang sudah dibuat. Pekerjaan yang tidak bergantung pada pilihan tersebut tetap dapat berjalan. Penyusunan dokumen ini tidak memerlukan penghentian untuk menanyakan semuanya sekaligus.

## 8. Urutan pelaksanaan dan dependensi

| Tahap | Isi pekerjaan | Hasil yang harus tersedia |
|---|---|---|
| A — Persiapan dan perancangan | RFIX-01; petakan kebutuhan dan bandingkan kandidat melalui RFIX-02; verifikasi lingkungan uji | Handler aman, kebutuhan data jelas, kandidat dan cara pengujiannya tercatat |
| B — Prototipe dan pemilihan database | Uji bagian pembeda kandidat; pilih KEP-10; selaraskan RFIX-03–09/18/23 dan rebuild RFIX-22 | Kandidat terbukti memenuhi fungsi; schema terpilih per siklus dapat dibentuk ulang melalui seeder |
| C — Dependency dan mutu CI | RFIX-10–13; dapat ditriase sejak tahap A tanpa menunggu schema | Dependency ditangani menurut paparan; warning diketahui penyebabnya; risiko kunci mempunyai keputusan |
| D — Fungsi dan pemeliharaan | RFIX-14–17 dan RFIX-21 | Grafik terkendali, tautan stabil, ekspor dan form jelas |
| E — Bukti penerapan | RFIX-19, RFIX-20 dan verifikasi akhir RFIX-22 | Dokumentasi sinkron, bukti staging/email/backup dan commit rilis jelas |

Tahapan menunjukkan ketergantungan hasil, bukan kewajiban menunggu semua item satu tahap selesai sebelum membaca/menyiapkan tahap berikutnya. P0 dapat ditutup dalam perubahan kecil tersendiri. Database tidak ditunda sampai sesudah penambahan fitur.

Setiap changeset mengerjakan satu masalah yang dapat ditinjau dan diuji. Pencatatan ini belum menjalankan perubahan aplikasi/database. Saat implementasi, reset database develop yang benar telah diizinkan pengguna dan tidak memerlukan persetujuan ulang; pelaksana tetap memeriksa identitas target. Push, merge dan deployment mengikuti instruksi pelaksanaan yang berlaku.

## 9. Rincian pekerjaan

Semua ID RFIX di bawah merupakan ID baru khusus rencana ini. ID A/B/PUB yang sudah dipakai repositori tetap mempunyai makna historisnya; misalnya PUB-01 tidak dipakai ulang untuk menamai perbaikan tulisan pribadi.

Setiap pekerjaan mencatat commit awal/akhir, berkas berubah, perubahan data, perintah uji, hasil nyata, dan keputusan terkait. Kepemilikan file mengikuti AGENTS.md serta penugasan branch saat pelaksanaan; batch lintas domain diperiksa sebagai integrasi.

### RFIX-01 — Amankan konfirmasi hapus alumni

**Asal:** RF-01. **Prioritas:** P0. **Domain:** view bersama B dan endpoint/test alumni A.

**Target:** nama pengguna selalu diperlakukan sebagai teks. Nama berapostrof tidak merusak konfirmasi; ekspresi penanda tidak dijalankan.

**Berkas terkait:** resources/views/alumni/index.blade.php; resources/js/app.js atau modul JavaScript existing hanya jika handler memang dipindah; tests/Feature/AlumniDestroyTest.php; pengujian interaksi browser yang memakai tooling tersedia.

**Langkah:**

- [ ] Reproduksi dengan nama biasa, apostrof, kutip ganda, karakter HTML dan ekspresi penanda sintetis pada lingkungan uji.
- [ ] Gunakan pesan konfirmasi statis atau atribut data yang dibaca sebagai teks; pilih perubahan terkecil.
- [ ] Pertahankan CSRF, method DELETE, batas aktor/target dan larangan penghapusan yang sudah berlaku.
- [ ] Uji handler melalui browser aplikasi ketika runtime tersedia, termasuk batal dan lanjut hapus.
- [ ] Jalankan pengujian endpoint penghapusan serta build bila JavaScript/aset berubah.

**Syarat selesai:** tidak ada eksekusi penanda; konfirmasi tetap bekerja untuk nama sah; penghapusan di luar izin ditolak. Bukti handler terisolasi saja tidak menggantikan pemeriksaan integrasi akhir.

### RFIX-02 — Petakan kebutuhan dan susun rancangan schema develop

**Asal:** ARAH-02/03. **Prioritas:** P1, dikerjakan sejak tahap awal. **Domain:** integrasi A+B.

**Berkas terkait:** database/migrations/*.php; app/Models/*.php; config/database.php; config/cache.php; config/session.php; config/queue.php; seeder dan controller yang membaca/menulis setiap tabel.

**Langkah:**

- [ ] Catat vendor/versi database, target develop/test dan konfigurasi driver yang dipakai.
- [ ] Gunakan 21 tabel baseline sebagai inventaris awal, lalu petakan ke fungsi yang benar-benar dibutuhkan.
- [ ] Petakan setiap tabel/kolom ke pembaca, penulis, relasi, aturan hapus dan sumber data seeder.
- [ ] Putuskan struktur dipertahankan, disederhanakan, digabung bila satu fungsi, atau dihapus bila tidak diperlukan; tandai alasan setiap keputusan.
- [ ] Bandingkan kandidat menurut bagian 6.4 dan uji bagian yang membedakannya dengan data sintetis.
- [ ] Catat pilihan sementara/terpilih beserta alasan dan masalah yang belum lolos uji.
- [ ] Susun migration dari kosong, constraint/index dan seeder untuk kandidat yang sedang diuji; kompatibilitas dataset lama develop tidak diwajibkan.

**Syarat selesai:** tersedia kebutuhan data, kandidat yang dibandingkan, alasan keputusan KEP-10, rancangan yang dipilih untuk siklus tersebut, serta peta perubahan migration/model/tes/seeder. Rancangan dapat direvisi berdasarkan temuan pengujian; tidak dibatasi bentuk atau isi database lama develop.

### RFIX-03 — Lindungi histori laporan dan atur penghapusan akun

**Asal:** RF-04. **Prioritas:** P1. **Dependensi:** RFIX-02, KEP-01/02, lingkungan uji RFIX-22.

**Berkas terkait:** app/Http/Controllers/AlumniController.php; app/Http/Controllers/ProfileController.php; app/Models/User.php; app/Models/KajianReport.php; migration acuan hasil penataan yang mendefinisikan FK; tests/Feature/AlumniDestroyTest.php; tests/Feature/ProfileTest.php; tests/Feature/AlumniAuditTest.php; tests/Feature/KajianReportSummaryTest.php.

**Langkah:**

- [ ] Buat fixture penulis dengan laporan draft, submitted, approved dan rejected, serta pengguna yang hanya menjadi reviewer.
- [ ] Tulis tes gagal yang membuktikan kebijakan pelestarian yang dipilih pada penghapusan lewat daftar alumni dan profil sendiri.
- [ ] Terapkan aturan controller dan database yang konsisten; pemeriksaan aplikasi saja tidak cukup bila FK tetap dapat menghapus histori.
- [ ] Definisikan FK pada migration acuan hasil penyusunan ulang; data develop boleh direset dan dibentuk ulang lewat seeder.
- [ ] Tentukan pembersihan avatar/foto laporan/refleksi menurut retensi, setelah transaksi berhasil dan hanya untuk path milik aplikasi.
- [ ] Uji bahwa rekap jumlah laporan/kehadiran periode lama tetap sesuai kebijakan setelah tindakan akun.

**Syarat selesai:** penghapusan tidak diam-diam menghapus histori organisasi; pesan menjelaskan alasan penolakan bila ditolak; kebijakan jurnal pribadi dan file konsisten; audit tindakan terjaga. Tidak otomatis menerapkan soft delete ke semua tabel.

### RFIX-04 — Konsolidasikan index yang berlebih

**Asal:** RF-11. **Prioritas:** P1. **Dependensi:** RFIX-02.

**Berkas terkait:** migration users hasil konsolidasi; tiga migration index baseline sebagai pembanding; User::scopeAlumniListing; DashboardController; ChartController; tes schema/rebuild RFIX-22.

**Langkah:**

- [ ] Tentukan definisi index/constraint akhir dari query dan kebutuhan FK, termasuk urutan kolom dan keunikan.
- [ ] Tinjau query daftar, filter/sort, statistik dan ekspor menggunakan fixture sintetis yang dapat diulang.
- [ ] Letakkan index yang diperlukan satu kali pada migration acuan dan hapus definisi pembentuk index berulang dari susunan akhir.
- [ ] Bangun database kosong dan periksa index yang terbentuk; tidak wajib menguji jalur upgrade data develop lama.
- [ ] Bandingkan hasil query serta rencana eksekusi/waktu pada dataset seeder yang sama.

**Syarat selesai:** tidak ada index identik tanpa alasan; unique/FK berlaku; hasil daftar/rekap sesuai fixture. Nama dan isi migration lama boleh dirapikan/dikonsolidasikan karena target develop dibuat ulang. Hasilnya diuji dari database kosong.

### RFIX-05 — Selaraskan kolom, constraint dan aturan data

**Asal:** ARAH-03 dan hasil RFIX-02. **Prioritas:** P1.

**Berkas terkait:** migration users/role_user/levels/wilayah/kajian_reports; ProfileUpdateRequest; AlumniController; AlumniCsvController; StoreKajianReportRequest; UpdateKajianReportRequest; model terkait; tes profil, impor dan laporan.

**Langkah:**

- [ ] Cocokkan tipe, panjang, nullable, enum, default dan FK dengan aturan form, impor dan kode penyimpanan.
- [ ] Definisikan keunikan email/pasangan role serta telepon sesuai KEP-07; siapkan fixture valid dan fixture benturan untuk menguji constraint.
- [ ] Petakan nilai turunan seperti total_peserta dan pastikan jumlahnya konsisten pada create, update, submit, rekap dan ekspor.
- [ ] Samakan nilai yang dipakai formulir, seeder, enum dan filter; data develop lama dapat diganti dengan fixture yang konsisten.
- [ ] Lakukan hanya perubahan schema yang mempunyai kebutuhan dan dampak jelas; data yang bertentangan dilaporkan dengan strategi penanganannya.

**Syarat selesai:** schema baru, validasi dan seeder konsisten; constraint menolak data tidak sah; nilai turunan memenuhi invariant. Data develop lama tidak harus dipertahankan. Tidak menambah trigger, tabel enum baru, atau normalisasi berlapis tanpa kebutuhan.

### RFIX-06 — Tinjau tabel/kolom tertunda dan sisa schema

**Asal:** ARAH-03. **Prioritas:** P1 untuk keputusan struktur.

**Berkas terkait:** VideoProgress, migration video_progress, konfigurasi driver framework, kolom profil yang ditandai inventaris, seeder dan dokumentasi cakupan.

**Langkah:**

- [ ] Periksa pemakaian video_progress pada route, model, job, test, ekspor, data dan integrasi.
- [ ] Periksa fungsi kolom seperti consent_at dan makna AB sebelum menilai kebutuhan retensi/penghapusannya.
- [ ] Periksa sessions, cache, antrean dan password reset menurut driver yang dipakai.
- [ ] Catat keputusan akhir tiap kandidat beserta fungsi yang didukung atau alasan dikeluarkan dari schema aktif.
- [ ] Hapus kandidat yang tidak diperlukan beserta relasi/model/tes/seeder yang khusus bergantung padanya; reset develop telah diizinkan.

**Syarat selesai:** schema aktif berisi struktur yang mempunyai fungsi jelas dan seluruh referensinya konsisten. Untuk video_progress, temuan source tambahan menunjukkan relasi pada Video serta dua pengujian MateriManagementTest; keduanya ikut ditangani bila tabel dikeluarkan. Tidak menambah kembali fitur progres hanya untuk membenarkan keberadaan tabel.

### RFIX-07 — Kurangi query role berulang tanpa melemahkan izin

**Asal:** RF-10. **Prioritas:** P1. **Domain:** model User A; view bersama B.

**Berkas terkait:** app/Models/User.php; app/Http/Controllers/AlumniController.php; resources/views/alumni/index.blade.php; gate/provider dan navigasi hanya jika terpengaruh; tes role, akses regional dan daftar alumni.

**Langkah:**

- [ ] Ukur query nyata pada halaman dengan jumlah baris berbeda dan beberapa kombinasi role.
- [ ] Gunakan relasi yang sudah dimuat untuk keputusan tampilan atau hitung izin target sekali; pertahankan mekanisme izin endpoint.
- [ ] Hindari cache role global yang dapat mengembalikan izin lama setelah perubahan role.
- [ ] Uji multi-role, perubahan role dalam request/alur yang relevan, aktor tidak aktif dan koorda lintas wilayah.
- [ ] Bandingkan pertumbuhan jumlah query sebelum/sesudah.

**Syarat selesai:** query tambahan role tidak tumbuh empat kali per baris alumni biasa seperti jalur baseline; keputusan izin tetap benar. Estimasi 60/400 query pada audit awal tidak dipakai sebagai angka benchmark aktual.

### RFIX-08 — Lindungi keberadaan pengelola aktif

**Asal:** RF-05. **Prioritas:** P1. **Dependensi:** KEP-03.

**Berkas terkait:** ProfileController::update/destroy; AlumniController::update/destroy; AuthServiceProvider; VerificationController; model User; tests/Feature/ProfileTest.php; tests/Feature/AlumniDestroyTest.php; tests/Feature/VerificationRoleAccessTest.php.

**Langkah:**

- [ ] Tentukan invarian pengelola global dan super admin sesuai KEP-03, termasuk susunan multi-role.
- [ ] Tulis tes perubahan wilayah/angkatan, self-delete dan pengelola lain yang masih aktif.
- [ ] Lindungi transisi pada semua endpoint terkait, bukan hanya tombol/menu.
- [ ] Pertimbangkan transaksi dan penguncian bersama saat dua pengelola mengubah akun bersamaan; menghitung di luar transaksi saja dapat berpacu.
- [ ] Pertahankan larangan self-verify dan batas target verifikasi koorda; jangan membuka pemulihan lewat eskalasi role tidak berizin.

**Syarat selesai:** tindakan pengguna tidak menghilangkan seluruh jalur pengelolaan yang wajib tetap tersedia; penolakan dijelaskan; tindakan sah saat ada pengganti tetap berjalan.

### RFIX-09 — Pertahankan status suspended saat profil berubah

**Asal:** RF-06. **Prioritas:** P1.

**Berkas terkait:** ProfileController::update; AlumniController::update; aturan status User; tes ProfileTest dan AlumniAuditTest.

**Langkah:**

- [ ] Uji matriks status awal pending/active/suspended dengan perubahan biasa dan perubahan wilayah/angkatan.
- [ ] Pertahankan suspended ketika perubahan profil tidak merupakan tindakan aktivasi kembali yang sah.
- [ ] Terapkan perilaku konsisten pada pembaruan sendiri dan pembaruan lewat pengelola.
- [ ] Pertahankan audit/notifikasi yang sesuai hasil nyata; jangan mengirim pesan seolah akun masuk antrean biasa jika status tetap ditangguhkan.

**Syarat selesai:** profil tidak menjadi jalan menghilangkan penangguhan. Fitur aktivasi ulang baru tidak ditambahkan tanpa keputusan cakupan.

### RFIX-10 — Perbarui dependency PHP berdasarkan paparan

**Asal:** RF-02. **Prioritas:** P1. **Domain:** toolchain B, tes domain A+B.

**Berkas terkait:** composer.json, composer.lock, .github/workflows/tests.yml bila pemeriksaan ditambahkan, run-ci.sh, tes auth/profil/materi/laporan/refleksi.

**Langkah:**

- [ ] Jalankan audit Composer resmi dan catat waktu, versi paket, ID advisory serta dependensi yang menarik paket tersebut.
- [ ] Kelompokkan paparan HTTP/runtime, fitur yang tidak dikonfigurasi, CLI, dan kode yang tidak menerima input pengguna.
- [ ] Pertahankan target PHP 8.3/Laravel 12 dan lakukan pembaruan terarah dengan dependensi yang memang perlu ikut berubah.
- [ ] Jalankan validasi Composer, pemeriksaan platform, tes domain terdampak, lalu suite gabungan pada lockfile akhir.
- [ ] Catat advisory yang selesai diperbaiki, yang masih berlaku, dan penilaian tidak berlaku beserta alasannya.

**Syarat selesai:** lockfile baru konsisten, PHP 8.3 terpenuhi, risiko yang berlaku ditutup atau memiliki keputusan penanganan yang jelas. Advisory TokenGuard tidak disebut sebagai bypass login web tanpa konfigurasi guard yang relevan.

### RFIX-11 — Perbarui dependency frontend/tooling secara terarah

**Asal:** RF-03. **Prioritas:** P1.

**Berkas terkait:** package.json, package-lock.json, vite.config.js, postcss.config.js, tailwind.config.js dan modul JavaScript aktif.

**Langkah:**

- [ ] Perbarui hasil audit bertanggal dan petakan paket langsung/transitif serta pemakaiannya pada browser, build atau development server.
- [ ] Tangani rantai shell-quote/concurrently sebagai satu rantai dampak; periksa pula Axios, Vite, Rollup, PostCSS dan paket relevan lain.
- [ ] Hapus dependency yang terbukti tidak dipakai setelah memeriksa import, konfigurasi dan build.
- [ ] Pilih pembaruan versi yang menyelesaikan risiko tanpa memigrasikan Tailwind/framework secara tidak perlu.
- [ ] Jalankan instalasi dari lockfile, build dan interaksi yang bergantung pada JavaScript.

**Syarat selesai:** build dan interaksi tetap berfungsi; hasil audit akhir dan alasan triase tercatat. Jumlah advisory lama tidak diperlakukan sebagai ukuran keberhasilan tunggal.

### RFIX-12 — Bersihkan warning dan perjelas gate CI

**Asal:** RF-07. **Prioritas:** P1.

**Berkas terkait:** .github/workflows/tests.yml, run-ci.sh, phpunit.xml, tests/TestCase.php, tests/Support/TestDatabaseSafety.php, tests/Tooling/run-ci-test.sh, bootstrap/config jika terbukti menjadi penyebab.

**Langkah:**

- [ ] Tampilkan rincian warning PHPUnit yang terpotong dan pastikan penyebabnya sebelum memperbaiki.
- [ ] Pertahankan database test terpisah serta pemeriksaan sebelum RefreshDatabase bekerja.
- [ ] Perbaiki sumber warning; jangan sekadar mematikan pelaporannya agar pipeline hijau.
- [ ] Tetapkan kategori warning/deprecation dan advisory yang menggagalkan CI, berikut alasan pengecualian yang sah.
- [ ] Bedakan tes PHP, build aset dan pengujian interaksi browser dalam laporan hasil.
- [ ] Catat run CI pada commit patch akhir, bukan run baseline atau branch lain.

**Syarat selesai:** hasil CI dapat dibaca tanpa menyembunyikan peringatan; assertion, warning, skip dan batas pengujian dilaporkan akurat.

### RFIX-13 — Tutup penilaian kunci historis

**Asal:** RF-08. **Prioritas:** P1 kondisional untuk rilis.

**Berkas terkait:** .gitignore, template environment, dokumentasi AUDIT/TASK/runbook; konfigurasi server hanya pada lingkungan yang memang dituju.

**Langkah:**

- [ ] Periksa apakah kunci historis pernah digunakan pada lingkungan yang masih relevan tanpa menampilkan nilainya.
- [ ] Bedakan kunci develop yang datanya boleh dibuang dari kunci server yang mempunyai data terenkripsi.
- [ ] Untuk develop yang benar-benar dibangun ulang, gunakan kunci develop yang sesuai tanpa kebutuhan menyelamatkan dataset lama.
- [ ] Jika ada pemakaian server nyata, catat dampak sesi/data terenkripsi dan lakukan rotasi/pemulihan sesuai hasil inventarisasi.
- [ ] Pastikan rahasia tidak masuk source, log, contoh output atau dokumen.

**Syarat selesai:** pemakaian kunci diketahui atau ketidaktersediaan buktinya dijelaskan spesifik; tindakan sesuai lingkungan. Ketidakpastian kunci server tidak menghalangi perancangan schema develop.

### RFIX-14 — Kunci sumber grafik dan tangani kegagalan pemuatan

**Asal:** RF-09. **Prioritas:** P2. **Dependensi:** KEP-04.

**Berkas terkait:** resources/views/dashboard/index.blade.php, resources/js/app.js atau modul grafik existing, package.json/package-lock.json bila perlu, README.

**Langkah:**

- [ ] Pilih library yang dipertahankan berdasarkan cakupan perubahan dan fungsi tiga grafik aktif.
- [ ] Hilangkan ketergantungan URL JavaScript yang berubah tanpa versi; gunakan aset yang versinya terkunci.
- [ ] Tambahkan pemeriksaan HTTP gagal dan pesan yang dapat dipahami ketika grafik/data gagal dimuat.
- [ ] Sediakan pembacaan angka/tabel yang sesuai agar pengguna tidak bergantung pada warna/grafik saja.
- [ ] Uji responsif, data kosong, sesi tidak berizin dan kegagalan jaringan.

**Syarat selesai:** grafik berjalan dari dependency yang jelas, kegagalan tidak meninggalkan ruang kosong tanpa penjelasan, dan dokumentasi stack sesuai kode.

### RFIX-15 — Jaga kestabilan URL tulisan terbit

**Asal:** RF-12. **Prioritas:** P2.

**Berkas terkait:** app/Models/Post.php, LandingController, PostController, ArticlePublishingTest, PublicContentTest, ContentManagementTest.

**Langkah:**

- [ ] Tetapkan bahwa edit judul tulisan terbit tetap mempertahankan URL yang sudah dibagikan.
- [ ] Prioritaskan mempertahankan slug yang sudah tersimpan; riwayat slug/redirect ditambahkan hanya jika kebutuhan perubahan URL ditetapkan.
- [ ] Pertahankan pembeda jenis artikel/refleksi, keunikan slug, pembatasan draf dan aturan terbit.
- [ ] Uji edit judul lalu buka URL sebelumnya, collision judul sama, serta akses lintas jenis.

**Syarat selesai:** judul dapat berubah tanpa memutus URL terbit. Dataset develop baru dapat dibuat ulang; perlindungan ini ditujukan pada perilaku publikasi setelah digunakan.

### RFIX-16 — Tetapkan batas ekspor XLSX yang terukur

**Asal:** batas workbook memori. **Prioritas:** P2. **Dependensi:** KEP-05.

**Berkas terkait:** app/Support/AlumniExcel.php, AlumniCsvController, AlumniIndexRequest, resources/views/alumni/csv.blade.php, AlumniExcelTest/AlumniCsvTest.

**Langkah:**

- [ ] Ukur waktu dan puncak memori ekspor pada volume fixture yang bertambah.
- [ ] Pertahankan filter, scope wilayah, penulisan sel teks dan perlindungan formula.
- [ ] Pilih batas XLSX dengan pesan yang jelas serta CSV streaming sebagai pilihan pada volume lebih besar.
- [ ] Tambahkan writer/queue baru hanya jika batas sederhana tidak memenuhi kebutuhan yang dibuktikan.
- [ ] Uji data kosong, telepon berawalan nol, Unicode dan data yang menyerupai formula.

**Syarat selesai:** ekspor tidak gagal diam-diam pada volume yang didukung; batas berdasarkan pengukuran; hasil tetap mengikuti izin dan filter.

### RFIX-17 — Rapikan form dan interaksi tanpa mengubah cakupan data

**Asal:** perapian UX. **Prioritas:** P2.

**Berkas terkait:** resources/views/profile/edit.blade.php, resources/views/alumni/index.blade.php, komponen/modal dan JavaScript navigasi yang sudah ada; ProfileTest, AuthFormAccessibilityTest, NavigationAccessibilityTest.

**Langkah:**

- [ ] Gunakan satu susunan form responsif atau potongan field bersama bila itu cukup menghilangkan penggandaan desktop/mobile.
- [ ] Pertahankan field, endpoint, validasi, error bag, nilai input lama, upload dan autocomplete.
- [ ] Samakan perilaku fokus modal tambah alumni dengan kebutuhan keyboard: fokus masuk, Escape, pembatasan fokus dan kembali ke pemicu.
- [ ] Uji layar 320/390 piksel dan desktop, label/ID, pesan gagal dan tombol utama.
- [ ] Jelaskan definisi hitungan “alumni” sesuai kontrak seluruh akun dalam scope; jangan mengubah angka diam-diam.

**Syarat selesai:** form yang sama dapat digunakan pada ukuran layar terkait; tidak ada field/aksi penting hilang dan tidak terjadi ID ganda.

### RFIX-18 — Satukan kontrak izin dan aturan pengolahan data

**Asal:** perapian request/helper laporan dan ARAH-03. **Prioritas:** P1 saat menyentuh alur terkait.

**Berkas terkait:** routes/web.php, StoreKajianReportRequest, UpdateKajianReportRequest, ReportController::visibleReports, AuthServiceProvider, ScopeWilayah dan model terkait.

**Langkah:**

- [ ] Selaraskan request/helper laporan dengan kontrak pengelola aktif saja; alumni tetap ditolak.
- [ ] Petakan aturan yang sama di route, controller, request dan view; tentukan satu rujukan aturan yang sederhana menggunakan pola Laravel existing.
- [ ] Pertahankan scope wilayah, role ganda, kepemilikan draf, larangan self-review dan row lock.
- [ ] Kurangi percabangan mati yang hanya mendukung alur lama yang sudah dikeluarkan dari cakupan.
- [ ] Uji endpoint langsung, bukan hanya keberadaan menu.

**Syarat selesai:** lapisan aplikasi menyatakan aturan yang sama dan tidak membuka akses akibat refactor. Tidak membangun permission engine atau workflow generik baru.

### RFIX-19 — Buktikan kebutuhan operasional sebelum rilis nyata

**Asal:** tindak lanjut operasional. **Prioritas:** P1 untuk penerapan.

**Berkas terkait:** template environment, config/mail.php, config/filesystems.php, runbook, catatan QA, pengujian auth/impor/upload.

**Langkah:**

- [ ] Verifikasi reset password, verifikasi email dan pengiriman ulang pada transport eksternal yang benar.
- [ ] Uji pengguna hasil impor dapat menyetel password dan masuk.
- [ ] Uji HTTPS, cookie, document root public, URL aset, disk privat/publik dan konfigurasi proxy pada lingkungan tujuan.
- [ ] Pastikan konfigurasi driver sesi/cache/antrean cocok dengan schema yang dipertahankan dan layanan yang dijalankan.
- [ ] Lakukan smoke test setiap role dan wilayah dengan data sintetis.

**Syarat selesai:** bukti lingkungan tujuan tersedia. Staging/SMTP yang belum tersedia dicatat sebagai kebutuhan lingkungan tertentu, tanpa menghalangi reset dan perapian develop.

### RFIX-20 — Selaraskan dokumentasi, identitas dan arsip teknis

**Asal:** perapian dokumentasi/arsip. **Prioritas:** P2/P3.

**Berkas terkait:** README.md, AGENTS.md, docs/aturan_pengembangan/PRD.md, PLAN.md, TASK.md, AUDIT.md, SOP.md, CATATAN_JALUR_A/B, composer.json, layout/aset lama dan cache PowerShell yang ter-track.

**Langkah:**

- [ ] Tautkan dokumen induk ini sebagai rujukan arahan baru ketika dimasukkan ke repo saat implementasi.
- [ ] Pisahkan ringkasan status terkini dan arsip; pertahankan bukti audit lama sebagai riwayat.
- [ ] Selaraskan keterangan reset develop, schema acuan dan seeder agar tidak tersisa perintah pelestarian data lama yang sudah diganti pengguna.
- [ ] Hapus berkas/aset/template yang terbukti tidak dirujuk; jangan menandai kerusakan template tidak aktif sebagai kegagalan halaman aktif.
- [ ] Selaraskan metadata proyek dan keputusan lisensi KEP-08.
- [ ] Catat commit rilis yang dipilih; jangan menganggap main identik dengan develop.

**Syarat selesai:** pelaksana baru dapat mengenali prioritas, kontrak fungsi, cara membentuk database dan status implementasi tanpa membaca riwayat yang saling menimpa.

### RFIX-21 — Perjelas istilah publik dan catatan pribadi

**Asal:** ARAH-04 dan penjelasan fitur. **Prioritas:** P2.

**Berkas terkait:** view pengelolaan Post, view Refleksi saya/Tinjau refleksi, navigasi anggota/publik, PRD dan panduan penggunaan.

**Langkah:**

- [ ] Gunakan penjelasan sederhana pada bagian 5 untuk membedakan tulisan publik, catatan sendiri dan kiriman untuk ditanggapi.
- [ ] Jelaskan pembaca sebelum pengguna mengirim catatan; gunakan istilah “tanggapan” yang tidak menyiratkan persetujuan publikasi.
- [ ] Jika nama menu atau tautan Refleksi publik diubah, catat keputusan KEP-09 dan pertahankan URL/fungsi yang sudah tersedia.
- [ ] Uji draf penulis, kiriman lintas wilayah, pengelola lain, super admin dan pengunjung tanpa akun.

**Syarat selesai:** pengguna dapat mengetahui siapa pembacanya dan apa akibat tombol simpan/kirim/terbit. Hak admin biasa tidak diperluas menjadi penerbit dan catatan pribadi tidak otomatis dipublikasikan.

### RFIX-22 — Buktikan rebuild, integrasi dan pemulihan sesuai tahap

**Asal:** ARAH-06/07 dan verifikasi seluruh perubahan data. **Prioritas:** P1.

**Berkas terkait:** migration acuan, seeder/factory, tests/TestCase.php, TestDatabaseSafety, run-ci.sh, script/runbook pemulihan ketika relevan, tes domain A+B.

**Langkah:**

- [ ] Verifikasi bahwa target reset adalah database develop proyek ini; database pengujian memakai nama terpisah berakhiran _testing.
- [ ] Bangun schema dari database kosong dan jalankan seeder RFIX-23; periksa seluruh constraint, index dan hubungan.
- [ ] Jalankan pengujian domain pada schema baru, kemudian suite gabungan dan build pada PHP 8.3/MySQL atau MariaDB.
- [ ] Jalankan ulang pembentukan dari kosong untuk memastikan tidak ada ketergantungan pada tabel/baris develop lama.
- [ ] Jalankan ulang seeder tanpa reset untuk membuktikan tidak menambah duplikasi master/akun fixture.
- [ ] Sebelum pemakaian nyata, perluas drill backup/restore mencakup tulisan publik, Refleksi saya dan file privat/publik pada schema akhir.

**Syarat selesai:** develop dapat dibentuk ulang secara konsisten dari source+seeder; tes tidak menyasar database kerja yang salah; bukti rilis menggunakan commit akhir. Jalur upgrade dan penyelamatan data lama develop tidak diwajibkan.

### RFIX-23 — Sediakan seeder dan fixture yang menjadi sumber data develop

**Asal:** ARAH-06/07. **Prioritas:** P1, bagian inti penataan database.

**Berkas terkait:** DatabaseSeeder, RoleSeeder, LevelSeeder, WilayahSeeder, UserSeeder, PostSeeder, GallerySeeder dan factory existing. Tambahkan seeder domain laporan/materi/refleksi hanya bila diperlukan untuk membangun contoh yang dapat dipakai; hindari satu seeder per baris atau satu kelas untuk setiap variasi status.

**Langkah:**

- [ ] Pisahkan data referensi yang diperlukan aplikasi dari contoh akun/konten yang hanya untuk local/testing.
- [ ] Bentuk empat role dan empat level dengan makna yang benar; selaraskan ID/urutan level dengan logika akses yang dipilih.
- [ ] Sediakan cakupan minimal dua wilayah, semua role, active/pending/suspended, akun tanpa wilayah dan kombinasi role untuk pengujian.
- [ ] Sediakan Artikel/Refleksi publik berstatus draf/terbit, Galeri, playlist/video serta laporan dan refleksi pribadi dengan status yang diperlukan.
- [ ] Gunakan identitas sintetis dan alamat domain .test pada akun contoh; semua hubungan owner/reviewer/wilayah harus sah.
- [ ] Buat total peserta dan angka rekap yang dapat dihitung ulang dari fixture.
- [ ] Gunakan pola seed yang tidak menghasilkan duplikasi ketika diulang; data contoh tetap dibatasi local/testing.
- [ ] Buktikan instalasi develop dapat digunakan tanpa input database manual setelah migrate dan seed.

**Syarat selesai:** data dasar dan skenario develop dapat diulang dari source, cukup untuk mencoba alur inti, tidak bercampur dengan klaim/konten resmi, dan tidak memerlukan tabel fitur tertunda agar test dapat berjalan.

## 10. Strategi migration dan reset develop yang disepakati

### 10.1 Kewenangan reset

Pengguna secara eksplisit mengizinkan data develop saat ini dibuang dan disusun ulang melalui seeder. Pelaksana tidak perlu meminta persetujuan ulang untuk reset pada database develop proyek yang sudah teridentifikasi benar.

Pemeriksaan target dilakukan untuk memastikan perintah mencapai lingkungan yang dimaksud: nama host, nama database, environment aplikasi, konfigurasi yang sedang dibaca dan status config cache. Pemeriksaan ini tidak mengubah izin pengguna menjadi tahap persetujuan tambahan.

Pencatatan rencana ini belum menjalankan reset. Database testing tetap terpisah; pengaman _testing existing tidak dinonaktifkan.

### 10.2 Penyusunan ulang schema yang dipilih per siklus

1. Petakan kebutuhan dan nilai kandidat menurut bagian 6.4 sebelum mengunci rancangan fisik. Schema baseline bukan rancangan final.
2. Susun kandidat dari kebutuhan aplikasi aktif dan kontrak pada bagian 5; kebutuhan yang perlu berubah dibahas dan dicatat secara terpisah.
3. Gabungkan migration tambahan yang hanya membentuk satu kondisi awal menjadi susunan acuan yang mudah dibaca.
4. Buat tabel referensi sebelum foreign key yang membutuhkannya; urutan pembentukan harus deterministik.
5. Kolom, constraint dan index awal didefinisikan satu kali. Hindari rangkaian create–alter–koreksi untuk mencapai desain awal yang sama.
6. Perubahan nama, isi atau penghapusan migration lama diizinkan untuk rebuild develop ini.
7. Jumlah file migration mengikuti kelompok tanggung jawab dan ketergantungan. Tidak memaksakan satu file raksasa atau satu file untuk setiap kolom.
8. Cocokkan model, relasi, fillable/cast, validasi, query, factory, seeder dan tes dengan schema akhir.
9. Migrasi awal baru diuji dari database kosong. Kompatibilitas dataset develop lama tidak menjadi kriteria penerimaan.
10. Setelah aplikasi mempunyai data operasional yang harus dipertahankan, perubahan schema berikutnya memakai strategi migration data yang sesuai; izin reset develop sekarang tidak digeneralisasi ke masa tersebut.


### 10.3 Contoh urutan perintah pada tahap implementasi

Perintah berikut merupakan prosedur yang direncanakan, belum dijalankan saat pencatatan ini.

~~~bash
# Setelah host/database develop, environment dan config cache diperiksa:
php artisan migrate:fresh --seed
php artisan db:seed
php artisan route:list
~~~

Perintah pertama menghapus dan membentuk ulang tabel pada koneksi aktif, lalu mengisi seeder. Perintah kedua dipakai untuk memeriksa pengulangan seed; jumlah data referensi/fixture tidak boleh bertambah karena duplikasi.

Uji otomatis dilakukan pada database _testing terpisah dengan pengaman existing. Tidak mengubah environment menjadi testing hanya untuk melewati pemeriksaan pada database yang sebenarnya bukan database uji.

### 10.4 Yang sengaja tidak menjadi beban pekerjaan develop

1. Penyelamatan baris lama develop.
2. Pemindahan data lama ke schema baru.
3. Backfill khusus demi kompatibilitas dataset develop lama.
4. Migration tambahan semata-mata agar seluruh rangkaian schema lama dapat dipertahankan.
5. Penambahan kolom arsip, riwayat slug atau mesin status hanya untuk menjaga fixture develop yang boleh dibuat ulang.

Ketentuan ini tidak menghapus aturan perilaku aplikasi setelah digunakan: hubungan data sah, privasi, izin, tautan terbit dan konsistensi laporan tetap bagian kualitas fungsi.

## 11. Cakupan seeder dan factory

| Kelompok data | Isi minimum yang diperlukan | Syarat |
|---|---|---|
| Role | super_admin, admin, koorda, alumni | Nama konsisten dengan gate dan helper |
| Level | dasar, menengah, lanjutan, akhir | Role tidak disimpan sebagai deskripsi level; urutan akses jelas |
| Wilayah | Data referensi yang ditetapkan dan cukup wilayah untuk uji scope | Tidak mengarang daftar wilayah resmi yang belum disepakati |
| Akun develop | Semua role, kombinasi role, active/pending/suspended dan tanpa wilayah | Sintetis, relasi sah, terbatas local/testing |
| Tulisan publik | Artikel dan Refleksi; draf dan terbit | Draf tidak tampil publik; penulis catatan pribadi tidak otomatis menjadi penerbit |
| Galeri | Contoh foto/caption yang aman untuk demo | Contoh dibedakan dari dokumentasi resmi |
| Materi | Playlist/video pada level berbeda dan keadaan aktif/tidak aktif | Menguji akses level/status tanpa mengaktifkan fitur progres tertunda |
| Laporan | draft/submitted/approved/rejected dengan owner/reviewer yang berbeda | Total peserta benar, wilayah sesuai, tanggal tetap atau dikendalikan test |
| Refleksi saya | Draf privat, terkirim dan sudah ditanggapi | Wilayah snapshot, kepemilikan dan reviewer sah |
| Notifikasi/audit | Contoh secukupnya untuk memeriksa hasil tindakan | Tidak memalsukan bukti penggunaan nyata |
| File | Sampul/foto contoh yang benar-benar tersedia atau fallback yang jelas | Tidak menghasilkan referensi ke file hilang; foto privat tetap privat |

Factory dipakai untuk variasi tambahan dan pengujian volume. Seeder default tidak harus membuat ribuan baris agar dashboard tampak penuh. Dataset pengukuran dibuat terpisah agar pembentukan develop tetap ringan.

Master dan akun demo dibuat idempotent melalui pola existing seperti firstOrCreate/syncWithoutDetaching bila sesuai. Pembuatan ulang menyeluruh dilakukan melalui reset develop yang sudah diizinkan, bukan lewat seeder yang diam-diam menghapus seluruh tabel.

## 12. Matriks pengujian penerimaan

| Area | Skenario minimum | Hasil yang diharapkan | Bukti terkait |
|---|---|---|---|
| XSS konfirmasi | Nama normal, apostrof, kutip ganda, karakter HTML, ekspresi penanda; batal/lanjut | Nama tetap teks, konfirmasi bekerja, marker tidak dieksekusi | RFIX-01 |
| Rebuild DB | Database kosong → migrate → seed → seed ulang | Schema lengkap, relasi sah, tidak ada duplikasi seed | RFIX-02/04–06/22/23 |
| Role dan wilayah | Empat role, multi-role, koorda dua wilayah, tanpa wilayah, status nonaktif | Endpoint sesuai kontrak; UI dan server konsisten | RFIX-07/18 |
| Hapus akun | User tanpa laporan, penulis berbagai status laporan, reviewer, self-delete | Kebijakan yang dipilih berlaku di controller dan FK; file sesuai retensi | RFIX-03 |
| Pengelola terakhir | Satu/banyak pengelola global, satu/banyak super admin, tindakan bersamaan | Jalur pengelolaan yang wajib tersedia tetap ada | RFIX-08 |
| Status akun | Pending/active/suspended × edit biasa/kritis × sendiri/pengelola | Suspended tidak hilang lewat edit profil; audit/notifikasi sesuai | RFIX-09 |
| Publikasi | Dua jenis tulisan, draf/terbit, judul berubah, slug sama | Hanya terbit terbaca, jenis tidak tertukar, URL terbit tetap sah | RFIX-15/21 |
| Privasi jurnal | Draf semua role, pengelola lain, kirim tanpa syarat, wilayah berubah, self-review, review ulang | Draf privat, kiriman terkunci, wilayah snapshot dan satu tanggapan dipertahankan | RFIX-21 |
| Laporan | Draf/edit/kirim/review/rekap/CSV/foto; alumni akses langsung | Scope/status/owner/reviewer benar, alumni ditolak, foto tidak terbuka publik | RFIX-03/18/22 |
| Impor/ekspor | CSV/XLSX valid/tidak valid, duplikat, nomor nol depan, formula, scope | Data sah tersimpan; data salah dilaporkan; hasil aman dan sesuai filter | RFIX-05/16 |
| Grafik | Data kosong, data terisi, jaringan gagal, respons HTTP gagal | Versi library terkunci; angka terbaca; pesan gagal jelas | RFIX-14 |
| Form/mobile | 320/390 piksel, desktop, keyboard, fokus modal, validasi gagal | Field dan aksi tersedia; label/ID benar; input lama dipertahankan | RFIX-17 |
| Email/onboarding | Register, kirim ulang, reset, pengguna hasil impor, transport gagal | Pengiriman dan pemulihan sesuai alur; kegagalan ditangani jelas | RFIX-19 |
| CI/dependency | PHP 8.3, database yang benar, lockfile akhir, warning dan advisory | Hasil segar per commit; tidak ada penyembunyian kegagalan | RFIX-10–12 |
| Pemulihan sebelum live | Schema akhir + file publik/privat + kunci yang sesuai | Restore dan smoke test berhasil pada lingkungan uji | RFIX-19/22 |

Tidak perlu menulis tes yang hanya menyalin bentuk implementasi. Utamakan perilaku berisiko, batas akses, transisi status, constraint, rebuild dan integrasi. Perubahan dokumentasi/label kecil cukup diverifikasi sesuai dampaknya.

## 13. Perintah verifikasi dan pemaknaan hasil

Semua perintah dijalankan pada lingkungan yang sudah disiapkan; keberadaannya dalam dokumen tidak berarti sudah dieksekusi.

~~~bash
composer validate --strict
composer check-platform-reqs
composer audit --locked --format=json
npm audit --package-lock-only --json
npm run build
php artisan route:list
~~~

Untuk pengujian gabungan gunakan runner existing setelah variabel TEST_DB_* diarahkan ke database uji proyek yang baru/terpisah dan guard berhasil:

~~~bash
bash tests/Tooling/run-ci-test.sh
bash run-ci.sh
~~~

Pengujian domain dapat dipilih setelah environment testing aman:

~~~bash
php artisan test --env=testing --filter=AlumniDestroyTest
php artisan test --env=testing --filter=ProfileTest
php artisan test --env=testing --filter=MemberReflectionTest
php artisan test --env=testing --filter=KajianReport
~~~

1. Build yang berhasil tidak membuktikan permission, email, browser atau advisory selesai.
2. Tes PHP tidak menggantikan pengujian JavaScript/browser.
3. Test pada PHP 8.4/8.5 atau lint saja tidak menggantikan runtime PHP 8.3.
4. Audit dependency dapat menghasilkan exit code gagal karena advisory; hasilnya ditriase, bukan disembunyikan.
5. Uji database memakai MySQL/MariaDB sesuai query aplikasi; SQLite bukan pengganti otomatis.
6. “Migrate berhasil” belum membuktikan seeder, data, relasi, izin dan alur pengguna benar.
7. Jumlah test/assertion yang berubah dicatat dari output nyata; angka baseline tidak disalin untuk patch baru.

## 14. Catatan perubahan per pekerjaan

Setiap RFIX memiliki satu catatan dengan kolom berikut. Kolom hasil diisi saat pelaksanaan; dokumen ini tidak mengisinya dengan hasil fiktif.

| Kolom catatan | Isi yang diwajibkan |
|---|---|
| ID pekerjaan/temuan | RFIX terkait dan RF/ARAH asalnya |
| Status dan tanggal | Tercatat/siap/sedang/implementasi/terverifikasi/diterapkan |
| Baseline dan hasil | Branch, commit awal, commit patch dan commit pengujian |
| Target lingkungan | Develop/test/staging; host/database dicatat tanpa rahasia |
| Keputusan terkait | KEP yang dipakai, alasan dan siapa yang menetapkan |
| Berkas dan schema | File berubah; tabel/kolom/FK/index yang ditata |
| Data develop | Apakah direset dan seeder apa yang digunakan |
| Perilaku sebelum/sesudah | Perubahan yang dapat diamati pengguna |
| Pengujian | Perintah/alat, runtime, hasil, assertion/warning/skip dan bukti |
| Dampak terkait | Izin, privasi, notifikasi, file, API/URL dan konfigurasi |
| Batas yang tersisa | Masalah konkret yang belum tertutup dan pekerjaan penanganannya |
| Persetujuan penerapan | Hanya diisi ketika ada tindakan merge/deploy yang memang diinstruksikan |

Status “Terverifikasi” memerlukan kriteria pekerjaan dan tes terkait terpenuhi. Status “Diterapkan” memerlukan bukti lingkungan tujuan; commit saja tidak cukup.

## 15. Syarat penutupan tahap perbaikan

- [ ] RF-01 ditutup dengan uji handler dan integrasi yang relevan.
- [ ] Schema akhir dibangun ulang dari kosong dan tidak memerlukan data develop lama.
- [ ] Seeder/factory cukup untuk mencoba fungsi inti dan pengulangan seed tidak menggandakan data.
- [ ] Setiap tabel/kolom/index aktif mempunyai fungsi yang dapat dijelaskan.
- [ ] Model, request, controller, view, factory, seeder dan tes sesuai schema akhir.
- [ ] Perilaku penghapusan akun/laporan/refleksi/file mengikuti keputusan yang dicatat.
- [ ] Pengelola terakhir dan status suspended terlindungi.
- [ ] Query role tidak berulang per baris tanpa kebutuhan.
- [ ] Scope, multi-role, status, draf pribadi dan batas publikasi tetap benar.
- [ ] Dependency dan warning mempunyai penanganan berbukti.
- [ ] Grafik, URL dan ekspor memenuhi target pekerjaan masing-masing.
- [ ] Dokumen pusat menyebut prioritas DB dan izin reset develop yang terbaru.
- [ ] Bukti uji mencantumkan commit/runtime/lingkungan yang benar.
- [ ] Penerapan nyata hanya dinyatakan selesai setelah bukti email, konfigurasi dan pemulihan terkait tersedia.

Tidak ada syarat menjaga dataset develop lama, menguji upgrade dataset lama, atau mempertahankan jumlah migration/tabel baseline.

## 16. Sumber, batas dan riwayat arahan

### 16.1 Sumber

1. [Audit awal 3 November 2026](AUDIT_REPOSITORI_2026-11-03.md).
2. Source pada commit bd9cdc1bc728916060e50d4566eccc64a172df9b: [repositori](https://github.com/afikafiranti/rausyanfikrinstitute/tree/bd9cdc1bc728916060e50d4566eccc64a172df9b).
3. [CI baseline 39086196915](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915).
4. AGENTS.md serta PRD/PLAN/TASK/AUDIT/SOP pada docs/aturan_pengembangan sebagai kontrak baseline; arahan eksplisit terbaru pengguna dalam dokumen ini menggantikan bagian yang bertentangan untuk cakupan develop saat ini.
5. Penelusuran source tambahan saat pencatatan: video_progress masih dirujuk Video::progresses dan dua tes MateriManagementTest, walaupun fitur pengguna ditunda. Rujukan itu wajib diperbarui bila struktur dikeluarkan.

### 16.2 Riwayat keputusan dalam sesi

| Urutan | Peristiwa | Akibat |
|---|---|---|
| 1 | Pengguna menyetujui arah perbaikan hasil audit | RF-01–12 dan tindak lanjut terkait dicatat sebagai pekerjaan |
| 2 | Pengguna meminta database diprioritaskan, ringan dan tidak overengineering | Penataan schema/index/query/aturan data dimajukan |
| 3 | Pengguna meminta penjelasan Artikel/Refleksi/Refleksi saya dalam bahasa sederhana | Kontrak publik/pribadi dan hak role dituangkan pada bagian 5 |
| 4 | Pengguna meminta seluruh arah dicatat agar temuan/rencana/implementasi tidak disalahpahami | Dokumen induk dan register RFIX disusun |
| 5 | Pengguna menegaskan data develop boleh dibuang dan dibentuk ulang dengan seeder | Syarat pelestarian/upgrade dataset develop lama dicabut; rebuild dan RFIX-23 menjadi bagian wajib |
| 6 | Pengguna menegaskan schema belum final dan masih dicari/dikembangkan | Struktur fisik baseline dibuka untuk evaluasi; kebutuhan fungsi dan pilihan schema dipisahkan, dengan pemilihan per siklus |

### 16.3 Status akhir pencatatan ini

Dokumen telah mencatat arah dan kriteria pekerjaan. Pembuatan dokumen ini belum mengubah source aplikasi, menjalankan migration/reset/seeder, memperbaiki dependency, atau menerapkan perubahan pada server. Temuan awal tetap terbuka sampai masing-masing pekerjaan memiliki bukti implementasi dan verifikasi.
