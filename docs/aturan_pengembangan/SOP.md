# SOP Pengembangan dan Sinkronisasi

Pembaruan 4 November 2026. Berlaku untuk rencana RFIX dan menggantikan aturan historis yang bertentangan tentang pelestarian dataset develop, perubahan migration lama dan kewajiban bekerja paralel.

## 1. Masuk sesi dan pilih pekerjaan

1. Baca AGENTS, [dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md), [TASK](TASK.md), [PLAN](PLAN.md), [PRD](PRD.md), [AUDIT](AUDIT.md) dan SOP ini.
2. Periksa branch/status dan baseline source; pertahankan perubahan pengguna/pengembang lain.
3. Pilih ID RFIX, peta berkas/domain dan kriteria penerimaannya. Ubah TASK menjadi IN_PROGRESS ketika pelaksanaan dimulai.
4. Gunakan kondisi kode sebagai bukti. Jika source sudah berubah, perbarui penilaian sebelum mengerjakan temuan lama.
5. Kerjakan perubahan minimum yang menyelesaikan alur. Jangan mengulang pembangunan fitur yang sudah ada atau menambahkan fitur di luar kebutuhan.

## 2. Perancangan database develop

- Skema belum final. Bandingkan kandidat menurut kebutuhan web, privasi, integritas, kesederhanaan kode dan pengukuran.
- Nama/jumlah tabel, kolom, relasi dan migration baseline dapat berubah. Kontrak pengguna tidak boleh melemah akibat perubahan fisik schema.
- Data develop yang ada boleh dibuang. Tidak perlu jalur upgrade, backfill, atau penyelamatan dataset lama develop.
- Migration awal boleh ditata/digabung untuk membentuk schema dari kosong. Seeder/factory ikut diperbarui dan menjadi bagian syarat selesai.
- Tentukan satu sumber aturan data yang jelas; gunakan kemampuan Laravel yang tersedia sebelum menambah lapisan/dependency.
- Tabel framework dievaluasi bersama driver; tabel fitur tertunda diperiksa seluruh pemakainya sebelum dikeluarkan.
- Artikel dan Refleksi publik adalah dua jenis tulisan untuk publik. Refleksi saya mempunyai draf pribadi dan pengiriman internal; pilihan tabel tidak boleh mencampurkan pembacanya.

## 3. Reset develop yang sudah diizinkan

Izin pengguna untuk reset data develop sudah ada dan tidak perlu ditanyakan ulang untuk target proyek yang telah teridentifikasi.

Sebelum menjalankan perintah destruktif, periksa host, database, environment dan konfigurasi yang benar-benar dimuat, termasuk config cache. Jangan mengubah label environment untuk melewati pemeriksaan target. Izin ini tidak berlaku untuk produksi atau sistem lain.

Pada target develop yang telah benar, prosedur rebuild yang direncanakan:

~~~bash
php artisan migrate:fresh --seed
php artisan db:seed
php artisan route:list
~~~

Pembentukan dari kosong dan pengulangan seeder harus berhasil tanpa duplikasi data referensi/fixture. Data contoh terbatas local/testing, memakai identitas sintetis dan tidak dianggap konten resmi.

Pengaman pengujian tetap terpisah: database _testing, TestDatabaseSafety dan pemeriksaan sebelum RefreshDatabase. Izin reset develop tidak menjadi alasan menonaktifkan pengaman test atau berbagi database test antar pelaksana.

## 4. Implementasi dan koordinasi

1. Reproduksi masalah atau ukur kondisi awal sesuai risiko. Uji regresi dibuat untuk logika berisiko yang diubah.
2. Selaraskan migration, model, relasi, validasi, query, seeder, factory, view dan tes yang terdampak.
3. Pertahankan transaksi, row lock, audit, CSRF, scope wilayah, multi-role dan privasi.
4. Hindari cache izin global yang dapat menyimpan role lama; hindari query berulang di view.
5. Peta domain A/B dipakai saat ada pekerjaan bersama. Jangan menimpa edit orang lain atau memformat ulang seluruh file lintas domain.
6. Instruksi ini tidak mewajibkan sub-agent atau paralelisasi. Ikuti penugasan/lingkungan yang sedang berlaku.
7. Perubahan keputusan fungsi dicatat pada PRD dan dokumen induk; pilihan teknis serta hasilnya dicatat dengan ID KEP/RFIX yang sesuai.

## 5. Verifikasi

- Target bersama PHP 8.3 dan MySQL/MariaDB; gunakan lockfile yang sesuai.
- Jalankan pemeriksaan domain yang relevan, kemudian integrasi yang terdampak. Tidak perlu tes baru untuk perubahan rendah dampak yang hanya menyalin implementasi.
- Build aset tidak membuktikan perilaku browser/permission. Tes PHP tidak membuktikan handler JavaScript.
- Uji rebuild schema kosong, seed ulang, keunikan/FK, status, hak akses dan fungsi pengguna sebelum menyebut penataan DB selesai.
- Audit dependency dilaporkan dengan waktu, versi, ID advisory dan applicability. Angka severity tidak langsung menjadi bukti serangan ke endpoint.
- Tampilkan rincian warning dan perbaiki penyebabnya; jangan menyembunyikannya agar CI hijau.
- Catat perintah, commit, runtime, lingkungan, hasil, warning dan skip. Jangan menggunakan angka tes lama untuk patch baru.
- Bukti staging, SMTP eksternal dan pemulihan sebelum live dipisahkan dari pekerjaan develop. Ketidaktersediaannya tidak menghalangi perancangan schema.

## 6. Pencatatan dan penutupan

TASK adalah satu register progres aktif. Dokumen induk menyimpan keputusan dan kriteria. AUDIT menyimpan risiko/bukti; PLAN menyimpan urutan; PRD menyimpan fungsi/cakupan; catatan A/B menyimpan detail pelaksanaan.

Status PARTIAL berarti ada subtugas selesai tetapi pekerjaan belum memenuhi seluruh kriteria, bukan tanda pekerjaan sedang dikunci pelaksana. DONE memerlukan bukti kriteria; deployment memerlukan bukti lingkungan tujuan.

Perbarui README jika ringkasan fitur/setup berubah. Saat terjadi perbedaan, periksa source dan instruksi pengguna terbaru, kemudian perbaiki pencatatan yang salah. Jangan menumpuk status aktif yang bertentangan di beberapa dokumen.

## 7. Skill project dan sumber teknis

Baca instruksi project skill pada .agents/skills saat relevan: diagnosing-bugs untuk diagnosis; tdd untuk perubahan berisiko; implement untuk pelaksanaan spesifikasi; code-review untuk review; ui-ux-pro-max/ui-styling untuk UI; qa/browser-use/remote-browser untuk QA browser; research untuk riset teknis sumber primer. Ketersediaan skill tidak mengharuskan perubahan stack atau pemasangan dependency.

Jangan menjalankan konfigurasi penuh ekosistem skill, integrasi gateway, atau mengirim kredensial/data nyata ke layanan browser/gateway hanya karena tool tersebut tersedia. Ikuti cakupan tugas dan instruksi pengguna.

## 8. Riwayat

[SOP baseline 18 Oktober](https://github.com/afikafiranti/rausyanfikrinstitute/blob/bd9cdc1bc728916060e50d4566eccc64a172df9b/docs/aturan_pengembangan/SOP.md) menyimpan prosedur historis lengkap. Larangan reset dataset develop dan perubahan migration lama di sana telah diganti arahan pengguna 4 November. Prosedur perlindungan lingkungan lain serta pemulihan sebelum penggunaan nyata tetap diperiksa sesuai konteks.
