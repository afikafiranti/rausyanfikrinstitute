# Audit dan Risiko Aktif

Penilaian dasar per 6 November 2026; tambahan alur Laporan 7 November. [TASK](TASK.md#register-rfix) menjadi satu-satunya register penyelesaian dan [bukti eksekusi](TASK.md#verifikasi-aplikasi-terakhir). Dokumen ini mempertahankan asal temuan, triase, serta batas kesimpulan; keberhasilan lokal tidak membuktikan kondisi server.

## Jenis dan batas bukti

| Kode | Sumber | Batas kesimpulan |
|---|---|---|
| B1 | Source dan uji mekanisme terisolasi | Mekanisme tertentu terbukti, belum seluruh alur aplikasi |
| B2 | Source, migration dan penelusuran alur | Tidak sama dengan reproduksi pada DB/server tujuan |
| B3 | Output CI/perintah aktual | Berlaku pada commit dan lingkungan yang dicatat |
| B4 | Pencocokan versi dengan advisory | Keterjangkauan serangan ditriase tersendiri |
| B5 | Catatan historis | Pemakaian saat ini harus diperiksa sebelum tindakan server |
| B6 | Perilaku/batas produk terdokumentasi | Tidak otomatis menjadi bug |
| B7 | Arahan perapian/inventarisasi | Bukan bukti kerusakan data telah terjadi |

## Asal temuan dan pengendalian

Tabel ini memetakan alasan pekerjaan, bukan menyalin statusnya. Detail baseline berada pada [audit repositori 3 November](AUDIT_REPOSITORI_2026-11-03.md); spesifikasi tindakan berada di dokumen induk dan hasilnya di TASK.

| Temuan | Mekanisme atau paparan awal | Bukti awal | Pekerjaan/pengendalian |
|---|---|---|---|
| RF-01 | Nama alumni disisipkan ke JavaScript konfirmasi hapus | B1/B2 | RFIX-01: hilangkan interpolasi ke kode |
| RF-02 | Versi dependency PHP cocok advisory | B4 | RFIX-10: patch sesuai paparan dan audit lockfile |
| RF-03 | Versi dependency frontend/tooling cocok advisory | B3/B4 | RFIX-11: patch dan triase rantai transitif |
| RF-04 | Cascade akun menghapus laporan; file dapat tertinggal | B2 | RFIX-03/22: kebijakan retensi, FK dan pemulihan |
| RF-05 | Perubahan profil/self-delete menghilangkan pengelola terakhir | B2, tergantung susunan role | RFIX-08: guard transaksi dan uji beberapa koneksi |
| RF-06 | Perubahan profil mengganti suspended menjadi pending | B2 | RFIX-09: pertahankan status dan pesan nyata |
| RF-07 | CI sukses dengan warning dan gate audit belum eksplisit | B3 | RFIX-12: detail keluaran/gate serta bukti remote |
| RF-08 | Pemakaian APP_KEY historis belum teridentifikasi | B5 | RFIX-13: inventaris lingkungan dan dampak enkripsi |
| RF-09 | Grafik CDN tanpa versi terkunci | B2 | RFIX-14: bundle lockfile dan penanganan gagal |
| RF-10 | Query role berulang walau relasi dimuat | B2, belum benchmark saat audit awal | RFIX-07: gunakan relasi untuk tampilan dan ukur |
| RF-11 | Index bersusunan sama dibuat berulang | B2, belum inventaris DB saat audit awal | RFIX-04: konsolidasi berdasarkan pemakaian |
| RF-12 | Edit judul mengganti slug tulisan terbit | B2 | RFIX-15: pertahankan URL yang dibagikan |

Perapian schema/fixture (RFIX-02/05/06/23/24) memerlukan inventaris dan regresi, bukan asumsi data telah rusak. Kontrak laporan yang berbeda antara helper/request dan route (RFIX-18) bukan bukti alumni berhasil menembus endpoint. Batas workbook/UX (RFIX-16/17), operasional (RFIX-19/22) dan dokumentasi/aset (RFIX-20) dinilai sesuai lingkungan dan kebutuhan, bukan hanya jumlah berkas/tabel.

Penyatuan halaman Laporan (RFIX-17) memakai scope/filter bersama untuk daftar dan ekspor; mutasi tetap memeriksa izin pada server. Pengelola biasa menghapus draf sendiri; super admin aktif mendapat pengecualian edit/hapus semua status dengan audit dalam transaksi yang sama, row lock dan pembersihan foto setelah commit. Pengecualian ini tidak mengubah aturan kirim/self-review atau memperluas pengelola lain. Ekspor Excel mengikuti batas volume KEP-05 dan teks sel harus diperlakukan sebagai data, bukan rumus. Bukti regresi dan alur browser tercatat pada [TASK](TASK.md); bukti lokal ini tidak mengubah risiko lingkungan tujuan di bawah.

Penyatuan Materi (RFIX-17) membedakan akses pengelola aktif dan akses belajar anggota pada server. Modal tidak menggantikan pemeriksaan role, level, status terbit atau relasi video–playlist. Penghapusan playlist tetap meminta konfirmasi eksplisit dan menghapus video terkait melalui relasi database; schema dan kelayakan anggota tidak diubah. Hasil regresi serta batas bukti mengikuti [TASK](TASK.md).

Penyatuan Alumni/Verifikasi (RFIX-17) mempertahankan gate, scope wilayah, batas role target, larangan self-review dan transaksi keputusan. Pembukaan modal bukan sumber kewenangan. Daftar tunggal dan ekspor memakai cakupan yang sama; pengurutan multi-role tidak boleh menggandakan akun. Konfirmasi serta toast global tidak menggantikan validasi/mutasi server; pesan dipasang sebagai teks dan toast harus dapat ditutup ketika modal terbuka, termasuk browser tanpa Popover API. Hasil regresi dicatat pada [TASK](TASK.md).

Form samping Artikel/Galeri (RFIX-17) membedakan create/edit dari target server dan input lama, termasuk nilai null. Mode antarmuka tidak menjadi otorisasi. Regresi mempertahankan slug/jadwal publikasi, gambar lama, validasi berkas dan pembersihan file; perpindahan item tidak boleh membuang isian tanpa konfirmasi. Bukti mengikuti [TASK](TASK.md).

## Daftar risiko aktif

- **Dependency tooling.** Residu low npm berasal dari satu advisory `GHSA-gcqx-w48m-w2h4` pada rantai `flowbite → flowbite-datepicker → @rollup/plugin-node-resolve → deepmerge 4.3.1`. Paket terakhir digunakan pada tooling build datepicker; aplikasi tidak memasukkan objek unggahan pengguna ke proses merge tersebut. Registry yang diperiksa pada tanggal penilaian masih menawarkan 4.3.1. Penurunan major Flowbite yang disarankan otomatis tidak diterapkan. Temuan tetap terlihat, dengan gate sesuai runner; triase ini bukan klaim seluruh risiko dependency hilang.
- **CI remote.** Hasil lokal belum membuktikan patch pada runner remote. Akar warning baseline belum dapat ditetapkan dari log ringkas yang memotong pesan file_get_contents; jangan menganggap sudah terdiagnosis.
- **Kunci server.** Pemeriksaan repository/lokal tidak membuktikan kunci mana yang dipakai server. Sebelum rotasi, identifikasi lingkungan, data terenkripsi dan backup kunci; jangan mencetak rahasia atau merotasi tanpa menilai dampaknya.
- **Operasional tujuan.** SMTP organisasi, QA staging, backup/restore serta RTO/RPO server membutuhkan bukti lingkungan tujuan. Drill sintetis lokal tidak menggantikannya.
- **Kapasitas hosting.** Batas XLSX dipilih dari pengukuran lokal pada TASK. Memori/waktu hosting belum diukur; kontrak batas dan alternatif ekspor mengikuti PRD.
- **Identitas/aset.** Lisensi memerlukan keputusan pemilik. Inventaris/pembersihan seluruh aset dan template lama masih perlu diselesaikan.
- **Makna AB.** Kolom `users.ab` berisi iya/tidak, ditambahkan pada commit [57d63b1](https://github.com/afikafiranti/rausyanfikrinstitute/commit/57d63b15172e2882a433570459ed63da5130346b) tanggal 22 November 2025 dengan pesan “status Ab”. PRD, form profil, migration dan commit awal tidak memberikan kepanjangan atau aturan bisnis; pemakaian yang ditemukan hanya penyimpanan profil, filter dan grafik. Filter daftar dihapus dan grafik dashboard diganti Pekerjaan Alumni sesuai permintaan; data profil serta endpoint AB lama dipertahankan tanpa menafsirkan nilainya sebagai kategori lain.

## Perubahan status audit

Nilai penutupan dari kriteria RFIX dan bukti pada TASK; bedakan dokumentasi, patch yang tersedia, verifikasi lokal, dan penerapan target. Batas fitur mengikuti [PRD](PRD.md#cakupan-dan-status-implementasi), keputusan schema mengikuti dokumen induk, dan pengaman develop/tes mengikuti SOP.

[Audit baseline](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/docs/aturan_pengembangan/AUDIT.md) beserta snapshot bertanggal merupakan riwayat. Larangan reset develop lama tidak menggantikan izin aktif yang dibatasi pada target proyek terverifikasi.
