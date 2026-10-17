# QA Jalur B — B2.5

Tanggal: 2026-10-16. Branch: dev-B. Runtime: PHP 8.3.33.

## Lingkungan dan cakupan

- Aplikasi QA memakai database baru bernama rf_b_qa_<acak>_testing, akun/data sintetis, APP_KEY sementara, session dan storage terpisah. Tidak memakai database kerja atau environment nyata.
- Browser Use cloud mengakses tunnel sementara dengan router terbatas pada halaman publik, login/logout, dashboard, laporan dan asset build/landingpage. Akses /storage dan file aplikasi lain tidak disajikan. /profile sengaja tidak tersedia dalam fixture.
- Pengujian browser tidak menggantikan CI remote, hasil merge dengan A, staging atau uji backup/pemulihan.

## Sesi browser pertama

Report ID: QA-B25-20261016-01. Skor browser sebelum perbaikan favicon: **4/5**.

| Alur | Bukti observasi browser | Hasil |
|---|---|---|
| Home → detail refleksi terbit | Judul/konten sintetis terbit tampil; draf tidak tampil | Lulus |
| Galeri kosong | Pesan “Belum ada foto galeri.” pada desktop/mobile | Lulus |
| Menu publik mobile | Native details/summary dapat dibuka/ditutup dengan keyboard dan navigasi ke galeri | Lulus |
| Laporan tanpa login | Redirect ke login | Lulus |
| Form laporan | Validasi field wajib, input keyboard, simpan judul/tanggal/peserta/catatan | Lulus |
| Edit dan kirim | Laporan ID 4 menjadi “Laporan Browser B25 Revisi”, status submitted; edit/kirim hilang | Lulus |
| Drawer internal | Fokus ke tombol tutup, Tab tetap di dalam, Escape menutup dan mengembalikan fokus | Lulus |
| Mobile 390×844 | scrollWidth = clientWidth = 390 pada publik/internal | Lulus |
| Console/network | Tidak ada exception JS atau unhandled rejection; favicon internal 404 | Perbaikan favicon diterapkan |

Sumber saat pengujian (tunnel bersifat sementara):

- https://forming-highlights-ran-warning.trycloudflare.com/
- https://forming-highlights-ran-warning.trycloudflare.com/refleksi/refleksi-qa-terbit-01m523j793dfw6t2htzpw454gz
- https://forming-highlights-ran-warning.trycloudflare.com/galeri
- https://forming-highlights-ran-warning.trycloudflare.com/laporan/buat
- https://forming-highlights-ran-warning.trycloudflare.com/laporan/4

## Perbaikan lokal B

- Nama aksesibel dan state ARIA pada tombol drawer dan akun; dialog drawer bernama.
- Fokus drawer: pindah saat dibuka, pembatasan Tab/Shift+Tab, Escape, pengembalian fokus, background inert dengan state sebelumnya dipulihkan.
- Skip link menuju main yang dapat menerima fokus. Margin konten mobile tidak lagi memakai margin negatif pada semua sisi.
- Pencarian dummy yang tidak melakukan pencarian dihapus. Duplikasi atribut alt avatar dibersihkan.
- Favicon SVG existing ditautkan pada layout internal dan guest; endpoint asset lokal menghasilkan HTTP 200.

## Catatan integrasi A

Inspeksi resources/views/auth/login.blade.php menemukan label Email/Password belum memiliki asosiasi for/id yang lengkap, dan tombol tampil/sembunyikan password belum memiliki nama aksesibel. File domain A tidak diubah. Perlu ditangani pemilik A saat integrasi; keberhasilan mengetik/login melalui browser bukan bukti semua semantik form auth sudah aksesibel.

## Sesi lanjutan

Report ID: QA-B25-20261016-02. Skor browser setelah perbaikan: **5/5 untuk alur yang diuji**. Catatan integrasi semantik login A di atas tetap terbuka; skor browser bukan audit aksesibilitas menyeluruh.

| Alur | Bukti observasi browser | Hasil |
|---|---|---|
| Approve | Laporan ID 4 menjadi Disetujui, reviewer QA Koorda; tombol keputusan hilang | Lulus |
| Reject | Alasan kosong ditahan validasi required; laporan ID 1 Ditolak dengan alasan “Perlu dokumentasi tambahan QA” | Lulus |
| Rekap approved | 2 laporan, 5 kehadiran laki-laki, 7 perempuan, total 12 | Lulus |
| CSV sesuai filter | ID 4 masuk, ID 1 yang ditolak tidak masuk file unduhan | Lulus |
| Drawer mobile | Tab/Shift+Tab tetap di dialog, Escape mengembalikan fokus | Lulus |
| Menu akun desktop | Enter membuka, Escape menutup dan mengembalikan fokus | Lulus |
| Skip link | Keyboard memfokuskan #konten-utama | Lulus |
| Layout mobile | Konten rekap berada di bawah navbar 80px tanpa tumpang tindih/overflow horizontal | Lulus |
| Favicon | Hard reload guest dan app memperoleh 200 image/svg+xml | Lulus |
| Console/network | Tidak ada exception, unhandled rejection atau error network belum terselesaikan setelah perbaikan | Lulus |

Sumber tambahan saat pengujian:

- https://forming-highlights-ran-warning.trycloudflare.com/laporan/review
- https://forming-highlights-ran-warning.trycloudflare.com/laporan/1
- https://forming-highlights-ran-warning.trycloudflare.com/laporan/rekap?status=approved
- https://forming-highlights-ran-warning.trycloudflare.com/laporan/ekspor?wilayah_id=1&status=approved
- https://forming-highlights-ran-warning.trycloudflare.com/landingpage/images/logo/favicon.svg

Browser Use menghasilkan qa-report.html, screenshot langkah serta CSV pada artifact sesi cloud (outputs/). Artifact tersebut tidak disalin ke filesystem repo; tabel ini merangkum hasil yang dikembalikan tool, bukan klaim screenshot tersimpan lokal.

## Verifikasi dan pembersihan

- RED test navigasi: 1 gagal / 2 assertion pada pencarian dummy sebelum implementasi.
- Test navigasi setelah perbaikan: 1 lulus / 9 assertion.
- Suite akhir PHP 8.3.33: 139 lulus, 2 skipped registrasi existing, 1125 assertion pada rf_b_testing.
- Build Vite: lulus, 162 modul. node --check resources/js/notus.js, lint test, git diff --check: lulus.
- Review kode independen readonly tidak menemukan blocker navigasi; pengujian dinamis di atas melengkapi review statis tersebut.
- Sesi reviewer dilaporkan logout dan browser ditutup oleh Browser Use. Server PHP dan proses tunnel dihentikan; database QA sementara dihapus dengan pemeriksaan nama/database terhadap state sesi.
- Tunnel sumber di atas sudah tidak aktif. Data browser bersifat sintetis dan tidak diterapkan ke database kerja. CI remote, integrasi A, screen reader lintas platform, staging dan backup/pemulihan belum diverifikasi.
