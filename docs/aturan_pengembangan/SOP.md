# SOP Pengembangan dan Sinkronisasi Dokumen

Dokumen ini wajib diikuti sebelum, selama, dan setelah pengembangan fitur.

## 1. Dokumen Wajib

Semua pengembangan harus mengacu pada:
- `PRD.md`: kebutuhan produk, role, fitur, acceptance criteria.
- `PLAN.md`: urutan pengembangan dan prioritas.
- `TASK.md`: task yang sudah, sedang, dan akan dikerjakan.
- `AUDIT.md`: risiko, guardrail, dan keputusan audit.
- `SOP.md`: aturan kerja dan sinkronisasi dokumen.

## 2. Aturan Sinkronisasi

Sebelum mulai coding:
1. Baca `PRD.md` untuk memahami kebutuhan.
2. Baca `PLAN.md` untuk memastikan prioritas.
3. Tambahkan atau update item di `TASK.md` sebagai `IN_PROGRESS`.
4. Cek `AUDIT.md` untuk risiko terkait.

Saat coding:
1. Jaga perubahan tetap sesuai scope task.
2. Jika menemukan bug baru, catat di `AUDIT.md`.
3. Jika task berubah scope, update `PRD.md` dan `PLAN.md`.
4. Jika menemukan dependency baru, catat di `TASK.md`.

Setelah coding:
1. Jalankan cek sintaks atau test yang relevan.
2. Cek workflow manual jika fitur menyentuh UI.
3. Update `TASK.md` dengan hasil dan verifikasi.
4. Update `AUDIT.md` jika ada risiko tersisa.
5. Pastikan dokumen tidak saling bertentangan.

## 3. Workflow Sub-Agen

Pengembangan dikelola sebagai tim sub-agen spesialis. Dalam praktik kerja, sub-agen dapat bekerja simultan pada area yang tidak saling menimpa.

### Product Owner dan PRD Strategist
Tanggung jawab:
- Menentukan kebutuhan dan acceptance criteria.
- Mencegah scope melebar tanpa keputusan.

Dokumen utama:
- `PRD.md`
- `PLAN.md`

### Backend Laravel Engineer
Tanggung jawab:
- Controller, route, middleware, request validation, authorization, transaksi.

Dokumen utama:
- `TASK.md`
- `AUDIT.md`

### Database dan Data Integrity Specialist
Tanggung jawab:
- Migration, seeder, relasi, index, foreign key, enum, data consistency.

Dokumen utama:
- `AUDIT.md`
- `TASK.md`

### UX Workflow Designer
Tanggung jawab:
- Alur form, tabel, feedback, empty state, error state, dan navigasi.

Dokumen utama:
- `PRD.md`
- `AUDIT.md`

### Frontend Blade dan Tailwind Engineer
Tanggung jawab:
- Implementasi view, komponen Blade, layout responsif, tampilan data.

Dokumen utama:
- `TASK.md`

### QA dan Test Engineer
Tanggung jawab:
- Test plan, regression test, role test, dan validasi workflow.

Dokumen utama:
- `TASK.md`
- `AUDIT.md`

### Technical Writer dan Documentation Controller
Tanggung jawab:
- Menjaga semua dokumen sinkron.
- Menolak penutupan task jika dokumen belum diperbarui.

Dokumen utama:
- Semua dokumen di folder ini.

## 3A. Skill Wajib dan Aturan Penggunaan

Skill project terpasang di `.agents/skills` dan berlaku untuk Codex dalam repo ini. Setiap skill eksternal wajib dibaca instruksinya saat relevan sebelum dipakai.

### UI dan UX

Gunakan `ui-ux-pro-max` saat:
- Mendesain halaman baru.
- Mereview UX halaman admin, dashboard, landing page, form, tabel, modal, dan navigasi.
- Memilih sistem warna, tipografi, spacing, layout, aksesibilitas, atau pola visual.

