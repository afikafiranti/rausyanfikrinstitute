# Plan Pengembangan

Dokumen ini hanya menentukan urutan dan dependensi. Status aktual ada di [register TASK](TASK.md#register-rfix); rincian dan kriteria penerimaan ada di [dokumen induk](DOKUMEN_INDUK_PERBAIKAN_2026-11-04.md#9-rincian-pekerjaan).

## Rencana Aktif

Mulai dari item yang belum selesai pada TASK. Jika pekerjaan dalam tahap awal sudah DONE, jangan mengulangnya untuk mengikuti urutan historis.

| Urutan/dependensi | Pekerjaan |
|---|---|
| Prioritas keamanan | RFIX-01 berdiri sendiri; tidak menunggu desain database |
| Acuan schema | RFIX-02/KEP-10 menjadi dasar RFIX-03–06 dan fixture RFIX-23 |
| Integritas akun dan izin | RFIX-07–09/18 mengikuti kontrak PRD; RFIX-08 memakai KEP-03 |
| Dependency dan CI | RFIX-10–13 dapat ditriase paralel dengan pekerjaan schema |
| Penyelesaian antarmuka dan kode | RFIX-14–17 memakai keputusan KEP-04/05; RFIX-21/24 mengikuti ARAH-10/11 |
| Pemeriksaan lintas perubahan | RFIX-22 memakai schema/fixture akhir dan hasil integrasi; RFIX-20 menyelaraskan catatan/aset |
| Pembuktian di lingkungan tujuan | RFIX-12 remote, RFIX-13 server, RFIX-19 dan bagian rilis RFIX-22 membutuhkan target/akses yang sah |

Penataan dokumentasi RFIX-20 tidak menunggu staging. Pekerjaan CI, operator, dan pemilik lisensi dapat berjalan independen bila kebutuhannya tersedia. Keterbatasan satu lingkungan tidak membatalkan bukti yang valid pada lingkungan lain.

## Batas perubahan

Cakupan produk mengikuti [PRD](PRD.md#cakupan-dan-status-implementasi), keputusan schema mengikuti dokumen induk, dan koordinasi berkas mengikuti AGENTS. Fitur yang ditunda tidak dimulai kembali sebagai bagian perapian kode tanpa keputusan kebutuhan baru.

## Tindak Lanjut Setelah Integrasi

Bagian ini mempertahankan anchor rujukan lama. Daftar prioritas A/B/PUB sebelum RFIX hanya riwayat; tindak lanjut aktif mengikuti TASK dan urutan di atas.
