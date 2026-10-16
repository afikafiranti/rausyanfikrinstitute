# Plan Pengembangan

Dokumen ini menetapkan prioritas, pembagian kerja paralel, dan urutan pengembangan berdasarkan risiko serta dampak terhadap pengguna.

## Rencana Aktif: Dua Orang Bekerja Paralel

Tanggal penetapan: 2026-10-16.

Rencana ini menggantikan urutan dan prioritas tahap lama di bagian referensi modul. Pekerjaan masih berstatus TODO; audit dan penyusunan rencana tidak berarti bug telah diperbaiki.

| Jalur | Penanggung jawab | Hasil utama | Branch |
|---|---|---|---|
| A | Pengembang A (pengguna) | Akses akun dan wilayah aman; data alumni, profil, verifikasi, dan materi konsisten | dev-A |
| B | Pengembang B (rekan pengguna) | Pengujian tim konsisten; dashboard, konten publik, dan laporan dapat dipakai | dev-B |

Penugasan Jalur A dikonfirmasi pengguna pada 2026-10-16. Branch dev-A dibuat dari develop; pekerjaan implementasi A0-A2 masih TODO. Commit dokumentasi pembagian kerja menjadi baseline bersama di develop sebelum pengembang B membuat branch kerjanya.

Aturan pembuka sesi agen disimpan di AGENTS.md pada root repo. Agen pada dev-A mengenali Jalur A; agen pada dev-B mengenali Jalur B. Sesi baru tanpa penugasan di luar dev-A diarahkan ke B, dengan pemeriksaan branch/status sebelum mengedit. Branch dev-B belum dibuat pada workspace ini.

Keduanya mulai dari commit dasar yang sama setelah dokumen pembagian ini masuk ke branch bersama. Tidak perlu menunggu implementasi jalur lain untuk mulai bekerja. Setiap jalur memakai schema dan method publik yang sudah ada; pengujian gabungan dilakukan setelah kedua PR tersedia.

### Prioritas Aktif

P0 wajib ditutup sebelum aplikasi digunakan dengan data nyata. P1 menstabilkan fitur yang sudah terlihat oleh pengguna. P2 melengkapi fitur setelah fondasinya lulus pengujian.

| Prioritas | Jalur A: Pengembang A | Jalur B: Pengembang B |
|---|---|---|
| P0 / segera | A0: batas wilayah dan hak tindakan; akses materi menolak akun tidak memenuhi syarat; seeder akun aman | B0: selaraskan CI/database/PHP; amankan konfigurasi lingkungan; lindungi route dashboard |
| P1 / berikutnya | A1: registrasi dan penetapan wilayah; validasi profil/alumni; audit dan notifikasi; sorting | B1: angka dashboard nyata; upload aman; slug unik; route konten sesuai action; homepage terhubung konten |
| P2 / pengembangan | A2: pengelolaan playlist/video dan penggunaan materi; impor/ekspor alumni | B2: submit dan review laporan sampai rekap/export; QA publik dan kesiapan rilis |

Urutan berlaku di dalam masing-masing jalur: A0 -> A1 -> A2 dan B0 -> B1 -> B2. B tidak perlu menunggu A0 selesai untuk mengerjakan B0/B1. P2 boleh dikembangkan pada database terisolasi setelah P0/P1 jalurnya lulus, tetapi rilis gabungan tetap menunggu semua P0 selesai.

### Jalur A: Akses, Alumni, Profil, Verifikasi, dan Materi

Pemilik: pengembang A (pengguna). Branch: dev-A.

**A0 — P0: tutup celah akses.**

