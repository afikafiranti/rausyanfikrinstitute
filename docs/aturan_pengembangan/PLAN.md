# Plan Pengembangan

Dokumen ini adalah rencana tahapan pengembangan berurutan berdasarkan prioritas urgensi, risiko, dan dampak terhadap pengguna.

## Prinsip Prioritas

Urutan kerja selalu mengikuti aturan berikut:
1. Perbaiki bug yang menghambat data tersimpan atau user menjalankan workflow utama.
2. Stabilkan data model, migration, route, dan permission sebelum memperluas fitur.
3. Selesaikan workflow end-to-end sebelum mempercantik detail visual.
4. Dokumentasikan perubahan sebelum pindah ke tahap berikutnya.
5. Jangan mengembangkan fitur baru di atas modul yang masih memiliki inkonsistensi database.

## Tim Sub-Agen Pengembangan

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

## Urutan Eksekusi Pengembangan

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

