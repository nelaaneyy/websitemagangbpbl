<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Courier', 'Arial', sans-serif; font-size: 10pt; color: #0f172a; margin: 15px; }
        .border-box { border: 2px solid #0f172a; padding: 15px; min-height: 500px; }
        .header-title { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 15px; }
        .header-title h2 { margin: 0; font-size: 14pt; color: #1e3a8a; }
        .header-title h4 { margin: 3px 0; font-size: 11pt; color: #475569; }
        table.grid { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.grid th, table.grid td { border: 1px solid #475569; padding: 6px; font-size: 9pt; }
        table.grid th { background-color: #e2e8f0; text-align: left; }
        .map-canvas-mock { border: 1px dashed #64748b; background-color: #f8fafc; height: 220px; text-align: center; line-height: 200px; color: #94a3b8; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="border-box">
        <div class="header-title">
            <h2>DINAS ENERGI DAN SUMBER DAYA MINERAL (ESDM)</h2>
            <h4>{{ $title }}</h4>
            <p style="margin:0; font-size:8pt;">Sistem Informasi Pelayanan Listrik Terpadu (SIPELITA) - Modul Perencanaan Lisdes</p>
        </div>

        <div class="map-canvas-mock">
            [ TAMPILAN SKETSA GEOSPASIAL JARINGAN LISDES (HUB-AND-SPOKE & DAISY CHAIN TOPOLOGY) ]
        </div>

        <h4>RINCIAN ELEMEN PERENCANAAN LISDES:</h4>
        <table class="grid">
            <thead>
                <tr>
                    <th>Atribut Parameter</th>
                    <th>Nilai Spesifikasi Jaringan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Kode / Lokasi Desa</td>
                    <td>{{ $data->kode_cluster ?? 'USULAN-LISDES' }} - Desa {{ $data->desa }}</td>
                </tr>
                <tr>
                    <td>Jumlah Usulan Tercover</td>
                    <td>{{ $data->jumlah_usulan ?? $data->jumlah_kk ?? 1 }} KK / Rumah Tangga</td>
                </tr>
                <tr>
                    <td>Total Estimasi Panjang Jaringan</td>
                    <td>{{ number_format($data->total_panjang_jaringan_meter ?? $data->estimasi_jarak ?? 0, 0) }} Meter (Kabel JTM/JTR)</td>
                </tr>
                <tr>
                    <td>Titik Centroid Spasial</td>
                    <td>Lat: {{ $data->centroid_lat ?? $data->latitude }}, Lng: {{ $data->centroid_lng ?? $data->longitude }}</td>
                </tr>
                <tr>
                    <td>Estimasi Anggaran Perluasan</td>
                    <td>Rp {{ number_format($data->estimasi_anggaran ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <p>Diterbitkan Oleh WebGIS SIPELITA - Dinas ESDM<br>Tanggal: {{ date('d F Y') }}</p>
        </div>
    </div>
</body>
</html>