Gunakan `ui-styling` saat:
- Mengimplementasikan Blade/Tailwind UI.
- Merapikan form, tabel, badge, empty state, feedback state, responsivitas, dan dark/light style jika kelak dibutuhkan.

Gunakan `design-system` saat:
- Menetapkan token desain, standar komponen, dan aturan konsistensi UI.
- Membuat atau memperbarui design system internal Rausyan Fikr.

Gunakan `shadcn` hanya sebagai referensi pola komponen:
- Project ini berbasis Laravel Blade/Tailwind, bukan React.
- Jangan menginstal komponen shadcn React ke project kecuali ada keputusan migrasi/penambahan stack.
- Ambil prinsip komponen, accessibility pattern, dan struktur UI jika relevan, lalu adaptasi ke Blade/Tailwind.

### Backend, Arsitektur, dan Kualitas Kode

Gunakan `diagnosing-bugs` saat:
- Ada bug, error Laravel, data tidak tersimpan, route tidak berjalan, query gagal, atau workflow rusak.

Gunakan `tdd` saat:
- Membuat fitur baru yang punya risiko bisnis/data.
- Memperbaiki bug yang perlu regression test.
- Menambah test Feature/Unit Laravel.

Gunakan `implement` saat:
- Mengerjakan task yang sudah punya PRD/PLAN/TASK jelas.
- Mengubah kode berdasarkan spesifikasi yang sudah disetujui.

Gunakan `code-review` saat:
- Mereview perubahan sebelum task ditutup.
- Mengecek kesesuaian implementasi terhadap PRD dan SOP.

Gunakan `codebase-design` saat:
- Merapikan modul besar.
- Menentukan batas tanggung jawab controller, model, middleware, service, dan view.

Gunakan `domain-modeling` saat:
- Mendefinisikan istilah domain seperti alumni, koorda, wilayah, level, kajian, materi, laporan, verifikasi, dan status.

Gunakan `research` saat:
- Membutuhkan riset teknis berbasis sumber primer.
- Membandingkan pendekatan Laravel, Tailwind, Chart.js, browser automation, atau library lain.

Gunakan `handoff` saat:
- Pekerjaan panjang perlu dilanjutkan sesi/agen lain.
- Konteks perlu dipadatkan menjadi dokumen serah-terima.

Gunakan `setup-matt-pocock-skills` hanya jika perlu mengonfigurasi penuh ekosistem Matt Pocock skills untuk repo ini. Jika dijalankan, hasil konfigurasi wajib dicatat di `TASK.md` dan `AUDIT.md`.

### Browser QA dan Automasi

Gunakan `browser-use` saat:
- Perlu mengendalikan browser untuk menguji workflow web.
- Perlu screenshot atau inspeksi interaksi halaman.

Gunakan `remote-browser` saat:
- Codex berjalan di sandbox tanpa GUI tetapi perlu membuka dan menguji halaman.

Gunakan `qa` saat:
- Meminta skor kualitas halaman atau workflow end-to-end.
- Melakukan QA eksplisit pada URL lokal/staging.

Catatan keamanan:
- Skill Browser-use dapat memakai browser/cloud/tunnel sesuai instruksi skill.
- Jangan kirim kredensial nyata, data pribadi sensitif, atau token produksi ke browser automation tanpa izin eksplisit.
- Untuk workflow lokal, gunakan akun dummy/test jika memungkinkan.

### 9Router

Gunakan `9router` sebagai entry point saat:
- User secara eksplisit meminta 9Router.
- Pengembangan membutuhkan gateway AI lokal/remote yang kompatibel OpenAI.
- Perlu menyusun integrasi AI tanpa mengikat langsung ke provider tertentu.

Gunakan `9router-chat` saat:
- Membuat atau menguji panggilan chat/completion via 9Router.

Gunakan `9router-web-search` dan `9router-web-fetch` hanya jika:
- User meminta penggunaan 9Router untuk pencarian/fetch web.
- Atau SOP task membutuhkan 9Router sebagai jalur riset.

