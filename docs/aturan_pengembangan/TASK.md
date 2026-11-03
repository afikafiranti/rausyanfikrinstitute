# Task Log Pengembangan

## Ringkasan Status Saat Ini

Pembaruan 4 November 2026. Basis kode aplikasi: bd9cdc1bc728916060e50d4566eccc64a172df9b. Dokumen induk dan pencatatan aktif disimpan atas instruksi pengguna. Tidak ada patch aplikasi, schema baru, reset database atau eksekusi seeder pada sinkronisasi dokumentasi ini.

Satu register progres aktif adalah dokumen ini. Rincian langkah dan kriteria setiap pekerjaan ada pada [dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md#9-rincian-pekerjaan). [PLAN](PLAN.md#rencana-aktif) menetapkan urutan; [AUDIT](AUDIT.md) menetapkan status bukti.

### DONE — DOK-SYNC-2026-11-04

- Dokumen induk dipindahkan ke repo agar dapat dibaca agent tanpa akses ChatGPT Library.
- Snapshot audit disertakan sebagai berkas repo; tautan internal diperiksa.
- AGENTS/README/PRD/PLAN/TASK/AUDIT/SOP diselaraskan dengan arahan terbaru dan source baseline.
- Penataan schema masih terbuka; izin reset data develop dan kewajiban seeder dicatat.
- Pemeriksaan source mengonfirmasi temuan belum ditutup; tidak mencatat implementasi yang belum terjadi.
- Verifikasi dokumentasi: struktur tabel Markdown, referensi RFIX/ARAH/KEP, tautan berkas/anchor, kesesuaian bukti source, dan perubahan hanya dokumentasi.
- Penyusunan dan verifikasi dokumen selesai; berkas disimpan pada develop dalam dua commit dokumentasi melalui browser GitHub. Verifikasi remote memastikan isi dan cakupan perubahannya.
- Subtugas pencatatan RFIX-20 selesai; pembersihan aset/template dan penetapan lisensi tetap pekerjaan tersendiri. Status keseluruhan RFIX-20 PARTIAL.

### Register RFIX

| ID | Pekerjaan | Prioritas | Domain koordinasi | Status | Catatan kondisi kode |
|---|---|---|---|---|---|
| RFIX-01 | Amankan konfirmasi hapus alumni | P0 | A+B | TODO | RF-01; nama dalam JavaScript masih ada |
| RFIX-02 | Petakan kebutuhan dan bandingkan kandidat schema | P1 | Integrasi | TODO | Schema belum final; data develop boleh dibuat ulang |
| RFIX-03 | Atur hapus akun, laporan dan file | P1 | A+B | TODO | RF-04; kebijakan KEP-01/02 dan kandidat schema |
| RFIX-04 | Konsolidasikan index yang berlebih | P1 | Integrasi | TODO | RF-11; schema baru boleh disusun dari kosong |
| RFIX-05 | Selaraskan kolom, constraint dan validasi | P1 | A+B | TODO | Hasil RFIX-02; keputusan kebutuhan data |
| RFIX-06 | Tinjau schema tertunda dan referensinya | P1 | A+B | TODO | video_progress masih punya relasi dan tes |
| RFIX-07 | Kurangi query role berulang | P1 | A+B | TODO | RF-10; gunakan pengukuran dan tes izin |
| RFIX-08 | Lindungi pengelola aktif terakhir | P1 | A | TODO | RF-05; definisi pengelola dan KEP-03 |
| RFIX-09 | Jaga suspended saat profil berubah | P1 | A | TODO | RF-06; jalur profil sendiri dan edit admin |
| RFIX-10 | Triase dan perbarui dependency PHP | P1 | B+A | TODO | RF-02; audit bertanggal dan runtime PHP 8.3 |
| RFIX-11 | Triase dan perbarui dependency frontend | P1 | B | TODO | RF-03; browser/build/development dibedakan |
| RFIX-12 | Bersihkan warning dan perjelas CI | P1 | B+A | TODO | RF-07; rincian warning belum didiagnosis penuh |
| RFIX-13 | Nilai pemakaian kunci historis | P1 kondisional | Operator+B | TODO | RF-08; bukan bukti kunci produksi aktif bocor |
| RFIX-14 | Kunci library grafik dan tangani gagal | P2 | B | TODO | RF-09; KEP-04 |
| RFIX-15 | Pertahankan URL tulisan saat edit judul | P2 | B | TODO | RF-12; slug masih dapat berubah |
| RFIX-16 | Ukur dan tetapkan batas ekspor XLSX | P2 | A+B | TODO | Workbook masih memakai memori; KEP-05 |
| RFIX-17 | Rapikan form dan interaksi responsif | P2 | A+B | TODO | Form profil ganda dan fokus modal |
| RFIX-18 | Selaraskan kontrak izin laporan | P1 | A+B | TODO | Route menolak alumni; helper/request masih menyebut alumni |
| RFIX-19 | Buktikan email dan operasional target | P1 rilis | Operator+A+B | TODO | Staging/SMTP eksternal belum ada bukti lulus |
| RFIX-20 | Selaraskan dokumentasi dan arsip teknis | P2/P3 | Integrasi | PARTIAL | Dokumen aktif sinkron; aset/template dan lisensi belum ditata |
| RFIX-21 | Perjelas istilah publik dan pribadi | P2 | B | TODO | Kontrak dicatat; perubahan teks/menu aplikasi belum dibuat |
| RFIX-22 | Buktikan rebuild, integrasi dan pemulihan | P1 | Integrasi | TODO | Tidak mewajibkan upgrade dataset develop lama |
| RFIX-23 | Lengkapi seeder/factory develop | P1 | A+B | TODO | Seeder baseline belum memenuhi semua fixture rencana |

### Arti status

- TODO: pekerjaan yang tersisa belum diimplementasikan.
- IN_PROGRESS: changeset sedang dikerjakan; catat branch, pelaksana dan commit awal agar tidak tumpang tindih.
- PARTIAL: ada bagian selesai dengan bukti, tetapi keseluruhan pekerjaan belum memenuhi kriteria; bukan tanda ada pelaksana yang masih memegang lock.
- REVIEW: implementasi dan tes relevan tersedia, menunggu pemeriksaan hasil.
- BLOCKED: kebutuhan atau keputusan spesifik menghalangi langkah terkait; sebutkan tepatnya.
- DONE: kriteria penerimaan terpenuhi dan bukti commit/perintah/lingkungan dicatat.

Persetujuan rilis/penerapan tidak disimpulkan dari DONE sebuah perubahan kode. TASK, audit dan catatan jalur tidak boleh menyebut test berhasil bila belum dijalankan.

## Kondisi implementasi baseline

Fitur akun/alumni/materi, laporan, homepage ringkas, dashboard alumni, Refleksi saya dan pembatasan Post/Galeri sudah tersedia pada develop hasil integrasi 18 Oktober. Tidak mengulang A0–A5/PUB-03/PUB-04/UIUX-02 sebagai pembangunan fitur baru.

Bukti CI yang diperiksa pada source baseline: [run 39086196915](https://github.com/afikafiranti/rausyanfikrinstitute/actions/runs/39086196915) completed/success; ringkasan 280 warnings, 7 passed, 2172 assertions. Ini bukti baseline, bukan tes patch RFIX.

## Pembaruan progres berikutnya

Setiap perubahan RFIX mengisi: branch/commit, target develop/test/staging, keputusan KEP, file/schema berubah, seeder yang dipakai, perilaku sebelum/sesudah, perintah uji dan hasil/warning/skip. Gunakan [format dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md#14-catatan-perubahan-per-pekerjaan). Catatan A/B menyimpan detail pelaksanaan; status aktif tetap satu pada register di atas.

## Riwayat sebelum rencana RFIX

[Versi TASK pada baseline 18 Oktober](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/docs/aturan_pengembangan/TASK.md) mempertahankan seluruh task A/B/PUB dan bukti historis. [Catatan A](CATATAN_JALUR_A.md) dan [Catatan B](CATATAN_JALUR_B.md) tetap tersedia. Label lama “belum merge”, “CI belum”, larangan reset develop, dan urutan agenda/tim tidak menggantikan arahan aktif.
