<?php

namespace App\Services;

use App\Models\Warga;
use App\Models\ElectricPole;
use App\Models\PengajuanLisdes;
use DOMDocument;

class KmlExporterService
{
    /**
     * Generate OGC Standard KML XML string for Google Earth visualization
     */
    public function generateKml(?string $desa = null): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $kml = $dom->createElementNS('http://www.opengis.net/kml/2.2', 'kml');
        $dom->appendChild($kml);

        $document = $dom->createElement('Document');
        $kml->appendChild($document);

        // Document Metadata
        $name = $dom->createElement('name', htmlspecialchars('SIPELITA WebGIS - Layers Export ' . ($desa ? "({$desa})" : 'Provinsi')));
        $document->appendChild($name);

        $desc = $dom->createElement('description', htmlspecialchars('Peta Geospasial Elektrifikasi Listrik BPBL dan Jaringan Lisdes Dinas ESDM'));
        $document->appendChild($desc);

        // Styles
        $this->addStyles($dom, $document);

        // Folder 1: Penerima Manfaat BPBL
        $folderWarga = $dom->createElement('Folder');
        $folderWarga->appendChild($dom->createElement('name', htmlspecialchars('Penerima Manfaat BPBL')));
        
        $queryWarga = Warga::whereNotNull('latitude')->whereNotNull('longitude');
        if ($desa) {
            $queryWarga->where('desa', 'LIKE', "%{$desa}%");
        }
        $wargas = $queryWarga->get();

        foreach ($wargas as $w) {
            $pm = $dom->createElement('Placemark');
            $pm->appendChild($dom->createElement('name', htmlspecialchars($w->nama) . " ({$w->nik_masked})"));
            
            $balloon = "<div>" .
                "<b>NIK:</b> {$w->nik_masked}<br>" .
                "<b>Alamat:</b> " . htmlspecialchars($w->alamat) . ", Desa " . htmlspecialchars($w->desa) . "<br>" .
                "<b>Status:</b> {$w->status_verifikasi}<br>" .
                "<b>Estimasi Jarak Tiang:</b> {$w->jarak_tiang_calc} m<br>" .
                "</div>";
            $pm->appendChild($dom->createElement('description', htmlspecialchars($balloon)));
            
            $styleUrl = '#style_warga_' . ($w->status_verifikasi === 'terpasang' || $w->status_verifikasi === 'lolos_verifikasi_pusat' ? 'green' : 'orange');
            $pm->appendChild($dom->createElement('styleUrl', $styleUrl));

            $point = $dom->createElement('Point');
            $point->appendChild($dom->createElement('coordinates', "{$w->longitude},{$w->latitude},0"));
            $pm->appendChild($point);

            $folderWarga->appendChild($pm);
        }
        $document->appendChild($folderWarga);

        // Folder 2: Tiang TR/TM & Gardu Listrik
        $folderPoles = $dom->createElement('Folder');
        $folderPoles->appendChild($dom->createElement('name', htmlspecialchars('Tiang Listrik dan Gardu')));
        
        $poles = ElectricPole::all();
        foreach ($poles as $p) {
            $pm = $dom->createElement('Placemark');
            $pm->appendChild($dom->createElement('name', htmlspecialchars($p->kode_tiang) . " [{$p->jenis}]"));
            
            $balloon = "<div><b>Kode:</b> {$p->kode_tiang}<br><b>Jenis:</b> {$p->jenis}<br><b>Kapasitas:</b> {$p->kapasitas_kva} kVA</div>";
            $pm->appendChild($dom->createElement('description', htmlspecialchars($balloon)));
            $pm->appendChild($dom->createElement('styleUrl', $p->jenis === 'Gardu' ? '#style_gardu' : '#style_tiang'));

            $point = $dom->createElement('Point');
            $point->appendChild($dom->createElement('coordinates', "{$p->longitude},{$p->latitude},0"));
            $pm->appendChild($point);

            $folderPoles->appendChild($pm);
        }
        $document->appendChild($folderPoles);

        return $dom->saveXML();
    }

    private function addStyles(DOMDocument $dom, $document)
    {
        // Style Warga Green
        $styleGreen = $dom->createElement('Style');
        $styleGreen->setAttribute('id', 'style_warga_green');
        $iconStyle = $dom->createElement('IconStyle');
        $iconStyle->appendChild($dom->createElement('scale', '1.1'));
        $icon = $dom->createElement('Icon');
        $icon->appendChild($dom->createElement('href', 'http://maps.google.com/mapfiles/kml/paddle/grn-circle.png'));
        $iconStyle->appendChild($icon);
        $styleGreen->appendChild($iconStyle);
        $document->appendChild($styleGreen);

        // Style Warga Orange
        $styleOrange = $dom->createElement('Style');
        $styleOrange->setAttribute('id', 'style_warga_orange');
        $iconStyle = $dom->createElement('IconStyle');
        $iconStyle->appendChild($dom->createElement('scale', '1.1'));
        $icon = $dom->createElement('Icon');
        $icon->appendChild($dom->createElement('href', 'http://maps.google.com/mapfiles/kml/paddle/ylw-circle.png'));
        $iconStyle->appendChild($icon);
        $styleOrange->appendChild($iconStyle);
        $document->appendChild($styleOrange);

        // Style Tiang
        $styleTiang = $dom->createElement('Style');
        $styleTiang->setAttribute('id', 'style_tiang');
        $iconStyle = $dom->createElement('IconStyle');
        $iconStyle->appendChild($dom->createElement('scale', '0.9'));
        $icon = $dom->createElement('Icon');
        $icon->appendChild($dom->createElement('href', 'http://maps.google.com/mapfiles/kml/shapes/info-i.png'));
        $iconStyle->appendChild($icon);
        $styleTiang->appendChild($iconStyle);
        $document->appendChild($styleTiang);

        // Style Gardu
        $styleGardu = $dom->createElement('Style');
        $styleGardu->setAttribute('id', 'style_gardu');
        $iconStyle = $dom->createElement('IconStyle');
        $iconStyle->appendChild($dom->createElement('scale', '1.3'));
        $icon = $dom->createElement('Icon');
        $icon->appendChild($dom->createElement('href', 'http://maps.google.com/mapfiles/kml/shapes/electric.png'));
        $iconStyle->appendChild($icon);
        $styleGardu->appendChild($iconStyle);
        $document->appendChild($styleGardu);
    }
}