- Bedakan izin masuk modul dari izin lintas wilayah. Admin/super admin boleh lintas wilayah; koorda dibatasi wilayahnya pada daftar, detail, dan tindakan approve/reject. Koorda tanpa wilayah ditolak.
- Jangan sekadar menghapus koorda dari isAdminLike() karena gate view-alumni dan review-user memang perlu mengizinkan koorda. Gunakan pemeriksaan admin/super admin khusus untuk pengecualian wilayah dan pertahankan izin tindakan sesuai PRD.
- Perketat gate manage-content ke admin/super admin; jalur B tetap dapat mengerjakan CRUD dengan gate dan method role yang sudah ada.
- Materi hanya tersedia bagi akun aktif dengan level yang memenuhi syarat. Level kosong tidak boleh menghilangkan filter; detail melalui URL langsung mengikuti aturan yang sama.
- Pisahkan seeder akun demo dari inisialisasi yang aman. Menjalankan seed ulang tidak boleh mereset password/profil pengelola nyata. Jalur A memiliki DatabaseSeeder dan seeder akun/master data.
- Tambah test negatif untuk tamu, alumni biasa, koorda beda wilayah/tanpa wilayah, akun pending/suspended, level kosong, dan level terlalu rendah. Fixture test harus menetapkan role/status secara eksplisit.

**A1 — P1: stabilkan siklus data alumni.**

- Sesuaikan registrasi publik dengan PRD: route guest, role alumni, status pending, dan alur melengkapi profil. Penambahan alumni oleh pengelola tetap memakai alumni.store dan tidak mengganti sesi pengelola.
- Tetapkan wilayah saat koorda menambah alumni agar akun dapat ditemukan dalam antrean verifikasi wilayahnya; admin memilih wilayah yang valid. Data lama tanpa wilayah tetap ditangani secara eksplisit.
- Validasi semua field profil/alumni sesuai tipe database, termasuk angkatan, tanggal lahir, status pernikahan, dan AB. Gunakan data hasil validasi saat menyimpan.
- Perbaiki sorting wilayah/level dengan query yang mendukungnya atau batasi opsi yang belum didukung.
- Berikan array langsung pada payload audit. Simpan perubahan kritikal dan audit dalam satu transaksi; catat penghapusan sebelum akun hilang. Periksa status sebelum mengirim notifikasi penolakan.
- Selaraskan aturan validasi password di endpoint profil dan auth. Pastikan pergantian foto profil tidak meninggalkan file lama tanpa batas.

**A2 — P2: selesaikan pembelajaran dan operasi alumni.**

- Lengkapi pengelolaan playlist/video oleh admin dan akses video yang benar-benar dapat digunakan alumni, mengikuti schema yang ada.
- Pencarian materi harus benar-benar memfilter data; hapus tombol edit/tambah yang belum mempunyai action.
- Lengkapi impor/ekspor CSV alumni setelah aturan validasi dan wilayah stabil. Sertakan laporan baris gagal dan pencegahan perubahan role/level tanpa izin.
- Progress menonton dan sinkronisasi metadata otomatis ditunda sampai akses dan pengelolaan materi selesai.

**Kriteria selesai jalur A:** pengelola tetap pada sesinya; akun baru memiliki role/status/wilayah yang tepat; akses lintas wilayah ditolak; status dan level membatasi materi; validasi gagal tidak menghasilkan error database; perubahan kritikal memiliki audit yang dapat dibaca. Test baru dan test alumni/auth/profile/materi yang terdampak harus lulus pada PHP 8.3.

### Jalur B: CI, Dashboard, Konten Publik, dan Laporan

Pemilik: pengembang B (rekan pengguna). Branch: dev-B. Mulai dari B0; baca AGENTS.md sebelum mengubah file.

**B0 — P0: lingkungan tim dan route aman.**

- Tetapkan PHP 8.3 sebagai target Composer dan CI; config.platform.php membantu resolusi dependensi ketika pengembang lokal memakai PHP 8.4. Perbarui lock hanya jika dibutuhkan dan jangan melakukan update paket menyeluruh.
- Gunakan MySQL/MariaDB untuk CI dan database test terisolasi, sesuai query aplikasi saat ini. Sediakan service database dalam CI dan samakan phpunit.xml dengan konfigurasi tersebut.
- Jangan menjalankan RefreshDatabase atau migrate:fresh pada database pengembangan bersama maupun produksi. Perbaiki run-ci.sh agar instalasi dependensi mendahului artisan dan tidak menimpa konfigurasi nyata.
- Hentikan pelacakan file environment yang berisi kunci; sediakan template tanpa rahasia. Verifikasi pemakaian kunci lama sebelum rotasi di server karena data terenkripsi dapat terdampak.
- Lindungi dashboard dengan auth dan uji redirect tamu. Tidak perlu menunggu perubahan gate dari A untuk memperbaiki route ini.
- Jangan mengubah UserSeeder/DatabaseSeeder; perbaikan seeder akun dimiliki A0. CI B memakai seeder/fixture test minimal yang tidak bergantung pada perubahan tersebut.

