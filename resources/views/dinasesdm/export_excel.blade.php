{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
{!! '<' . '?mso-application progid="Excel.Sheet"?' . '>' !!}
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="HeaderYellow">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9.5" ss:Color="#000000" ss:Bold="1"/>
   <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataText">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9.5"/>
  </Style>
  <Style ss:ID="DataTextCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9.5"/>
  </Style>
  <Style ss:ID="TitleBold">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Arial" ss:Size="12" ss:Bold="1"/>
  </Style>
  <Style ss:ID="LampiranRight">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Arial" ss:Size="9" ss:Bold="1"/>
  </Style>
  <Style ss:ID="TotalFooter">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9.5" ss:Bold="1"/>
   <Interior ss:Color="#F2F2F2" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="TotalFooterCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9.5" ss:Bold="1"/>
   <Interior ss:Color="#F2F2F2" ss:Pattern="Solid"/>
  </Style>
 </Styles>

 @php
     $sheetName = 'DATA CP-BPBL PROVINSI JAMBI';
     if (!empty($filters['desa']) && $filters['desa'] !== 'Semua Desa') {
         $cleanDesa = trim(explode(',', $filters['desa'])[0]);
         $sheetName = strtoupper(substr($cleanDesa, 0, 31));
     } elseif (!empty($filters['kecamatan']) && $filters['kecamatan'] !== 'Semua Kecamatan') {
         $sheetName = 'KEC ' . strtoupper(substr($filters['kecamatan'], 0, 26));
     } elseif (!empty($filters['kabupaten']) && $filters['kabupaten'] !== 'Semua Kabupaten') {
         $sheetName = 'KAB ' . strtoupper(substr($filters['kabupaten'], 0, 26));
     }
     $sheetName = str_replace(['\\', '/', '?', '*', ':', '[', ']'], '', $sheetName);

     $allColsDef = [
         'no'          => ['title' => 'NO', 'width' => 35, 'style' => 'DataTextCenter'],
         'provinsi'    => ['title' => 'PROVINSI', 'width' => 80, 'style' => 'DataTextCenter'],
         'kabupaten'   => ['title' => 'KABUPATEN / KOTA', 'width' => 140, 'style' => 'DataText'],
         'kecamatan'   => ['title' => 'KECAMATAN', 'width' => 130, 'style' => 'DataText'],
         'desa'        => ['title' => 'DESA / KELURAHAN', 'width' => 160, 'style' => 'DataText'],
         'nama'        => ['title' => 'NAMA KEPALA RMH TANGGA', 'width' => 180, 'style' => 'DataText'],
         'nik'         => ['title' => 'NIK', 'width' => 140, 'style' => 'DataTextCenter'],
         'alamat'      => ['title' => 'ALAMAT (RT/RW)', 'width' => 220, 'style' => 'DataText'],
         'no_hp'       => ['title' => 'NO TELEPON / HP', 'width' => 120, 'style' => 'DataTextCenter'],
         'jarak_tiang' => ['title' => 'JARAK TIANG (M)', 'width' => 130, 'style' => 'DataTextCenter'],
         'status'      => ['title' => 'STATUS VERIFIKASI', 'width' => 140, 'style' => 'DataTextCenter'],
         'tahun'       => ['title' => 'TAHUN USULAN', 'width' => 100, 'style' => 'DataTextCenter'],
         'keterangan'  => ['title' => 'KETERANGAN / CATATAN', 'width' => 180, 'style' => 'DataText'],
     ];

     $selectedCols = $selectedColumns ?? [];
     if (empty($selectedCols)) {
         $activeCols = ['no', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'nama', 'nik', 'alamat', 'jarak_tiang', 'status', 'tahun'];
     } else {
         $activeCols = array_intersect(array_keys($allColsDef), $selectedCols);
         if (empty($activeCols)) {
             $activeCols = array_keys($allColsDef);
         }
     }
     $colCount = count($activeCols);
 @endphp

 <Worksheet ss:Name="{{ $sheetName }}">
  <Table>
   @foreach ($activeCols as $colKey)
    <Column ss:Width="{{ $allColsDef[$colKey]['width'] }}"/>
   @endforeach

   <Row>
    <Cell ss:Index="{{ max(1, $colCount - 3) }}" ss:MergeAcross="{{ min(3, max(0, $colCount - 1)) }}" ss:StyleID="LampiranRight"><Data ss:Type="String">LAMPIRAN</Data></Cell>
   </Row>
   <Row>
    <Cell ss:Index="{{ max(1, $colCount - 3) }}" ss:MergeAcross="{{ min(3, max(0, $colCount - 1)) }}" ss:StyleID="LampiranRight"><Data ss:Type="String">Surat Kepala Dinas ESDM Provinsi Jambi</Data></Cell>
   </Row>
   <Row>
    <Cell ss:Index="{{ max(1, $colCount - 3) }}" ss:StyleID="LampiranRight"><Data ss:Type="String">NOMOR</Data></Cell>
    <Cell ss:MergeAcross="{{ min(2, max(0, $colCount - 2)) }}"><Data ss:Type="String">: {{ $filters['nomor_surat'] }}</Data></Cell>
   </Row>
   <Row>
    <Cell ss:Index="{{ max(1, $colCount - 3) }}" ss:StyleID="LampiranRight"><Data ss:Type="String">TANGGAL</Data></Cell>
    <Cell ss:MergeAcross="{{ min(2, max(0, $colCount - 2)) }}"><Data ss:Type="String">: {{ $filters['tanggal_surat'] }}</Data></Cell>
   </Row>
   <Row/>
   <Row>
    <Cell ss:MergeAcross="{{ max(0, $colCount - 1) }}" ss:StyleID="TitleBold"><Data ss:Type="String">DATA USULAN CALON PENERIMA BPBL TAHUN {{ date('Y') }}</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="{{ max(0, $colCount - 1) }}" ss:StyleID="TitleBold"><Data ss:Type="String">PROVINSI JAMBI</Data></Cell>
   </Row>
   <Row/>

   <!-- Table Header -->
   <Row ss:Height="25">
    @foreach ($activeCols as $colKey)
     <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">{{ $allColsDef[$colKey]['title'] }}</Data></Cell>
    @endforeach
   </Row>

   <!-- Data Rows -->
   @php $lastCategory = null; @endphp
   @forelse ($wargas as $index => $warga)
   @php
       $currentCategory = 'KECAMATAN ' . strtoupper($warga->kecamatan) . ' - DESA/KELURAHAN ' . strtoupper($warga->desa);
       $statusLabel = match($warga->status_verifikasi) {
           'terpasang' => 'Terpasang (Realisasi)',
           'lolos_verifikasi_pusat' => 'Lolos Pusat',
           'menunggu_verifikasi_pusat' => 'Menunggu Pusat',
           'ditolak/perlu_perbaikan' => 'Ditolak',
           default => ucfirst(str_replace('_', ' ', $warga->status_verifikasi)),
       };
       $tahunVal = $warga->tahun_usulan ?: ($warga->created_at ? $warga->created_at->format('Y') : '-');
   @endphp

   @if ($lastCategory !== $currentCategory)
   <Row ss:Height="22">
    <Cell ss:MergeAcross="{{ max(0, $colCount - 1) }}" ss:StyleID="HeaderYellow">
     <Data ss:Type="String">=== KATEGORI: {{ $currentCategory }} ===</Data>
    </Cell>
   </Row>
   @php $lastCategory = $currentCategory; @endphp
   @endif

   <Row>
    @foreach ($activeCols as $colKey)
     @if ($colKey === 'no')
      <Cell ss:StyleID="DataTextCenter"><Data ss:Type="Number">{{ $index + 1 }}</Data></Cell>
     @elseif ($colKey === 'provinsi')
      <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">JAMBI</Data></Cell>
     @elseif ($colKey === 'kabupaten')
      <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->kabupaten) }}</Data></Cell>
     @elseif ($colKey === 'kecamatan')
      <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->kecamatan) }}</Data></Cell>
     @elseif ($colKey === 'desa')
      <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->desa) }}</Data></Cell>
     @elseif ($colKey === 'nama')
      <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->nama) }}</Data></Cell>
     @elseif ($colKey === 'nik')
      <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $warga->nik }}</Data></Cell>
     @elseif ($colKey === 'alamat')
      <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $warga->alamat }} (RT/RW: {{ $warga->rt_rw }})</Data></Cell>
     @elseif ($colKey === 'no_hp')
      <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $warga->no_hp ?: '-' }}</Data></Cell>
     @elseif ($colKey === 'jarak_tiang')
      <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $warga->jarak_tiang ? $warga->jarak_tiang : '-' }}</Data></Cell>
     @elseif ($colKey === 'status')
      <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $statusLabel }}</Data></Cell>
     @elseif ($colKey === 'tahun')
      <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $tahunVal }}</Data></Cell>
     @elseif ($colKey === 'keterangan')
      <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $warga->keterangan_import ?: '-' }}</Data></Cell>
     @endif
    @endforeach
   </Row>
   @empty
   <Row>
    <Cell ss:MergeAcross="{{ max(0, $colCount - 1) }}" ss:StyleID="DataTextCenter"><Data ss:Type="String">Tidak ada data usulan yang sesuai dengan kriteria filter.</Data></Cell>
   </Row>
   @endforelse

   @if(count($wargas) > 0)
   <Row>
    <Cell ss:MergeAcross="{{ max(0, $colCount - 2) }}" ss:StyleID="TotalFooter"><Data ss:Type="String">TOTAL DATA CP-BPBL:</Data></Cell>
    <Cell ss:StyleID="TotalFooterCenter"><Data ss:Type="Number">{{ count($wargas) }}</Data></Cell>
   </Row>
   @endif
  </Table>
 </Worksheet>
</Workbook>
