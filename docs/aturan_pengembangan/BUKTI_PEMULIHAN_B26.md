# Bukti latihan pemulihan B2.6

Tanggal: 2026-10-17. Lingkungan: lokal, Windows/Laragon. Data: sintetis seluruhnya.

Perintah:

```powershell
php scripts/rehearse-recovery.php --run-local-drill --mysql-bin=C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin
```

Hasil akhir, exit code 0:

```json
{
    "status": "PASS",
    "runtime": "8.3.33",
    "database_server": "8.4.3",
    "synthetic_only": true,
    "row_counts": {
        "migrations": 22,
        "users": 2,
        "roles": 2,
        "role_user": 2,
        "wilayah": 1,
        "posts": 1,
        "galleries": 1,
        "kajian_reports": 1
    },
    "files_verified": 3,
    "key_decryption": "PASS",
    "corruption_detection": "PASS",
    "archive_sha256": "2188792f584e31ece881bc351f10c908ab288d3da81b5f3f58e4de75d556d571",
    "restore_seconds": 1.979,
    "total_seconds": 7.4,
    "cleanup": "PASS"
}
```

Urutan yang dijalankan: buat dua database acak baru, migration pada source tanpa seed,
fixture user/role/wilayah/konten/laporan, dump memakai mysqldump lokal, arsip SQL +
tiga file upload sintetis + kunci sintetis + manifest checksum, ekstraksi dan
verifikasi checksum, import pada database restore kosong, pemeriksaan jumlah row,
relasi role reviewer, metadata keputusan, foto privat dan dekripsi canary dengan
kunci yang dipulihkan. Database serta file sementara milik drill dihapus setelahnya.

Kontrol negatif: path binary tidak ada menghasilkan exit 2 sebelum membuat database.
Checksum bytes SQL yang sengaja diubah tidak cocok manifest. Parameter database existing
tidak disediakan; nama source/restore selalu dibuat acak oleh script dan cleanup hanya
menjangkau nama yang berhasil dibuat pada run tersebut.

Pada percobaan awal, cleanup gagal karena memakai kelas Filesystem yang tidak
terpasang. Kedua database sudah dihapus; direktori sementara yang tersisa dibersihkan
dengan pemeriksaan path literal di dalam workspace. Script diperbaiki memakai
Illuminate Filesystem existing dan exception handler exit nonzero; run final di atas
menyelesaikan cleanup tanpa sisa direktori recovery-drill.

Review juga menemukan cleanup perlu tetap mencoba resource berikutnya jika satu
DROP DATABASE gagal. RecoveryCleanup kini mencoba semua action, membersihkan file
sementara secara independen, lalu melaporkan resource yang gagal tanpa melaporkan
sukses. Test regresi gagal pada perilaku fail-fast lama lalu lulus setelah perbaikan
(1 test, 2 assertion). Run final yang dicatat di atas memakai perbaikan tersebut.

Validasi tambahan: composer validate --strict dan composer check-platform-reqs lulus;
PHP lint kedua script/test serta git diff --check lulus. Suite akhir: 140 test lulus,
2 skipped registrasi existing, 1127 assertion pada rf_b_testing. Build terakhir adalah
bukti B2.5; tidak diulang karena batch ini tidak mengubah frontend/dependensinya.

Hasil ini membuktikan mekanisme pada fixture kecil MySQL 8.4.3 lokal, bukan target
RTO/RPO, pemulihan data produksi, kompatibilitas client MariaDB atau kesiapan rilis.
Pemulihan staging nyata, backup offsite terenkripsi, akses operator, retensi,
integrasi A0, penilaian dependensi dan CI remote masih memerlukan bukti tersendiri.
Arsip sintetis tidak disimpan; hash dicatat untuk mengidentifikasi artefak run tersebut.