**B1 — P1: dashboard dan publikasi dapat dipercaya.**

- Ganti angka kajian demo dengan query nyata atau empty state berlabel belum tersedia. Perhitungan bulan berjalan harus memasukkan tahun.
- Terapkan scope dashboard/chart memakai method hasAnyRole() yang sudah ada: admin/super admin lintas wilayah, koorda wilayah sendiri. Jangan bergantung pada helper baru dari jalur A.
- Pilih satu jalur grafik yang dipakai dashboard aktif; hentikan pemuatan/request grafik pada halaman yang tidak membutuhkannya.
- Upload post/galeri: simpan file baru, pastikan perubahan record berhasil, baru hapus file lama; bersihkan file baru saat gagal.
- Pastikan slug post unik dan batasi resource route pada action/view yang tersedia.
- Hubungkan homepage aktif dengan post terbit dan galeri dari database; tambahkan detail publik yang dibutuhkan. Ganti konten template serta tautan kosong dengan konten/action yang nyata.
- Tambah test dashboard, scope statistik, slug duplikat, kegagalan upload/pembaruan, dan publikasi konten. Test gate manage-content lintas role dimiliki A0; lakukan pengujian endpoint gabungan setelah merge.

**B2 — P2: laporan kajian sampai dapat direview.**

- Selama belum berfungsi, sembunyikan aksi/form laporan demo atau beri status belum tersedia agar pengguna tidak mengira laporannya tersimpan.
- Bangun create/store/list/detail menggunakan KajianReport dan field yang sudah tersedia. Tetapkan wilayah dari hak pengguna, bukan mempercayai wilayah kiriman form.
- Lengkapi draft -> submitted -> approved/rejected, reviewer, waktu review, serta alasan reject wajib; total peserta dihitung dari komponen peserta di backend.
- Gunakan kelas ScopeWilayah secara langsung pada route laporan bila alias belum terdaftar, sehingga pekerjaan B tidak menunggu perubahan bootstrap milik A. Query dan tindakan tetap wajib memeriksa target laporan.
- Tambahkan view review yang nyata, test akses lintas wilayah, transisi status tidak sah, rekap, lalu export.
- Akhiri dengan QA mobile/keyboard, feedback form, empty/error state, serta prosedur deploy/backup dan pemulihan yang diuji di staging.

**Kriteria selesai jalur B:** instalasi dan CI PHP 8.3/MySQL dapat direproduksi; dashboard menampilkan angka nyata; file lama aman saat pembaruan gagal; konten admin tampil publik sesuai status terbit; laporan tersimpan dan direview sesuai wilayah. Test modul B dan npm run build harus lulus; perbaikan gate A diuji lagi saat integrasi.

### Kepemilikan File dan Batas Perubahan

| Area | Pemilik A | Pemilik B |
|---|---|---|
| Controller/request | Auth, Alumni, Profile, Verification, Materi, Notification dan request terkait | Dashboard, Chart, Landing, Report, Management/Post, Management/Gallery dan request barunya |
| Model | User, Role, Level, Wilayah, AuditLog, Playlist, Video, VideoProgress | Post, Gallery, KajianReport |
| Gate/middleware/bootstrap | AuthServiceProvider, AppServiceProvider, ScopeWilayah, bootstrap/app.php | Tidak mengubah file tersebut; gunakan kontrak role/middleware yang sudah ada |
| Route | routes/auth.php; blok alumni, profil, verifikasi, materi, notifikasi di routes/web.php | Blok root/homepage, dashboard, charts, laporan, management di routes/web.php |
| View | auth, alumni, profile, verification, materi, notifications | dashboard, laporan, management, landingpage |
| UI bersama | Komponen/layout/navigasi bersama hanya dipakai tanpa diubah | Pemilik resources/views/layouts, resources/views/partials/nav, resources/views/components, resources/js dan resources/css |
| Data | User/Role/Level/WilayahSeeder, DatabaseSeeder; migration baru domain A | Post/GallerySeeder; migration baru domain B; tidak menambah ketergantungan pada seeder A |
| Toolchain | Tidak mengubah konfigurasi toolchain | composer.json/lock, package.json/lock, Vite/Tailwind, phpunit.xml, .github/workflows, run-ci.sh, template environment dan .gitignore |
| Test | Test auth/alumni/profile/verification/materi/notification | Test dashboard/chart/content/landing/report, konfigurasi pengujian bersama |