Catatan:
- Untuk informasi teknis umum, tetap prioritaskan sumber primer dan aturan browsing yang berlaku.
- Jangan memasukkan API key 9Router ke repo.

### OpenCode

Repo `anomalyco/opencode` telah dicek. Dari repo tersebut, skill yang terdeteksi untuk instalasi adalah `effect`, yang tidak relevan untuk project Laravel/Blade ini. Karena itu tidak dipasang.

OpenCode tetap dicatat sebagai referensi kompatibilitas skill:
- OpenCode dapat membaca skill berbasis `SKILL.md`.
- Jika tim memakai OpenCode di luar Codex, gunakan folder skill yang kompatibel dan ikuti dokumentasi OpenCode.
- Jangan menambahkan konfigurasi OpenCode ke repo ini tanpa kebutuhan operasional yang jelas.

## 4. Urutan Eksekusi Standar

Setiap fitur harus melewati urutan ini:

1. Requirement
   - Pastikan kebutuhan ada di PRD.
   - Jika belum ada, tambahkan dulu.

2. Planning
   - Tempatkan task di PLAN sesuai prioritas.
   - Pecah task besar menjadi beberapa task kecil.

3. Task Registration
   - Catat task di TASK.
   - Tetapkan status `IN_PROGRESS`.

4. Audit Pre-Check
   - Cek risiko database, permission, UX, dan dependency.

5. Implementation
   - Kerjakan kode sesuai scope.
   - Jangan melakukan refactor besar yang tidak terkait.

6. Verification
   - Jalankan test atau cek sintaks.
   - Lakukan uji manual untuk workflow UI.
   - Cek log jika workflow pernah error.

7. Documentation Sync
   - Update TASK.
   - Update AUDIT.
   - Update PRD/PLAN jika ada perubahan scope.

8. Handover
   - Laporkan perubahan, file yang disentuh, dan verifikasi.

## 5. Aturan UX Wajib

1. Setiap form harus punya feedback sukses dan error.
2. Error validasi harus terlihat dekat dengan field atau di area yang mudah dilihat.
3. Modal form harus tetap terbuka jika validasi gagal.
4. Tabel harus punya empty state yang jelas.
5. Filter aktif harus mudah dipahami dan dapat di-reset.
6. Aksi penting harus punya permission yang tepat.
7. Jangan membuat user berpindah sesi tanpa disengaja.
8. Jangan menampilkan data dummy sebagai data nyata.

## 6. Aturan Database Wajib

1. Field yang dipakai controller harus ada di migration.
2. Field yang diisi mass assignment harus ada di `$fillable`.
3. Relasi Eloquent harus sesuai foreign key.
4. Nilai enum di form, seeder, dan database harus sama.
5. Perubahan schema harus disertai audit dampak.
6. Insert multi-tabel penting harus memakai transaksi.
7. Automated test wajib memakai database testing terpisah, saat ini `rf_db_testing`.
8. Jangan menjalankan `RefreshDatabase` pada database development `rf_db`.

## 7. Aturan Permission Wajib

1. Route admin harus dilindungi middleware `auth` dan gate yang sesuai.
2. Koorda hanya boleh mengakses data wilayahnya kecuali ada keputusan khusus.
3. Super admin dan admin harus dibedakan jika ada aksi berisiko tinggi.
4. Helper role di model harus konsisten dengan middleware.

## 8. Template Handover Setelah Task

Gunakan format berikut saat melaporkan hasil:

```text
Task:
Status:
File diubah:
Perubahan utama:
Verifikasi:
Risiko tersisa:
Dokumen yang diperbarui:
```

## 9. Larangan

- Jangan mulai fitur baru tanpa update `TASK.md`.
- Jangan menutup task tanpa verifikasi.
- Jangan mengubah schema tanpa update `AUDIT.md`.
- Jangan membiarkan error validasi tersembunyi.
- Jangan menambah role/permission tanpa mencatat dampaknya.
- Jangan menghapus perubahan user tanpa instruksi eksplisit.
