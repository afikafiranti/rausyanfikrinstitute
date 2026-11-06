# Peta Dokumentasi Pengembangan

Gunakan satu acuan untuk setiap jenis informasi. Dokumen lain menautkan ke acuan tersebut; jangan menyalin status, angka uji, aturan produk, atau langkah operasional ke beberapa tempat.

## Dokumen aktif dan tanggung jawab

| Dokumen | Sumber tunggal untuk | Bukan tempat untuk |
|---|---|---|
| [PRD](PRD.md) | Fungsi produk, pengguna, izin, status bisnis dan batas fitur | Progres RFIX atau hasil pengujian |
| [DESIGN](../../DESIGN.md) | Arah visual, warna, tipografi, komposisi dan gerak | Daftar fitur, progres atau hasil QA |
| [Dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md) | Arahan ARAH, keputusan KEP, pilihan schema dan spesifikasi/kriteria RFIX | Register progres, salinan PRD atau log eksekusi |
| [TASK](TASK.md) | Status pekerjaan, baseline/commit, hasil penerapan, perintah dan bukti verifikasi | Prosedur umum atau penetapan fitur baru |
| [PLAN](PLAN.md) | Urutan pekerjaan dan dependensi | Salinan register status atau spesifikasi lengkap |
| [AUDIT](AUDIT.md) | Temuan RF, penilaian risiko, triase dan keterbatasan bukti | Log uji berulang atau daftar fitur |
| [SOP](SOP.md) | Instalasi develop, keamanan target DB, verifikasi dan cara mencatat hasil | Hasil suatu run atau prosedur deployment server |
| [Runbook staging dan pemulihan](RUNBOOK_STAGING_JALUR_B.md) | Prosedur operator pada server, backup, deployment dan restore | Bukti bahwa rilis sudah dijalankan |
| [AGENTS](../../AGENTS.md) | Cara agen memulai sesi, kepemilikan dan koordinasi berkas | Kontrak produk atau rekap progres |

README di akar repositori menjadi pintu masuk proyek. Lockfile dan konfigurasi executable menjadi acuan versi dependency; spesifikasi perubahan tidak menggantikannya.

## Urutan baca

1. Baca AGENTS, [ringkasan TASK](TASK.md#ringkasan-status-saat-ini) dan [rencana aktif](PLAN.md#rencana-aktif) agar tidak mengulang pekerjaan selesai.
2. Baca bagian PRD, keputusan KEP dan RFIX yang terkait tugas; periksa AUDIT untuk risiko yang relevan.
3. Ikuti SOP saat menjalankan pekerjaan. Untuk lingkungan server, lanjutkan ke runbook.

## Aturan pembaruan

- Perubahan kebutuhan pengguna: perbarui PRD dan keputusan ARAH/KEP yang terkait.
- Perubahan spesifikasi atau syarat selesai: perbarui RFIX di dokumen induk; ubah PLAN hanya bila urutan/dependensinya berubah.
- Hasil implementasi/pengujian: catat di TASK. Perbarui AUDIT hanya untuk penilaian risikonya, dengan rujukan ke bukti TASK.
- Perubahan prosedur: perbarui SOP atau runbook sesuai lingkungannya.
- Perubahan pedoman visual: perbarui DESIGN; kebutuhan interaksi produk tetap di PRD.
- Perbaikan tautan/struktur dokumen tidak berarti tes aplikasi dijalankan ulang.
- Jika informasi bertentangan, cocokkan instruksi pengguna terbaru, source, dan bukti bertanggal, lalu perbaiki dokumen pemilik informasi. Jangan menambah ringkasan tandingan.

## Arsip dan bukti historis

Berkas di bawah mempertahankan kejadian pada tanggal/commit yang disebut. Pekerjaan, angka uji dan aturan lama di dalamnya bukan instruksi atau status aktif.

| Arsip | Isi |
|---|---|
| [Catatan Jalur A](CATATAN_JALUR_A.md) dan [Jalur B](CATATAN_JALUR_B.md) | Kronologi implementasi/integrasi dan dokumen sebelum RFIX |
| [Audit repositori 3 November](AUDIT_REPOSITORI_2026-11-03.md) | Temuan baseline beserta sumber dan batas pemeriksaan |
| [Audit UI/UX 18 Oktober](AUDIT_UI_UX_2026-10-18.md) | Pemeriksaan antarmuka pada snapshot tersebut |
| [QA Jalur B B25](QA_JALUR_B_B25.md) | Bukti QA browser baseline |
| [Bukti pemulihan B26](BUKTI_PEMULIHAN_B26.md) | Hasil drill pemulihan baseline |
| [CHANGELOG skeleton Laravel](../../CHANGELOG.md) | Riwayat bawaan template, bukan catatan rilis aplikasi |

Path arsip dipertahankan agar rujukan lama tetap dapat dibuka. Riwayat Git juga menyimpan versi dokumen sebelumnya. Folder ../diskusi berisi bahan diskusi pengguna, bukan register atau prosedur aktif.