Aturan file bersama:

1. routes/web.php adalah satu-satunya file kode yang dibagi per blok. Jangan melakukan format ulang, memindahkan urutan blok, atau mengganti seluruh file. Route baru menggunakan nama kelas lengkap agar kedua jalur tidak berebut blok import. Jika hunk bertabrakan, penggabung mempertahankan kedua perubahan sesuai kepemilikan blok.
2. Pertahankan nama route dan parameter existing untuk menghindari perubahan pemanggil di jalur lain. View A memakai layout/komponen existing; perubahan komponen bersama masuk catatan integrasi untuk B, tanpa menahan pekerjaan inti A.
3. Tidak mengganti nama/tipe field existing, signature method publik, atau arti role/status secara sepihak. Migration baru harus terpisah menurut domain dengan timestamp/nama unik; jangan mengedit migration yang sudah pernah dijalankan.
4. Role tetap super_admin, admin, koorda, alumni. Status akun tetap pending, active, suspended; reject akun tetap pending dengan alasan tercatat sesuai perilaku sekarang. Status laporan tetap draft, submitted, approved, rejected. Role akun dan level materi tetap dua konsep berbeda.
5. PLAN, PRD, TASK, AUDIT, dan SOP dibekukan selama kedua branch berjalan. Masing-masing jalur mencatat ID task, status, perubahan, risiko, dan hasil test pada file baru docs/aturan_pengembangan/CATATAN_JALUR_A.md atau CATATAN_JALUR_B.md. Penggabung menyinkronkan dokumen pusat setelah merge agar kedua orang tidak mengedit bagian log yang sama.

### Pengujian Mandiri dan Penggabungan

1. Buat dua branch dari commit dasar yang sama. Setiap orang memakai database lokal dan database test sendiri; jangan berbagi file .env berisi rahasia atau database kerja.
2. Jalur A menjalankan test domain A dengan konfigurasi database test lokal yang eksplisit. Jalur B menjalankan test domain B dan CI-nya. A dapat bekerja dengan schema existing sementara B memperbaiki CI; B dapat menguji bisnis modulnya dengan admin/fixture existing sementara A memperketat gate.
3. Setiap PR menyertakan ID task, file miliknya yang berubah, migration, hasil test, dan risiko yang tersisa. Gunakan PR kecil per A0/A1/A2 atau B0/B1/B2 agar koreksi P0 tidak menunggu penyelesaian seluruh fitur P2.
4. Untuk tiap batch, rebase/merge branch terbaru sebelum penggabungan. B0 disarankan masuk lebih dahulu untuk menyediakan CI yang konsisten, lalu A0; ini urutan integrasi, bukan syarat untuk mulai coding. Kedua jalur tetap melanjutkan batchnya sendiri.
5. Penggabung memeriksa khusus routes/web.php, perubahan gate vs endpoint B, urutan migration, dan lockfile. Jangan menyelesaikan konflik dengan membuang salah satu sisi.
6. Di hasil gabungan jalankan composer validate --strict, composer check-platform-reqs, php artisan route:list, migration/seed pada database uji baru, php artisan test, dan npm ci && npm run build pada PHP 8.3. Uji klik per role untuk login/register, alumni, verifikasi, materi, dashboard, konten, dan laporan yang sudah diaktifkan.
7. Release hanya setelah semua P0 tertutup, test regresi lulus, dan dokumen pusat diperbarui. Fitur P2 yang belum selesai tetap disembunyikan atau diberi label belum tersedia.

Catatan baseline audit: 108 file PHP lolos lint menggunakan PHP 8.4 dan composer validate --strict lolos. Runtime PHP 8.3, test aplikasi, build frontend, browser, dan deployment belum terverifikasi karena dependensi lokal belum terpasang. Hasil tersebut bukan bukti bahwa seluruh aplikasi sudah lulus test.

