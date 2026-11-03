# Plan Pengembangan

## Rencana Aktif

Pembaruan 4 November 2026. Arah dan kriteria lengkap ada di [dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md); status pekerjaan berjalan hanya di [TASK](TASK.md#register-rfix). [PRD](PRD.md) menjelaskan fungsi, [AUDIT](AUDIT.md) menjelaskan bukti risiko, dan [SOP](SOP.md) menjelaskan pelaksanaan.

### Titik mulai agent

1. Periksa branch/status dan sinkronkan dari develop yang memuat dokumen ini.
2. Tangani RFIX-01 (P0) sebagai perubahan kecil tersendiri: konfirmasi hapus tidak boleh mengevaluasi nama sebagai JavaScript.
3. Mulai RFIX-02 pada tahap awal: petakan kebutuhan dan nilai kandidat schema, kemudian bentuk serta uji melalui migration/seeder.
4. Gunakan rincian RFIX pada dokumen induk. Jangan menghabiskan sesi untuk membuat rencana baru yang mengulang spesifikasi ini ketika pengguna sudah meminta implementasi.

### Keputusan database yang berlaku

- Skema saat ini belum final. Nama/jumlah tabel, kolom, model, relasi dan migration dapat berubah.
- Data develop lama boleh dibuang dan dibuat ulang. Tidak diperlukan pelestarian, backfill atau jalur upgrade dataset develop lama.
- Migration boleh disusun ulang/dikonsolidasikan untuk membentuk schema dari kosong; seeder menjadi bagian wajib.
- Pilih struktur menurut kebutuhan web, privasi, integritas, kesederhanaan kode dan pengukuran. Jumlah tabel baseline bukan target akhir.
- Pisahkan kebutuhan fungsi dari pilihan fisik tabel. Artikel/Refleksi publik dan Refleksi saya tetap memiliki pembaca serta alur berbeda, meskipun rancangan penyimpanan masih dievaluasi.
- Izin reset develop sudah ada; pelaksana memeriksa identitas target, bukan meminta persetujuan ulang. Testing tetap terpisah dan pengamannya dipertahankan.
- Tabel framework/fitur tertunda ditinjau berdasarkan pemakaian. Jika tabel dikeluarkan, relasi/model/tes/seeder terkait ikut diperbarui.

### Tahap dan dependensi

| Tahap | Pekerjaan | Hasil |
|---|---|---|
| A | RFIX-01 dan RFIX-02 | Celah konfirmasi ditutup; kebutuhan dan kandidat schema diketahui |
| B | RFIX-03–09, RFIX-18, RFIX-23 dan RFIX-22 | Schema terpilih per siklus, akun/relasi/index/query konsisten, rebuild dan seeder terbukti |
| C | RFIX-10–13; triase dapat dimulai sejak tahap A | Dependency dan warning ditangani; pemakaian kunci historis dinilai sesuai lingkungan |
| D | RFIX-14–17 serta RFIX-21 | Grafik, URL, ekspor dan antarmuka memenuhi target |
| E | RFIX-19/20/22 | Dokumentasi aktual dan bukti operasional/pemulihan sebelum penggunaan nyata |

ID RFIX bukan urutan ketergantungan yang kaku. Skema dipilih setelah kebutuhan/kandidat dibandingkan. P0 tidak perlu menunggu seluruh rancangan DB. Keterbatasan staging/SMTP tidak menghalangi perapian develop.

### Perubahan yang tidak dimulai otomatis

Migrasi framework, penulisan ulang aplikasi, penambahan agenda/tim-kontak, progres menonton, metadata YouTube otomatis, revisi laporan rejected, CRUD master baru dan publikasi otomatis jurnal pribadi bukan bagian perbaikan ini tanpa keputusan kebutuhan baru.

### Koordinasi berkas

Peta A/B pada AGENTS dipakai ketika ada penugasan atau pekerjaan bersama. Perubahan schema yang menyentuh beberapa domain memperbarui seluruh pemakainya dalam changeset yang jelas. Jangan menimpa perubahan pengguna/pengembang lain atau memformat ulang seluruh blok route.

### Bukti selesai

Setiap RFIX harus memenuhi target, tes dan batas yang disebut pada dokumen induk. Kode, schema, data seeder dan pencatatan harus saling sesuai. Test/build lama tidak dipakai sebagai hasil patch baru. Baca [matriks penerimaan](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md#12-matriks-pengujian-penerimaan).

## Tindak Lanjut Setelah Integrasi

Bagian ini dipertahankan agar tautan lama tetap sah. Fitur hasil integrasi A/B 18 Oktober sudah tersedia di develop; tindak lanjut sekarang adalah RFIX pada Rencana Aktif di atas. Tidak mengulang task yang sudah terintegrasi dan tidak memakai urutan PUB-01/PUB-02 historis sebagai perintah baru.

Riwayat rincian: [PLAN baseline](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/docs/aturan_pengembangan/PLAN.md), [Catatan A](CATATAN_JALUR_A.md), [Catatan B](CATATAN_JALUR_B.md). Bukti rilis tetap terpisah dari keberadaan fitur.
