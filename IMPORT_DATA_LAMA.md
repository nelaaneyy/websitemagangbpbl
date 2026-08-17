# Rencana Import Data Real Tahun-Tahun Sebelumnya (Historis BPBL)

Document ini dibuat untuk merekam rencana teknis penginputan data real dari tahun-tahun sebelumnya ke dalam sistem Website BPBL Dinas ESDM Provinsi Jambi.

---

## 📌 Konteks & Tujuan
Memasukkan data real warga penerima BPBL dan/atau pengajuan Lisdes dari tahun-tahun lalu (misal: 2021, 2022, 2023, 2024, 2025) ke dalam database agar rekapitulasi, grafik, dan laporan tahunan di sistem lengkap dan akurat.

---

## 🛠️ Solusi Terpilih: Kombinasi Opsi 1 & Opsi 2 (Best Practice)

1. **Opsi 2 (Database Seeder / Script CLI):**
   - Digunakan untuk memasukkan **data real awal yang dimiliki saat ini** secara sekaligus dan cepat ke database.
   - Script akan menyelaraskan timestamp `created_at` atau `tahun` sesuai dengan tanggal/tahun pengajuan aslinya.

2. **Opsi 1 (Fitur Import Excel di Dashboard Admin ESDM):**
   - Dibuatkan fitur **"Upload File Excel"** dan **"Download Template Excel"** di halaman Dashboard Admin Dinas ESDM.
   - Digunakan agar Admin/Dinas bisa meng-upload susulan data lama secara mandiri kapan saja lewat UI website di kemudian hari.

---

## 📊 Format / Kolom Data Excel yang Disiapkan

| Nama Kolom Excel | Tipe / Format | Keterangan |
| :--- | :--- | :--- |
| `nik` | String (16 Digit) | Unique ID Warga |
| `nama` | String | Nama Lengkap Penerima |
| `kabupaten` | String | Kabupaten asal |
| `kecamatan` | String | Kecamatan asal |
| `desa` | String | Desa/Kelurahan asal |
| `rt_rw` | String | RT / RW |
| `alamat` | Text | Alamat Lengkap |
| `no_hp` | String | Nomor HP/Telepon |
| `latitude` | Decimal (opsional) | Koordinat Lokasi |
| `longitude` | Decimal (opsional) | Koordinat Lokasi |
| `status_verifikasi` | Enum / String | e.g., `lolos_verifikasi_pusat` / `terkirim` |
| `tanggal_pengajuan` | Date / Year (`YYYY-MM-DD` atau `YYYY`) | **Tanggal/Tahun asli pengajuan data lama** |

---

## 🚦 Status & Langkah Selanjutnya
- **Status Sesi Ini:** User sedang melakukan koordinasi dan menunggu keputusan/arahan serta file data dari atasan.
- **Instruksi untuk AI ketika User melanjutkan:**
  1. Tanyakan/konfirmasi ketersediaan file Excel data lama.
  2. Buatkan script Seeder (misal `DataLamaSeeder.php` atau `php artisan import:data-lama`).
  3. Buatkan Controller & View untuk fitur Import Excel di Dashboard Admin ESDM.

---
*Catatan dibuat pada: 13 Agustus 2026*