## Prinsip Prioritas

Urutan kerja selalu mengikuti aturan berikut:
1. Tutup celah akses dan risiko kehilangan/terbukanya data, kemudian perbaiki bug yang menghambat workflow utama.
2. Stabilkan data model, migration, route, dan permission sebelum memperluas fitur.
3. Selesaikan workflow end-to-end sebelum mempercantik detail visual.
4. Dokumentasikan perubahan sebelum pindah ke tahap berikutnya.
5. Jangan mengembangkan fitur baru di atas modul yang masih memiliki inkonsistensi database.

## Referensi Kompetensi Tim

Peran berikut adalah daftar kompetensi yang dibutuhkan, bukan tujuh orang atau tujuh jalur pekerjaan. Pelaksanaan aktif tetap dibagi kepada dua orang sesuai tabel kepemilikan di atas.

### 1. Product Owner dan PRD Strategist
Fokus:
- Menjaga scope produk.
- Menerjemahkan kebutuhan pengguna menjadi requirement.
- Memastikan PRD selalu sinkron dengan fitur aktual.

Output:
- Update `PRD.md`.
- Keputusan prioritas.
- Acceptance criteria.

### 2. Backend Laravel Engineer
Fokus:
- Route, controller, model, request validation, middleware, gate, service logic.
- Konsistensi database dan transaksi.

Output:
- Implementasi backend.
- Fix bug workflow.
- Catatan risiko teknis ke `AUDIT.md`.

### 3. Database dan Data Integrity Specialist
Fokus:
- Migration, seeders, foreign key, index, enum, relasi Eloquent.
- Menjaga data alumni, role, level, wilayah, dan audit log valid.

Output:
- Review migration.
- Data integrity checklist.
- Rekomendasi perbaikan schema.

### 4. UX Workflow Designer
Fokus:
- Alur pengguna admin, koorda, alumni, dan publik.
- Form, tabel, error state, empty state, feedback state, dan navigasi.

Output:
- UX acceptance criteria.
- Rekomendasi layout dan microcopy.
- Review halaman yang berubah.

### 5. Frontend Blade dan Tailwind Engineer
Fokus:
- View Blade, komponen UI, responsivitas, dan konsistensi styling.
- Integrasi Chart.js atau UI interaktif ringan.

Output:
- Implementasi UI.
- Responsiveness check.
- Konsistensi komponen.

### 6. QA dan Test Engineer
Fokus:
- Test manual dan otomatis.
- Regression check.
- Validasi role, permission, dan form.

Output:
- Test checklist.
- Catatan hasil test di `TASK.md`.
- Temuan bug di `AUDIT.md`.

### 7. Technical Writer dan Documentation Controller
Fokus:
- Menjaga semua dokumen pengembangan sinkron.
- Memastikan setiap perubahan punya jejak keputusan.

Output:
- Update SOP, task log, dan audit log.
- Review konsistensi antar dokumen.

## Referensi Rincian Modul dari Rencana Sebelumnya

Nomor tahap dan label prioritas di bawah adalah referensi historis scope modul. Untuk penjadwalan saat ini gunakan A0-A2 dan B0-B2 pada Rencana Aktif; bagian ini tidak mewajibkan kedua orang saling menunggu.

### Tahap 0. Pondasi Dokumentasi
Status: berjalan.

Tujuan:
- Membuat folder dokumentasi pengembangan.
- Membuat PRD, PLAN, TASK, AUDIT, SOP.
- Menetapkan workflow sinkronisasi dokumen.

Deliverable:
- `docs/aturan_pengembangan/PRD.md`
- `docs/aturan_pengembangan/PLAN.md`
- `docs/aturan_pengembangan/TASK.md`
- `docs/aturan_pengembangan/AUDIT.md`
- `docs/aturan_pengembangan/SOP.md`

### Tahap 1. Stabilkan Modul Alumni
Prioritas: urgent.

Tujuan:
- Pastikan tambah alumni, daftar alumni, detail alumni, filter, status, dan role berjalan end-to-end.

