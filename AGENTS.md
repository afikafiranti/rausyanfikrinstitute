# Aturan Agen Pengembangan Rausyan Fikr

## Mulai sesi

1. Jalankan git branch --show-current dan git status --short sebelum mengubah file. Pertahankan perubahan pengguna; jangan reset, clean, atau checkout paksa.
2. Baca [peta dokumentasi dan urutan baca](docs/aturan_pengembangan/README.md#urutan-baca), lalu ambil pekerjaan dari [TASK](docs/aturan_pengembangan/TASK.md#register-rfix) menurut [PLAN](docs/aturan_pengembangan/PLAN.md#rencana-aktif).
3. Periksa source dan penugasan terbaru. Snapshot dev-A/dev-B maupun catatan historis tidak otomatis sama dengan develop. Sinkronkan hanya bila aman; jangan membawa perubahan jalur lain yang belum di-commit.
4. Tandai pekerjaan IN_PROGRESS pada TASK, tentukan berkas yang dimiliki, lalu kerjakan perubahan terkecil yang memenuhi spesifikasi. Jangan mengulang pekerjaan DONE atau menafsirkan permintaan implementasi sebagai permintaan membuat rencana ulang.
5. Instruksi eksplisit pengguna terbaru berlaku lebih dahulu. Terapkan arahan cakupan melalui keputusan di dokumen induk dan kontrak PRD; jangan mengambil kontrak produk dari arsip.

## Batas pelaksanaan

- Ikuti [SOP](docs/aturan_pengembangan/SOP.md) untuk runtime, dependensi, perubahan schema, target database, verifikasi, dan pencatatan. Izin reset develop yang telah diberikan tidak perlu diminta ulang setelah target benar-benar diverifikasi; izin itu tidak berlaku pada produksi atau sistem lain.
- Database tes wajib terpisah dan lolos TestDatabaseSafety sebelum RefreshDatabase. Jangan menonaktifkan pengaman untuk menjalankan tes.
- Gunakan pola/dependensi existing; jangan menambah abstraksi atau fitur di luar tugas. Uji regresi untuk logika berisiko dan jangan mengklaim hasil yang belum dijalankan.
- Jangan menimpa pekerjaan pengembang lain, mengubah nama route/signature/kontrak di luar cakupan, atau memformat ulang seluruh berkas bersama.
- Gunakan tier eksekusi terendah yang andal. Paralelisasi hanya untuk pekerjaan yang independen; pemilik implementasi dan review harus jelas.
- Jangan mencatat rahasia ke Git/log atau mengirim data nyata ke layanan eksternal hanya karena suatu integrasi tersedia.

## Peta domain untuk koordinasi

Peta ini membantu pembagian pekerjaan, bukan pembatas penataan schema yang telah diizinkan.

| Domain | Tanggung jawab |
|---|---|
| A | Auth, alumni, profil, verifikasi, materi, notifikasi; model akun/role/level/wilayah/audit/playlist/video; gate, ScopeWilayah, bootstrap/app.php, routes/auth.php dan tes domain |
| B | Dashboard, grafik, landing, laporan dan pengelolaan konten; model Post/Gallery/KajianReport; layout/navigasi/CSS/JS, toolchain, CI dan tes domain |
| Bersama | Blok routes/web.php, perubahan schema lintas domain, migration/seeder terkait dan dokumentasi kanonis |

Koordinasikan perubahan lintas domain dengan seluruh pemakai kontrak tersebut. Route baru menggunakan nama kelas lengkap bila blok import dimiliki bersama. Status hasil tetap dicatat satu kali pada TASK; rincian teknis yang menjadi persyaratan masuk dokumen induk.
