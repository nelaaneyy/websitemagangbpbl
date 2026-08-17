{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
{!! '<?mso-application progid="Excel.Sheet"?>' !!}
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
 @endphp

 <Worksheet ss:Name="{{ $sheetName }}">
  <Table>
   <Column ss:Width="35"/>
   <Column ss:Width="80"/>
   <Column ss:Width="140"/>
   <Column ss:Width="130"/>
   <Column ss:Width="160"/>
   <Column ss:Width="180"/>
   <Column ss:Width="140"/>
   <Column ss:Width="220"/>
   <Column ss:Width="150"/>
   <Column ss:Width="120"/>
   
   <Row>
    <Cell ss:Index="7" ss:MergeAcross="3" ss:StyleID="LampiranRight"><Data ss:Type="String">LAMPIRAN</Data></Cell>
   </Row>
   <Row>
    <Cell ss:Index="7" ss:MergeAcross="3" ss:StyleID="LampiranRight"><Data ss:Type="String">Surat Kepala Dinas ESDM Provinsi Jambi</Data></Cell>
   </Row>
   <Row>
    <Cell ss:Index="7" ss:StyleID="LampiranRight"><Data ss:Type="String">NOMOR</Data></Cell>
    <Cell ss:MergeAcross="2"><Data ss:Type="String">: {{ $filters['nomor_surat'] }}</Data></Cell>
   </Row>
   <Row>
    <Cell ss:Index="7" ss:StyleID="LampiranRight"><Data ss:Type="String">TANGGAL</Data></Cell>
    <Cell ss:MergeAcross="2"><Data ss:Type="String">: {{ $filters['tanggal_surat'] }}</Data></Cell>
   </Row>
   <Row/>
   <Row>
    <Cell ss:MergeAcross="9" ss:StyleID="TitleBold"><Data ss:Type="String">DATA USULAN CALON PENERIMA BPBL TAHUN {{ date('Y') }}</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="9" ss:StyleID="TitleBold"><Data ss:Type="String">PROVINSI JAMBI</Data></Cell>
   </Row>
   <Row/>
   
   <!-- Table Header -->
   <Row ss:Height="25">
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">NO</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">PROVINSI</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">KABUPATEN</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">KECAMATAN</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">DESA / KELURAHAN / DUSUN</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">NAMA KEPALA RMH TANGGA</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">NIK</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">ALAMAT</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">JARAK DARI SUMBER LISTRIK (METER)</Data></Cell>
    <Cell ss:StyleID="HeaderYellow"><Data ss:Type="String">STATUS VERIFIKASI</Data></Cell>
   </Row>

   <!-- Data Rows -->
   @forelse ($wargas as $index => $warga)
   @php
       $statusLabel = match($warga->status_verifikasi) {
           'lolos_verifikasi_pusat' => 'Lolos Pusat',
           'menunggu_verifikasi_pusat' => 'Menunggu Pusat',
           'ditolak/perlu_perbaikan' => 'Ditolak',
           default => ucfirst(str_replace('_', ' ', $warga->status_verifikasi)),
       };
   @endphp
   <Row>
    <Cell ss:StyleID="DataTextCenter"><Data ss:Type="Number">{{ $index + 1 }}</Data></Cell>
    <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">JAMBI</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->kabupaten) }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->kecamatan) }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->desa) }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ strtoupper($warga->nama) }}</Data></Cell>
    <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $warga->nik }}</Data></Cell>
    <Cell ss:StyleID="DataText"><Data ss:Type="String">{{ $warga->alamat }} (RT/RW: {{ $warga->rt_rw }})</Data></Cell>
    <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $warga->jarak_tiang ? $warga->jarak_tiang : '-' }}</Data></Cell>
    <Cell ss:StyleID="DataTextCenter"><Data ss:Type="String">{{ $statusLabel }}</Data></Cell>
   </Row>
   @empty
   <Row>
    <Cell ss:MergeAcross="9" ss:StyleID="DataTextCenter"><Data ss:Type="String">Tidak ada data usulan yang sesuai dengan kriteria filter.</Data></Cell>
   </Row>
   @endforelse

   @if(count($wargas) > 0)
   <Row>
    <Cell ss:MergeAcross="8" ss:StyleID="TotalFooter"><Data ss:Type="String">TOTAL USULAN CP-BPBL:</Data></Cell>
    <Cell ss:StyleID="TotalFooterCenter"><Data ss:Type="Number">{{ count($wargas) }}</Data></Cell>
   </Row>
   @endif
  </Table>
 </Worksheet>
</Workbook>
