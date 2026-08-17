<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Saku Digital Perangkat Desa 2026 - SIPELITA ESDM</title>
    <style>
        @page { size: A4 portrait; margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; color: #1e293b; line-height: 1.6; }
        .kop-header { text-align: center; border-bottom: 3px double #0f172a; padding-bottom: 12px; margin-bottom: 20px; }
        .kop-header h2 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .kop-header h3 { margin: 2px 0; font-size: 12pt; font-weight: bold; color: #1e3a8a; }
        .kop-header p { margin: 0; font-size: 9pt; color: #64748b; }
        .title-box { text-align: center; background: #f8fafc; border: 1px solid #cbd5e1; padding: 14px; border-radius: 8px; margin-bottom: 20px; }
        .title-box h1 { margin: 0; font-size: 15pt; font-weight: bold; color: #1e3a8a; }
        .title-box p { margin: 4px 0 0 0; font-size: 10pt; color: #475569; font-weight: bold; }
        .chapter { margin-bottom: 20px; }
        .chapter-title { font-size: 12pt; font-weight: bold; color: #1e3a8a; border-left: 4px solid #f59e0b; padding-left: 8px; margin-bottom: 10px; text-transform: uppercase; }
        .content-box { background: #fafafa; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 6px; font-size: 10pt; }
        ol, ul { margin: 4px 0; padding-left: 20px; }
        li { margin-bottom: 4px; }
        .badge { display: inline-block; padding: 2px 8px; background: #e0f2fe; color: #0369a1; border-radius: 4px; font-size: 9pt; font-weight: bold; }
        .footer-note { margin-top: 30px; text-align: center; font-size: 9pt; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #1e3a8a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- KOP RESMI DINAS ESDM -->
    <div class="kop-header">
        <h2>PEMERINTAH PROVINSI JAMBI</h2>
        <h3>DINAS ENERGI DAN SUMBER DAYA MINERAL</h3>
        <p>Jl. H. Agus Salim No. 1, Kota Jambi — Telepon (0741) 60424 | Email: esdm@jambiprov.go.id</p>
    </div>

    <!-- TITLE JUDUL BUKU SAKU -->
    <div class="title-box">
        <h1>BUKU SAKU DIGITAL PERANGKAT DESA</h1>
        <p>Petunjuk Operasional SPBE e-Gov Bantuan Pasang Baru Listrik (BPBL) 2026</p>
    </div>

    <!-- CHAPTER 1 -->
    <div class="chapter">
        <div class="chapter-title">BAB I: TATA CARA VERIFIKASI WARGA DESA</div>
        <div class="content-box">
            <p><strong>Standar Operasional Prosedur (SOP) Verifikasi Kades:</strong></p>
            <ol>
                <li>Petugas melakukan login ke Portal Admin di <code>http://127.0.0.1:8000/login</code>.</li>
                <li>Buka menu <strong>Dasbor Verifikasi Warga</strong> untuk melihat berkas terdaftar di desa.</li>
                <li>Periksa kelengkapan foto KTP, Kartu DTKS/SKTM, dan foto fisik rumah tampak depan.</li>
                <li>Pastikan NIK warga telah berstatus <span class="badge">Terdaftar DTKS Kemensos</span>.</li>
                <li>Jika data layak, klik <strong>[Setujui & Teruskan ke ESDM]</strong>. Jika ada kekurangan, berikan catatan revisi.</li>
            </ol>
        </div>
    </div>

    <!-- CHAPTER 2 -->
    <div class="chapter">
        <div class="chapter-title">BAB II: PENGGUNAAN PWA & SURVEI OFFLINE PELOSOK</div>
        <div class="content-box">
            <p><strong>Fitur Offline PWA & Kompresi Foto Otomatis:</strong></p>
            <ul>
                <li><strong>Instalasi PWA:</strong> Buka aplikasi di Chrome/Edge, lalu klik <em>"Add to Home Screen"</em> agar icon SIPELITA muncul di layar utama HP.</li>
                <li><strong>Kompresi Otomatis:</strong> Foto KTP & Rumah berukuran besar (5-10 MB) akan otomatis dikompresi menjadi <strong>~200-300 KB</strong> oleh sistem tanpa mengurangi kejelasan.</li>
                <li><strong>Mode Offline:</strong> Penginputan di desa tanpa sinyal dapat tetap dilakukan. Data tersimpan di HP dan **otomatis tersinkron** saat HP mendapatkan sinyal kembali.</li>
            </ul>
        </div>
    </div>

    <!-- CHAPTER 3 -->
    <div class="chapter">
        <div class="chapter-title">BAB III: PENGUSULAN JARINGAN LISTRIK DESA (LISDES)</div>
        <div class="content-box">
            <p><strong>Pengusulan Infrastruktur Listrik Komunal Dusun:</strong></p>
            <ol>
                <li>Buka menu <strong>Ajukan Jaringan Lisdes</strong> pada Dasbor Kades.</li>
                <li>Isi nama dusun/RT sasaran, perkiraan jumlah KK belum berlistrik, dan koordinat GPS.</li>
                <li>Unggah berkas Surat Pengantar Kades & Berita Acara Kesediaan Lahan Hibah Tapak Tiang PLN.</li>
            </ol>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer-note">
        Dokumen Buku Saku Digital Resmi Disahkan Oleh Dinas Energi & Sumber Daya Mineral — SPBE e-Government v2.0
    </div>

    <script>
        window.onload = function() {
            // Auto open print preview when opened in popup
            if (window.location.search.includes('print=true')) {
                window.print();
            }
        }
    </script>
</body>
</html>
