<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran BPBL - {{ $warga->nik }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 15pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #1e3a8a;
        }
        .header p {
            margin: 0;
            font-size: 9pt;
            color: #475569;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0 25px 0;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            color: #64748b;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10pt;
        }
        .info-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .label {
            width: 35%;
            font-weight: bold;
            color: #334155;
        }
        .separator {
            width: 3%;
            text-align: center;
        }
        .value {
            width: 62%;
            color: #0f172a;
        }
        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            font-size: 9pt;
            font-weight: bold;
            border-radius: 4px;
            background-color: #e0f2fe;
            color: #0369a1;
            text-transform: uppercase;
        }
        .qr-section {
            margin-top: 30px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8fafc;
        }
        .qr-table {
            width: 100%;
        }
        .qr-table td {
            vertical-align: middle;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 8pt;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <div class="header">
        <h3>PEMERINTAH PROVINSI JAMBI</h3>
        <h2>DINAS ENERGI DAN SUMBER DAYA MINERAL</h2>
        <p>Jl. H. Agus Salim No. 1, Kota Jambi, Provinsi Jambi | Website: sipelita.esdm.jambiprov.go.id</p>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h4>TANDA TERIMA PENDAFTARAN RESMI BPBL</h4>
        <p>Nomor Registrasi Sistem: <strong>BPBL-{{ date('Ymd', strtotime($warga->created_at)) }}-{{ str_pad($warga->id, 5, '0', STR_PAD_LEFT) }}</strong></p>
    </div>

    <!-- DATA PEMOHON -->
    <table class="info-table">
        <tr>
            <td class="label">Nomor Induk Kependudukan (NIK)</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $warga->nik }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap Pemohon</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $warga->nama }}</strong></td>
        </tr>
        <tr>
            <td class="label">Kabupaten / Kota</td>
            <td class="separator">:</td>
            <td class="value">{{ $warga->kabupaten }}</td>
        </tr>
        <tr>
            <td class="label">Kecamatan</td>
            <td class="separator">:</td>
            <td class="value">{{ $warga->kecamatan }}</td>
        </tr>
        <tr>
            <td class="label">Desa / Kelurahan</td>
            <td class="separator">:</td>
            <td class="value">{{ $warga->desa }}</td>
        </tr>
        <tr>
            <td class="label">RT / RW</td>
            <td class="separator">:</td>
            <td class="value">{{ $warga->rt_rw }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Lengkap Domisili</td>
            <td class="separator">:</td>
            <td class="value">{{ $warga->alamat }}</td>
        </tr>
        <tr>
            <td class="label">Koordinat GPS Rumah (Lat, Lng)</td>
            <td class="separator">:</td>
            <td class="value"><code>{{ $warga->latitude }}, {{ $warga->longitude }}</code></td>
        </tr>
        <tr>
            <td class="label">Tanggal Pendaftaran</td>
            <td class="separator">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($warga->created_at)->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="label">Status Verifikasi Sistem</td>
            <td class="separator">:</td>
            <td class="value">
                <span class="badge-status">
                    @if($warga->status_verifikasi == 'terkirim')
                        PENDING VERIFIKASI DESA
                    @elseif($warga->status_verifikasi == 'disetujui_desa')
                        DISETUJUI DESA (PROSES VERIFIKASI ESDM)
                    @elseif($warga->status_verifikasi == 'lolos_verifikasi_pusat')
                        TERVERIFIKASI DINAS ESDM
                    @elseif($warga->status_verifikasi == 'terpasang')
                        KWH METER LISTRIK TERPASANG (PLN)
                    @else
                        PERLU PERBAIKAN / DITOLAK
                    @endif
                </span>
            </td>
        </tr>
    </table>

    <!-- QR CODE & OTENTIKASI SISTEM -->
    <div class="qr-section">
        <table class="qr-table">
            <tr>
                <td style="width: 130px; text-align: center;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('warga.search', ['nik' => $warga->nik])) }}" alt="QR Code Verifikasi" width="110" height="110" />
                </td>
                <td style="padding-left: 15px;">
                    <strong style="font-size: 10pt; color: #0f172a; display: block; margin-bottom: 4px;">Pindai QR Code untuk Verifikasi Real-time</strong>
                    <p style="margin: 0; font-size: 8.5pt; color: #475569; leading-height: 1.4;">
                        Dokumen ini diterbitkan secara otomatis oleh Sistem Informasi SIPELITA Bantuan Pasang Baru Listrik (BPBL) Dinas ESDM Provinsi Jambi. Scan QR Code di samping menggunakan kamera HP Anda untuk memeriksa pembaruan status verifikasi langsung di portal resmi.
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- SIGNATURE AREA -->
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center; font-size: 9.5pt;">
                <p style="margin: 0;">Jambi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 2px 0 60px 0; font-weight: bold;">Sistem Informasi SIPELITA ESDM</p>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">PANITIA VERIFIKASI BPBL</p>
                <p style="margin: 0; color: #64748b; font-size: 8.5pt;">Dinas ESDM Provinsi Jambi</p>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Simpan tanda terima pendaftaran ini sebagai bukti sah pengajuan Bantuan Pasang Baru Listrik (BPBL) Provinsi Jambi.
    </div>

</body>
</html>