Pekerjaan:
- Review tambah alumni oleh admin.
- Tambah tampilan error validasi.
- Pastikan role alumni tersimpan.
- Pastikan data tampil setelah insert.
- Tambahkan reset filter jika diperlukan.
- Review query scope wilayah.
- Tambahkan test minimal untuk store alumni.

Acceptance criteria:
- Admin dapat tambah alumni baru.
- Data bertambah di database.
- Data terlihat di daftar alumni.
- Error validasi tampil.
- Admin tetap login sebagai admin.

### Tahap 2. Audit Schema dan Migration
Prioritas: urgent.

Tujuan:
- Membersihkan inkonsistensi schema sebelum lanjut ke materi/laporan.

Pekerjaan:
- Review seluruh migration users, role, wilayah, level, playlists, videos, video_progress, kajian_reports.
- Perbaiki inkonsistensi `is-active` vs `is_active`.
- Pastikan field yang dipakai controller ada di database.
- Review enum seperti `ab` agar nilai form dan seeder sesuai.
- Review index dan foreign key.

Acceptance criteria:
- `php artisan migrate:fresh --seed` berjalan di environment aman.
- Seeder tidak gagal.
- Model fillable sesuai field database.

### Tahap 3. Stabilkan Auth, Profile, dan Verification
Prioritas: tinggi.

Tujuan:
- Memastikan onboarding alumni sampai aktif berjalan jelas.

Pekerjaan:
- Review registrasi publik.
- Review update profile dan status pending.
- Review approve/reject.
- Review notifikasi.
- Review audit log.
- Tambahkan pesan UX untuk status pending/active/rejected.

Acceptance criteria:
- Alumni baru memahami status akunnya.
- Koorda/admin melihat daftar pending yang tepat.
- Approve mengaktifkan akun dan set level.
- Reject menyimpan alasan.

### Tahap 4. Perbaiki Dashboard
Prioritas: tinggi.

Tujuan:
- Dashboard menjadi sumber monitoring yang akurat.

Pekerjaan:
- Hilangkan dummy data yang berpotensi menyesatkan.
- Validasi angka dashboard dengan query tabel.
- Pastikan chart loading aman.
- Tambahkan empty state jika data kosong.

Acceptance criteria:
- Semua angka dashboard berasal dari database.
- Chart tidak error saat data kosong.

### Tahap 5. Rapikan Manajemen Konten
Prioritas: sedang.

Tujuan:
- Membuat post/refleksi dan galeri siap dipakai admin.

Pekerjaan:
- Review CRUD post.
- Review CRUD galeri.
- Tambah validasi dan feedback.
- Pastikan upload gambar aman.
- Pastikan konten tampil di landing page jika diperlukan.

Acceptance criteria:
- Admin bisa tambah, edit, hapus post dan galeri.
- Landing page menampilkan data dinamis jika scope disetujui.

### Tahap 6. Bangun Modul Materi Kajian
Prioritas: sedang setelah schema stabil.

Tujuan:
- Alumni dapat mengakses materi sesuai level.

Pekerjaan:
- Rapikan schema playlist/video.
- Bangun CRUD playlist untuk admin.
- Bangun halaman materi untuk alumni.
- Batasi akses berdasarkan level.
- Tambahkan detail playlist/video.

Acceptance criteria:
- Alumni level tertentu hanya melihat materi sesuai aksesnya.
- Admin dapat mengelola playlist.
- Tidak ada error field tidak ditemukan.

### Tahap 7. Bangun Modul Laporan Kajian
Prioritas: sedang.

Tujuan:
- Koorda/admin dapat mengelola laporan kajian wilayah.

Pekerjaan:
- Finalisasi PRD laporan.
- Buat form laporan.
- Buat review approve/reject.
- Buat dashboard rekap.
- Export laporan jika dibutuhkan.

Acceptance criteria:
- Laporan tersimpan.
- Scope wilayah aman.
- Review workflow jelas.

### Tahap 8. QA, Hardening, dan Release
Prioritas: final.

Tujuan:
- Menyiapkan aplikasi untuk staging/produksi.

Pekerjaan:
- Regression test.
- Review security.
- Review performance query.
- Review UX mobile.
- Update dokumentasi.
- Buat release checklist.

Acceptance criteria:
- Workflow utama lulus test.
- Tidak ada error kritikal di log.
- Dokumen sinkron.

