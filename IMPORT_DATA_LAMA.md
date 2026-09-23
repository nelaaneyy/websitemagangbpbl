# Dokumentasi & Panduan Import Data Real Tahun-Tahun Sebelumnya (Historis BPBL)

Dokumen ini merekam spesifikasi teknis dan alur kerja penginputan data real penerima manfaat BPBL dari tahun-tahun sebelumnya ke dalam sistem Website BPBL Dinas ESDM Provinsi Jambi.

---

## 📌 Konteks & Tujuan
Memasukkan data real warga penerima BPBL dan/atau pengajuan Lisdes dari tahun-tahun lalu (misal: 2021, 2022, 2023, 2024, 2025) ke dalam database agar rekapitulasi, grafik analytics, dan laporan tahunan di sistem lengkap dan akurat.

---

## 📊 Format Kolom Data Excel Baku Terbaru

Sesuai dengan format data dinas terbaru, file Excel/CSV menggunakan susunan kolom sebagai berikut:

| Nama Kolom Excel | Mapping Database | Tipe & Keterangan |
| :--- | :--- | :--- |
| `No` | - | Nomor Urut (diabaikan) |
| `KECAMATAN` | `kecamatan` | Nama Kecamatan (String) |
| `DESA/KELURAHAN` | `desa` | Nama Desa/Kelurahan (String) |
| `NAMA` | `nama` | Nama Lengkap Penerima Manfaat |
| `NIK` | `nik` | NIK Warga (String 16 Digit Unique) |
| `ALAMAT` | `alamat` | Alamat Lengkap Domisili Warga |
| `Usulan` | `tahun_usulan` & `created_at` | Tahun Pengajuan (e.g. `2023`, `2024`) |
| `Realisasi` | `status_verifikasi` & `butuh_validasi_realisasi` | Tanda centang (`✓`, `V`, `v`, `1`, `ya`, `sudah`) vs Kosong |
| `Keterangan` | `keterangan_import` / `catatan` | Catatan/Keterangan Tambahan |

---

## ⚙️ Logika Pengecekan Centang & Validasi SuperAdmin

Ketika data dari Excel/CSV diimpor via CLI `php artisan import:data-lama` maupun via UI Dashboard Admin ESDM:

1. **Memiliki Centang Realisasi (`✓`, `V`, `v`, `1`, `ya`, `sudah`, `terpasang`, atau Tahun Realisasi):**
   - System secara otomatis menetapkan `status_verifikasi = 'terpasang'` (Sudah Direalisasi).
   - Flag `butuh_validasi_realisasi = false`.

2. **TIDAK Memiliki Tanda Centang (Kolom Realisasi Kosong/Blank):**
   - System menandai data ini dengan flag `butuh_validasi_realisasi = true` dan `status_verifikasi = 'menunggu_verifikasi_pusat'`.
   - Data otomatis masuk ke **Antrean Validasi Realisasi SuperAdmin** pada menu **"Validasi Realisasi Data"**.

---

## 🖥️ Fitur Validasi SuperAdmin

SuperAdmin / Verifikator ESDM dapat mengakses antarmuka di `route('dinasesdm.validasi_realisasi.index')` untuk melakukan tindakan peninjauan NIK:
- **[Sudah Realisasi] / Konfirmasi Massal:** Mengubah status verifikasi menjadi `terpasang` (Sudah Direalisasi) & menghapus flag validasi.
- **[Belum Realisasi] / Konfirmasi Massal:** Mengubah status menjadi `lolos_verifikasi_pusat` (Usulan Valid Belum Terpasang) & menghapus flag validasi.

---

## 🛠️ Cara Penggunaan Import Data Lama

### 1. Via Command Line Interface (CLI):
```bash
php artisan import:data-lama path/to/file.csv
```

### 2. Via Dashboard Admin ESDM:
1. Masuk ke Panel Admin ESDM (`/dinasesdm`).
2. Klik tombol **"Ekspor & Import"** -> Pilih **"Download Template Excel"** jika membutuhkan acuan format.
3. Upload file Excel/CSV melalui modal **Import Data BPBL**.

---
*Dokumentasi diperbarui pada: 8 September 2026*
