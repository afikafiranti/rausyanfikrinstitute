# Audit dan Risiko Aktif

Pembaruan 4 November 2026. Source yang diperiksa: bd9cdc1bc728916060e50d4566eccc64a172df9b; perubahan dokumentasi ini tidak mengubah aplikasi atau schema. Temuan aplikasi di bawah masih terbuka.

Rincian bukti bertanggal ada di [snapshot audit 3 November](AUDIT_REPOSITORI_2026-11-03.md). Keputusan/prioritas terbaru ada pada [dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md); progres implementasi hanya di [TASK](TASK.md).

## Daftar risiko aktif

| ID | Kondisi source yang dikonfirmasi | Jenis bukti dan batas | Prioritas | Tindak lanjut |
|---|---|---|---|---|
| RF-01 | Nama alumni berada dalam string JavaScript onsubmit konfirmasi hapus | Source + uji handler terisolasi; belum E2E Laravel/browser | P0 | RFIX-01 |
| RF-02 | Laravel 12.28.1/Symfony 7.3.x dan paket runtime lain cocok dengan advisory | Pencocokan lockfile/Packagist 3 November; bukan bukti semua jalur dapat dieksploitasi | P1 | RFIX-10 |
| RF-03 | Audit npm baseline mencatat 19 entri paket terdampak | Bertanggal; dua entri critical terkait rantai dependency | P1 | RFIX-11 |
| RF-04 | FK kajian_reports.user_id masih cascadeOnDelete; akun dihapus permanen | Source/schema; kebijakan perilaku setelah aplikasi dipakai perlu ditetapkan | P1 | RFIX-03/22 |
| RF-05 | Edit profil kritis menjadi pending; self-delete tanpa penjaga pengelola terakhir | Source; dampak tergantung susunan pengelola | P1 | RFIX-08 |
| RF-06 | Edit wilayah/angkatan dapat mengubah suspended menjadi pending | Source pada profil sendiri dan edit admin; bukan aktivasi otomatis | P1 | RFIX-09 |
| RF-07 | CI baseline success dengan 280 warnings, 7 passed, 2172 assertions | Log CI; penyebab warning belum didiagnosis penuh | P1 | RFIX-12 |
| RF-08 | Pemakaian APP_KEY historis belum dinilai tuntas | Catatan historis; tidak menyatakan kunci produksi aktif bocor | P1 kondisional | RFIX-13 |
| RF-09 | ApexCharts dimuat dari CDN tanpa versi; package memasang Chart.js | Source view/config; kegagalan grafik belum diuji ulang pada browser | P2 | RFIX-14 |
| RF-10 | User::hasRole memakai roles()->exists meski relasi daftar sudah dimuat | Source; estimasi query bukan benchmark | P1, dimajukan untuk DB/kode | RFIX-07 |
| RF-11 | Index users(wilayah_id,level_id,status) didefinisikan tiga kali; kandidat index berlebih lain ada | Migration source; schema terpasang tidak diperiksa melalui koneksi DB | P1, dimajukan untuk DB | RFIX-04 |
| RF-12 | Perubahan judul Post dapat membentuk slug baru tanpa pemetaan slug lama | Source model/detail route | P2 | RFIX-15 |

## Pemeriksaan source 4 November

Pemeriksaan statis mengonfirmasi:
- 55 berkas PHP aplikasi, 26 controller, 24 migration, 21 tabel bernama pada source dan 42 berkas PHP tests.
- Inline nama di konfirmasi hapus, cascade penulis laporan, transisi pending, CDN grafik, query role dan pembuatan slug masih ada.
- Route laporan menolak alumni, tetapi StoreKajianReportRequest dan helper visibleReports masih menyebut alumni. Ini ketidaksamaan kontrak internal; bukan bukti endpoint alumni sudah terbuka.
- VideoProgress belum menjadi fitur pengguna, tetapi dirujuk Video::progresses serta dua tes MateriManagementTest. Penghapusan kandidat tabel harus menyesuaikan referensi itu.
- Dokumen lama menyebut Chart.js; dashboard aktif memakai ApexCharts CDN. Rencana library akhir belum dipilih.

## Pemaknaan kondisi develop

Data develop lama boleh direset. Tidak diperlukan penyelamatan atau upgrade dataset lama untuk merancang schema. Risiko RF-04 tetap menyangkut aturan hapus/rekap setelah sistem digunakan; ia tidak menjadi larangan reset develop yang sudah diizinkan.

Struktur DB belum final. Baseline 21 tabel dan nama model bukan keputusan desain akhir. Kandidat schema, constraint, index dan seeder diuji menurut kebutuhan fungsi, bukan mengikuti target jumlah tabel semata.

## Bukti pengujian baseline

[CI 39086196915](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915) pada bd9cdc1bc728916060e50d4566eccc64a172df9b completed/success, dengan 280 warnings, 7 passed dan 2172 assertions. Log ringkas memotong sejumlah pesan file_get_contents; jangan menetapkan akar masalah tanpa output lengkap.

Build frontend dan pengaman runner lokal telah diperiksa pada audit awal. PHP/Composer/database/Chromium lokal tidak tersedia pada sesi audit tersebut; suite aplikasi tidak dijalankan ulang saat sinkronisasi dokumentasi. Audit dependency 43 advisory unik/12 paket PHP runtime dan 19 entri npm tetap merupakan hasil 3 November, bukan audit baru 4 November.

## Batas fitur yang tidak dihitung sebagai bug baru

- Homepage ringkas memang tidak memasukkan agenda/tim-kontak.
- Progres video, metadata otomatis dan CRUD web master belum diimplementasikan.
- Laporan rejected belum memiliki revisi/kirim ulang.
- Galeri disimpan langsung publik; verifikasi email belum menjadi syarat umum modul.
- Statistik alumni memasukkan seluruh akun dalam scope; widget jumlah kajian bulanan masih menyatakan belum tersedia.
- Refleksi publik tetap dapat dibuka melalui URL; navigasi publik aktif belum menautkannya.
- Draf Refleksi saya hanya penulis; pengiriman internal tidak mempublikasikannya.

## Perubahan status audit

Temuan ditutup hanya dengan commit implementasi dan bukti penerimaan terkait. Catat kapan temuan hanya didokumentasikan, kapan patch tersedia, dan kapan benar-benar terverifikasi. Jangan mengubah semua prioritas/cakupan berdasarkan snapshot lama.

Riwayat sebelum sinkronisasi: [AUDIT baseline](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/docs/aturan_pengembangan/AUDIT.md). Guardrail lama tentang pelestarian data develop/migration lama diganti oleh arahan eksplisit terbaru; riwayat tersebut tetap berguna sebagai bukti tanggalnya.
