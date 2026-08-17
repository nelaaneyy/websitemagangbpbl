<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendataan & Cetak Dokumen BPBL ESDM</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        * {
            box-sizing: border-box;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            color: #000000;
            font-size: 9.5pt;
            line-height: 1.35;
        }
        .page {
            width: 100%;
            page-break-after: always;
        }
        .page-last {
            width: 100%;
            page-break-after: avoid;
        }

        /* Kop Surat Resmi Pemprov Jambi */
        .kop-surat-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .kop-surat-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .kop-logo-cell {
            width: 60px;
            padding-right: 14px !important;
            text-align: left;
        }
        .kop-logo-img {
            height: 54px;
            width: auto;
            display: block;
        }
        .kop-text-cell {
            padding-left: 14px !important;
            border-left: 1.5px solid #8c9ba5 !important;
            text-align: left;
            vertical-align: middle;
        }
        .kop-title-prov {
            margin: 0;
            font-size: 14pt;
            font-weight: 800;
            color: #4b6b94;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.1;
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
        }
        .kop-title-dinas {
            margin: 3px 0 0 0;
            font-size: 11.5pt;
            font-weight: 700;
            color: #8c9ba5;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.1;
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
        }
        .kop-bar-bottom {
            width: 100%;
            height: 2.5px;
            background-color: #4b6b94;
            margin-top: 8px;
            margin-bottom: 15px;
        }

        /* Form Title */
        .form-header-title {
            text-align: center;
            font-weight: bold;
            font-size: 10.5pt;
            text-transform: uppercase;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .section-header {
            font-weight: bold;
            font-size: 9.5pt;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        /* Header Info Table */
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 8px;
        }
        table.info-table td {
            padding: 2px 0;
            border: none;
        }

        /* Table Styling */
        table.border-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }
        table.border-table th {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 5px 3px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }
        table.border-table td {
            padding: 4px 4px;
            border: 1px solid #000000;
            vertical-align: middle;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier', monospace; font-size: 8.5pt; }

        /* Signature Table */
        .signature-wrapper {
            width: 100%;
            margin-top: 15px;
        }
        .signature-box {
            width: 260px;
            float: right;
            text-align: center;
            font-size: 9pt;
            line-height: 1.3;
        }
        .signature-space {
            height: 45px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/logo-jambi.png');
    $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
@endphp

    <!-- HALAMAN 1: FORMULIR PENDATAAN PEMETAAN RUMAH TANGGA BERLISTRIK / BELUM BERLISTRIK -->
    <div class="page">
        <table class="kop-surat-table">
            <tr>
                <td class="kop-logo-cell">
                    @if(!empty($logoSrc))
                        <img src="{{ $logoSrc }}" class="kop-logo-img" alt="Logo Pemprov Jambi">
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-title-prov">PEMERINTAH PROVINSI JAMBI</div>
                    <div class="kop-title-dinas">DINAS ENERGI DAN SUMBER DAYA MINERAL</div>
                </td>
            </tr>
        </table>
        <div class="kop-bar-bottom"></div>

        <div class="form-header-title">
            FORMULIR PENDATAAN<br>
            PEMETAAN RUMAH TANGGA BERLISTRIK / BELUM BERLISTRIK PROVINSI JAMBI<br>
            TAHUN {{ date('Y') }}
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 130px;">Desa / Kelurahan</td>
                <td style="width: 12px;">:</td>
                <td style="font-weight: bold;">{{ $filters['desa'] }}</td>
            </tr>
            <tr>
                <td>Kecamatan</td>
                <td>:</td>
                <td>{{ $filters['kecamatan'] }}</td>
            </tr>
            <tr>
                <td>Kabupaten / Kota</td>
                <td>:</td>
                <td>{{ $filters['kabupaten'] }}</td>
            </tr>
        </table>

        <!-- A. Pendataan PLN -->
        <div class="section-header">A. Pendataan Rumah Tangga Berlistrik / Belum Berlistrik oleh PLN</div>
        <p style="margin: 2px 0 4px 12px; font-size: 9pt;">Pendataan Jangkauan Akses Jaringan Listrik PLN di Wilayah Desa/Kelurahan :</p>
        <div style="margin-left: 20px; font-size: 9pt; line-height: 1.5;">
            <div>[✓] Seluruh wilayah Desa/Kelurahan terjangkau akses jaringan listrik PLN</div>
            <div>[ &nbsp; ] Sebagian wilayah Desa/Kelurahan terjangkau akses jaringan listrik PLN</div>
            <div>[ &nbsp; ] Seluruh wilayah Desa/Kelurahan belum terjangkau akses jaringan listrik PLN</div>
        </div>

        <!-- B. Pendataan Penduduk & Rumah -->
        <div class="section-header">B. Pendataan Penduduk dan Jumlah Keseluruhan Rumah di Desa/Kelurahan</div>
        <table style="width: 100%; border: none; font-size: 9pt; margin-left: 12px; line-height: 1.5;">
            <tr><td style="width: 300px; border: none;">Jumlah Penduduk</td><td style="border: none;">: &nbsp;.................... jiwa</td></tr>
            <tr><td style="border: none;">Jumlah Kepala Keluarga</td><td style="border: none;">: &nbsp;.................... KK</td></tr>
            <tr><td style="border: none;">Jumlah Seluruh Rumah yang ada di Desa/Kelurahan</td><td style="border: none;">: &nbsp;.................... rumah</td></tr>
            <tr><td style="border: none;">Jumlah Rumah Berlistrik dari PLN (Pelanggan PLN)</td><td style="border: none;">: &nbsp;.................... rumah</td></tr>
            <tr><td style="border: none;">Jumlah Rumah Belum Berlistrik Sama Sekali</td><td style="border: none;">: &nbsp;<strong>{{ count($wargas) }}</strong> rumah</td></tr>
        </table>

        <!-- C. Pendataan Fasilitas Umum -->
        <div class="section-header">C. Pendataan Fasilitas Umum dan Sosial</div>
        <table class="border-table">
            <thead>
                <tr>
                    <th>Bangunan</th>
                    <th>Jumlah</th>
                    <th>Sumber Listrik</th>
                    <th>Bangunan Sekolah</th>
                    <th>Jumlah</th>
                    <th>Sumber Listrik</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Kantor Desa/Kelurahan</td><td class="text-center">-</td><td class="text-center">PLN</td>
                    <td>TK</td><td class="text-center">-</td><td class="text-center">PLN</td>
                </tr>
                <tr>
                    <td>Puskesmas / Posyandu</td><td class="text-center">-</td><td class="text-center">PLN</td>
                    <td>SD / SMP / SMA</td><td class="text-center">-</td><td class="text-center">PLN</td>
                </tr>
                <tr>
                    <td>Masjid / Mushala</td><td class="text-center">-</td><td class="text-center">PLN</td>
                    <td>Gereja / Wihara / Pura</td><td class="text-center">-</td><td class="text-center">PLN</td>
                </tr>
            </tbody>
        </table>

        <!-- Tanda Tangan Halaman 1 -->
        <div class="signature-wrapper">
            <div class="signature-box">
                <p>Desa/Kelurahan: {{ $filters['desa'] !== 'Semua Desa' ? $filters['desa'] : '....................' }}<br>Tanggal: {{ $filters['tanggal_surat'] }}</p>
                <p><strong>KEPALA DESA / LURAH {{ $filters['desa'] !== 'Semua Desa' ? strtoupper($filters['desa']) : '' }}</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">( {{ $filters['nama_kadis'] ?: '_______________________________' }} )</p>
                <p style="font-size: 8pt;">No. HP: {{ $filters['nip_kadis'] ?: '08............................' }}</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>


    <!-- HALAMAN 2: DATA RUMAH TANGGA DUSUN / KAMPUNG / RT BELUM BERLISTRIK -->
    <div class="page">
        <table class="kop-surat-table">
            <tr>
                <td class="kop-logo-cell">
                    @if(!empty($logoSrc))
                        <img src="{{ $logoSrc }}" class="kop-logo-img" alt="Logo Pemprov Jambi">
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-title-prov">PEMERINTAH PROVINSI JAMBI</div>
                    <div class="kop-title-dinas">DINAS ENERGI DAN SUMBER DAYA MINERAL</div>
                </td>
            </tr>
        </table>
        <div class="kop-bar-bottom"></div>

        <div class="form-header-title">
            DATA RUMAH TANGGA DUSUN / KAMPUNG / RT<br>
            BELUM BERLISTRIK
        </div>

        <table class="border-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>Kabupaten / Kota</th>
                    <th>Kecamatan</th>
                    <th>Desa / Kelurahan</th>
                    <th>Nama Dusun / RT / Kampung</th>
                    <th style="width: 80px;">Jumlah Rumah Tangga</th>
                    <th style="width: 95px;">Jarak Lokasi dari Tiang Listrik Terakhir (M)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wargas->take(15) as $index => $warga)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}.</td>
                        <td>{{ strtoupper($warga->kabupaten) }}</td>
                        <td>{{ strtoupper($warga->kecamatan) }}</td>
                        <td>{{ strtoupper($warga->desa) }}</td>
                        <td>{{ $warga->dusun ?: 'RT/RW: ' . $warga->rt_rw }}</td>
                        <td class="text-center">1</td>
                        <td class="text-center">{{ $warga->jarak_tiang ? $warga->jarak_tiang : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 8px; color: #64748b;">Belum ada data dusun/RT belum berlistrik.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="font-size: 8pt; margin-top: 6px; line-height: 1.3;">
            <strong>*) Keterangan:</strong><br>
            1. Data ini adalah usulan Pembangunan Distribusi Jaringan Listrik (JTM dan JTR);<br>
            2. Data ini sebagai Rasio Dusun Berlistrik Nasional wilayah Provinsi Jambi;<br>
            3. Data yang diusulkan adalah data kondisi saat ini di lokasi.
        </div>

        <div class="signature-wrapper">
            <div class="signature-box">
                <p>{{ $filters['desa'] !== 'Semua Desa' ? $filters['desa'] : 'Jambi' }}, {{ $filters['tanggal_surat'] }}</p>
                <p><strong>KEPALA DESA / LURAH {{ $filters['desa'] !== 'Semua Desa' ? strtoupper($filters['desa']) : '' }}</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">( {{ $filters['nama_kadis'] ?: '_______________________________' }} )</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>


    <!-- HALAMAN 3: DATA RUMAH TANGGA PEMBANGUNAN JARINGAN SWADAYA MASYARAKAT -->
    <div class="page">
        <table class="kop-surat-table">
            <tr>
                <td class="kop-logo-cell">
                    @if(!empty($logoSrc))
                        <img src="{{ $logoSrc }}" class="kop-logo-img" alt="Logo Pemprov Jambi">
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-title-prov">PEMERINTAH PROVINSI JAMBI</div>
                    <div class="kop-title-dinas">DINAS ENERGI DAN SUMBER DAYA MINERAL</div>
                </td>
            </tr>
        </table>
        <div class="kop-bar-bottom"></div>

        <div class="form-header-title">
            DATA RUMAH TANGGA DUSUN / KAMPUNG / RT<br>
            PEMBANGUNAN JARINGAN SWADAYA MASYARAKAT
        </div>

        <table class="border-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>Desa / Kelurahan</th>
                    <th>Nama Dusun / RT / Kampung</th>
                    <th style="width: 80px;">Jumlah Rumah Tangga</th>
                    <th style="width: 95px;">Jarak Lokasi dari Tiang Listrik (M)</th>
                    <th style="width: 110px;">Panjang Jaringan Swadaya (Meter)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wargas->take(10) as $index => $warga)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}.</td>
                        <td>{{ strtoupper($warga->desa) }}</td>
                        <td>{{ $warga->dusun ?: 'RT/RW: ' . $warga->rt_rw }}</td>
                        <td class="text-center">1</td>
                        <td class="text-center">{{ $warga->jarak_tiang ?: '-' }}</td>
                        <td class="text-center">-</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 8px; color: #64748b;">Belum ada data jaringan swadaya masyarakat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="font-size: 8pt; margin-top: 6px; line-height: 1.3;">
            <strong>*) Keterangan:</strong><br>
            1. Data ini adalah Pembangunan Jaringan melalui Dana Anggaran Masyarakat melalui Swadaya;<br>
            2. Data ini sebagai laporan untuk PLN penggunaan jaringan swadaya masyarakat;<br>
            3. Data yang diusulkan adalah data kondisi saat ini di lokasi.
        </div>

        <div class="signature-wrapper">
            <div class="signature-box">
                <p>{{ $filters['desa'] !== 'Semua Desa' ? $filters['desa'] : 'Jambi' }}, {{ $filters['tanggal_surat'] }}</p>
                <p><strong>KEPALA DESA / LURAH {{ $filters['desa'] !== 'Semua Desa' ? strtoupper($filters['desa']) : '' }}</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">( {{ $filters['nama_kadis'] ?: '_______________________________' }} )</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>


    <!-- HALAMAN 4: DAFTAR USULAN LAYAK MENERIMA BANTUAN PASANG BARU LISTRIK (BPBL) -->
    <div class="page">
        <table class="kop-surat-table">
            <tr>
                <td class="kop-logo-cell">
                    @if(!empty($logoSrc))
                        <img src="{{ $logoSrc }}" class="kop-logo-img" alt="Logo Pemprov Jambi">
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-title-prov">PEMERINTAH PROVINSI JAMBI</div>
                    <div class="kop-title-dinas">DINAS ENERGI DAN SUMBER DAYA MINERAL</div>
                </td>
            </tr>
        </table>
        <div class="kop-bar-bottom"></div>

        <div style="width: 100%; font-size: 8.5pt; text-align: right; margin-bottom: 8px; font-weight: bold; text-transform: uppercase;">
            LAMPIRAN : Surat Kepala Desa / Lurah {{ $filters['desa'] }}<br>
            NOMOR : {{ $filters['nomor_surat'] }} &bull; TANGGAL : {{ $filters['tanggal_surat'] }}
        </div>

        <div class="form-header-title">
            DAFTAR USULAN LAYAK MENERIMA<br>
            BANTUAN PASANG BARU LISTRIK (BPBL)<br>
            PROVINSI JAMBI TAHUN {{ date('Y') }}
        </div>

        <table class="border-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th style="width: 80px;">Kabupaten</th>
                    <th style="width: 75px;">Kecamatan</th>
                    <th style="width: 85px;">Desa/Kelurahan</th>
                    <th style="width: 100px;">Nama</th>
                    <th style="width: 100px;">NIK</th>
                    <th>Alamat</th>
                    <th style="width: 65px;">Jarak Tiang (M)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($wargas as $index => $warga)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}.</td>
                        <td>{{ strtoupper($warga->kabupaten) }}</td>
                        <td>{{ strtoupper($warga->kecamatan) }}</td>
                        <td>{{ strtoupper($warga->desa) }}</td>
                        <td><strong>{{ strtoupper($warga->nama) }}</strong></td>
                        <td class="text-center font-mono">{{ $warga->nik }}</td>
                        <td style="font-size: 8pt;">{{ $warga->alamat }} (RT/RW: {{ $warga->rt_rw }})</td>
                        <td class="text-center">{{ $warga->jarak_tiang ? $warga->jarak_tiang : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 10px; color: #64748b;">
                            Tidak ada data usulan calon penerima BPBL.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="font-size: 8pt; margin-top: 6px; line-height: 1.3;">
            <strong>*) Catatan:</strong><br>
            1. Lokasi sudah ada akses jaringan listrik PLN.<br>
            2. Tarikan Kabel Saluran Rumah (SR) maksimal 30 meter dari tiang Listrik/rumah tetangga berlistrik.
        </div>

        <div class="signature-wrapper">
            <div class="signature-box">
                <p>{{ $filters['desa'] !== 'Semua Desa' ? $filters['desa'] : 'Jambi' }}, {{ $filters['tanggal_surat'] }}</p>
                <p><strong>KEPALA DESA / LURAH {{ $filters['desa'] !== 'Semua Desa' ? strtoupper($filters['desa']) : '' }}</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">( {{ $filters['nama_kadis'] ?: '_______________________________' }} )</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>


    <!-- HALAMAN 5: SURAT VALIDASI LAYAK MENERIMA BANTUAN PASANG BARU LISTRIK -->
    <div class="page-last">
        <table class="kop-surat-table">
            <tr>
                <td class="kop-logo-cell">
                    @if(!empty($logoSrc))
                        <img src="{{ $logoSrc }}" class="kop-logo-img" alt="Logo Pemprov Jambi">
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-title-prov">PEMERINTAH PROVINSI JAMBI</div>
                    <div class="kop-title-dinas">DINAS ENERGI DAN SUMBER DAYA MINERAL</div>
                </td>
            </tr>
        </table>
        <div class="kop-bar-bottom"></div>

        <div class="form-header-title" style="margin-top: 10px; margin-bottom: 12px;">
            <u style="font-size: 10.5pt;">VALIDASI LAYAK MENERIMA BANTUAN PASANG BARU LISTRIK</u>
        </div>

        <div style="font-size: 9.5pt; line-height: 1.6; color: #000000; text-align: justify;">
            <p style="text-indent: 25px; margin-bottom: 8px;">
                Sesuai dengan ketentuan Pasal 3 Ayat 2 huruf c Peraturan Menteri Energi dan Sumber Daya Mineral Nomor 3 Tahun 2022 tentang Bantuan Pasang Baru Listrik (BPBL) Bagi Rumah Tangga Tidak Mampu, bahwa salah satu syarat calon penerima BPBL adalah berdasarkan validasi Kepala Desa/Lurah atau Pejabat yang setingkat layak menerima BPBL.
            </p>

            <p style="margin-bottom: 4px;">Untuk maksud tersebut, kami yang bertanda tangan di bawah ini:</p>
            
            <table class="info-table" style="margin: 2px 0 10px 15px; line-height: 1.5;">
                <tr>
                    <td style="width: 130px;">Nama</td>
                    <td style="width: 12px;">:</td>
                    <td style="font-weight: bold;">{{ $filters['nama_kadis'] ?: '................................................................................' }}</td>
                </tr>
                <tr>
                    <td>NIK / NIP</td>
                    <td>:</td>
                    <td>{{ $filters['nip_kadis'] ?: '................................................................................' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Kepala Desa / Lurah {{ $filters['desa'] !== 'Semua Desa' ? $filters['desa'] : '' }}</td>
                </tr>
                <tr>
                    <td>Desa / Kelurahan</td>
                    <td>:</td>
                    <td>{{ $filters['desa'] }}</td>
                </tr>
                <tr>
                    <td>Kecamatan</td>
                    <td>:</td>
                    <td>{{ $filters['kecamatan'] }}</td>
                </tr>
                <tr>
                    <td>Kabupaten / Kota</td>
                    <td>:</td>
                    <td>{{ $filters['kabupaten'] }}</td>
                </tr>
                <tr>
                    <td>Provinsi</td>
                    <td>:</td>
                    <td>Jambi</td>
                </tr>
            </table>

            <p style="text-indent: 25px; margin-bottom: 8px;">
                Berdasarkan pantauan dan verifikasi terhadap sejumlah <strong>{{ count($wargas) }}</strong> rumah tangga sebagaimana terlampir, maka kami menyatakan bahwa terhadap rumah tangga sejumlah tersebut di atas <strong>LAYAK MENERIMA BPBL</strong>.
            </p>

            <p style="text-indent: 25px;">
                Kami mengusulkan kepada Dinas Energi dan Sumber Daya Mineral Provinsi Jambi / Kementerian ESDM RI, kiranya terhadap rumah tangga tersebut di atas dapat direalisasikan bantuan pasang baru listrik.
            </p>
        </div>

        <div class="signature-wrapper" style="margin-top: 15px;">
            <div class="signature-box">
                <p>{{ $filters['desa'] !== 'Semua Desa' ? $filters['desa'] : 'Jambi' }}, {{ $filters['tanggal_surat'] }}</p>
                <p><strong>KEPALA DESA / LURAH {{ $filters['desa'] !== 'Semua Desa' ? strtoupper($filters['desa']) : '' }}</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">( {{ $filters['nama_kadis'] ?: '_______________________________' }} )</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>

</body>
</html>
