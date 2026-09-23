<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Verifikasi Lapangan (BAVL) - {{ $warga->nik }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; color: #1f2937; line-height: 1.5; margin: 20px; }
        .kop-header { text-align: center; border-bottom: 3px double #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-header h2 { margin: 0; font-size: 14pt; color: #1e3a8a; text-transform: uppercase; }
        .kop-header h3 { margin: 2px 0; font-size: 12pt; color: #374151; }
        .kop-header p { margin: 0; font-size: 9pt; color: #6b7280; }
        .doc-title { text-align: center; font-weight: bold; font-size: 13pt; text-decoration: underline; margin-bottom: 5px; }
        .doc-number { text-align: center; font-size: 10pt; color: #4b5563; margin-bottom: 25px; }
        table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.meta-table td { padding: 5px 8px; vertical-align: top; }
        table.meta-table td.label { width: 30%; font-weight: bold; color: #374151; }
        table.grid-table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        table.grid-table th, table.grid-table td { border: 1px solid #cbd5e1; padding: 8px; font-size: 10pt; }
        table.grid-table th { background-color: #f1f5f9; color: #1e293b; text-align: left; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 9pt; }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .signature-section { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 45%; text-align: center; }
        .signature-box-left { float: left; width: 45%; text-align: center; }
        .clear { clear: both; }
        .qr-placeholder { border: 1px dashed #94a3b8; padding: 10px; width: 110px; height: 110px; margin: 10px auto; font-size: 8pt; color: #64748b; }
    </style>
</head>
<body>
    <div class="kop-header">
        <h2>PEMERINTAH PROVINSI / KABUPATEN</h2>
        <h3>DINAS ENERGI DAN SUMBER DAYA MINERAL (ESDM)</h3>
        <p>Sistem Informasi Pelayanan Listrik Terpadu (SIPELITA) BPBL & Lisdes</p>
    </div>

    <div class="doc-title">BERITA ACARA VERIFIKASI LAPANGAN (BAVL)</div>
    <div class="doc-number">Nomor: {{ $nomorBavl }}</div>

    <p>Pada hari ini, tanggal <b>{{ $tanggalSurat }}</b>, telah dilakukan verifikasi geospasial dan analisis kelayakan teknis lapangan terhadap permohonan Bantuan Pasang Baru Listrik (BPBL) / Lisdes dengan rincian calon penerima manfaat sebagai berikut:</p>

    <table class="meta-table">
        <tr>
            <td class="label">Nama Pemohon / KK</td>
            <td>: {{ $warga->nama }}</td>
        </tr>
        <tr>
            <td class="label">NIK (Identitas Resmi)</td>
            <td>: {{ $warga->nik }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Lengkap</td>
            <td>: {{ $warga->alamat }}, RT/RW {{ $warga->rt_rw }}</td>
        </tr>
        <tr>
            <td class="label">Wilayah Administrasi</td>
            <td>: Desa {{ $warga->desa }}, Kec. {{ $warga->kecamatan }}, Kab. {{ $warga->kabupaten }}</td>
        </tr>
        <tr>
            <td class="label">Koordinat GPS Pinning</td>
            <td>: Lat {{ $warga->latitude }}, Lng {{ $warga->longitude }}</td>
        </tr>
        <tr>
            <td class="label">Status Verifikasi System</td>
            <td>: <span class="badge badge-success">{{ strtoupper($warga->status_verifikasi) }}</span></td>
        </tr>
    </table>

    <h4>HASIL EVALUASI GEOSPASIAL & ANTI-FRAUD</h4>
    <table class="grid-table">
        <thead>
            <tr>
                <th>Parameter Evaluasi</th>
                <th>Hasil Pengukuran / Ekstraksi</th>
                <th>Kategori Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Jarak Tiang Listrik TR Terdekat</td>
                <td>{{ number_format($warga->jarak_tiang_calc ?? 0, 1) }} Meter</td>
                <td>{{ ($warga->jarak_tiang_calc ?? 0) <= 200 ? 'Memenuhi Syarat BPBL (<= 200m)' : 'Perluasan Lisdes (> 200m)' }}</td>
            </tr>
            <tr>
                <td>Ekstraksi Biner EXIF Foto GPS</td>
                <td>
                    Lat: {{ $warga->exif_latitude ?? 'N/A' }}, Lng: {{ $warga->exif_longitude ?? 'N/A' }}<br>
                    Perangkat: {{ $warga->exif_device ?? 'GPS Camera App' }}
                </td>
                <td>{{ $warga->is_exif_valid ? 'Valid (Bebas Manipulasi)' : 'Flagged (Deviasi GPS / No EXIF)' }}</td>
            </tr>
            <tr>
                <td>3-Layer Duplicate Checking</td>
                <td>{{ $warga->catatan_duplikasi ?? 'Bebas Duplikasi' }}</td>
                <td>{{ strtoupper($warga->risiko_duplikasi ?? 'Rendah') }}</td>
            </tr>
        </tbody>
    </table>

    <p>Demikian Berita Acara Verifikasi Lapangan (BAVL) ini diterbitkan secara sah melalui sistem e-Government WebGIS SIPELITA Dinas ESDM sebagai acuan penerbitan Work Order (WO) Pemasangan Instalasi Listrik oleh Vendor PLN.</p>

    <div class="signature-section">
        <div class="signature-box-left">
            <p>Petugas Survey Lapangan,</p>
            <br><br><br>
            <p><b>( ........................................ )</b></p>
        </div>
        <div class="signature-box">
            <p>Verifikator Resmi Dinas ESDM,</p>
            <div class="qr-placeholder">
                <br><b>QR CODE VERIFIKASI</b><br>{{ $nomorBavl }}
            </div>
            <p><b>{{ $verifikator }}</b></p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
