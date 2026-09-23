<?php

namespace App\Helpers;

use ZipArchive;
use SimpleXMLElement;

class SimpleXlsxReader
{
    /**
     * Membaca seluruh tab/sheet dari file .xlsx secara native tanpa library eksternal.
     *
     * @param string $filePath
     * @return array Matrix 3D: ['Nama Sheet' => [[Baris 1], [Baris 2], ...]]
     */
    public static function parseAllSheets(string $filePath): array
    {
        if (!file_exists($filePath) || strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) !== 'xlsx') {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Baca sharedStrings.xml untuk mapping teks
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string)$val->t;
                    } elseif (isset($val->r)) {
                        $tText = '';
                        foreach ($val->r as $r) {
                            $tText .= (string)$r->t;
                        }
                        $sharedStrings[] = $tText;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Baca workbook.xml untuk ambil relasi ID dan Nama Tab
        $sheetsMeta = [];
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        if ($workbookXml) {
            $xml = @simplexml_load_string($workbookXml);
            if ($xml && isset($xml->sheets->sheet)) {
                foreach ($xml->sheets->sheet as $sheet) {
                    $rId = (string)$sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                    $name = (string)$sheet['name'];
                    $sheetsMeta[$rId] = $name;
                }
            }
        }

        // 3. Baca workbook.xml.rels untuk mapping ID ke nama file sheet
        $sheetFiles = [];
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($relsXml) {
            $xml = @simplexml_load_string($relsXml);
            if ($xml && isset($xml->Relationship)) {
                foreach ($xml->Relationship as $rel) {
                    $rId = (string)$rel['Id'];
                    $target = (string)$rel['Target'];
                    if (isset($sheetsMeta[$rId])) {
                        $sheetName = $sheetsMeta[$rId];
                        $sheetPath = str_starts_with($target, 'worksheets/') ? 'xl/' . $target : 'xl/' . ltrim($target, '/');
                        $sheetFiles[$sheetName] = $sheetPath;
                    }
                }
            }
        }

        $allSheetsData = [];

        // 4. Parse setiap sheet XML
        foreach ($sheetFiles as $sheetName => $sheetXmlPath) {
            $sheetXml = $zip->getFromName($sheetXmlPath);
            if (!$sheetXml) continue;

            $xml = @simplexml_load_string($sheetXml);
            if (!$xml || !isset($xml->sheetData->row)) continue;

            $sheetRows = [];
            foreach ($xml->sheetData->row as $rowXml) {
                $rowCells = [];
                foreach ($rowXml->c as $c) {
                    $cellRef = (string)$c['r'];
                    $cellType = (string)$c['t'];
                    $val = (string)$c->v;

                    // Tentukan Index Kolom (A -> 0, B -> 1, AA -> 26, dst)
                    preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                    $colStr = $matches[1] ?? 'A';
                    $colIdx = 0;
                    for ($i = 0; $i < strlen($colStr); $i++) {
                        $colIdx = $colIdx * 26 + (ord($colStr[$i]) - 64);
                    }
                    $colIdx--;

                    // Parse nilai berdasarkan tipe sel
                    if ($cellType === 's' && isset($sharedStrings[(int)$val])) {
                        $parsedVal = $sharedStrings[(int)$val];
                    } else {
                        $parsedVal = $val;
                    }

                    $rowCells[$colIdx] = $parsedVal;
                }

                // Normalisasi array kolom yang bolong
                if (!empty($rowCells)) {
                    $maxCol = max(array_keys($rowCells));
                    $rowData = [];
                    for ($i = 0; $i <= $maxCol; $i++) {
                        $rowData[$i] = $rowCells[$i] ?? '';
                    }
                    $sheetRows[] = $rowData;
                }
            }

            if (!empty($sheetRows)) {
                $allSheetsData[$sheetName] = $sheetRows;
            }
        }

        $zip->close();
        return $allSheetsData;
    }
}
