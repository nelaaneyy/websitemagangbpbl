<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Exception;

class ExifValidationService
{
    /**
     * Extract EXIF metadata from uploaded image file
     * 
     * @param UploadedFile $file
     * @param float|null $submittedLat Latitude pin lokasi pengajuan
     * @param float|null $submittedLng Longitude pin lokasi pengajuan
     * 
     * @return array Metadata EXIF & status validasi deviasi GPS
     */
    public function validatePhotoExif(UploadedFile $file, ?float $submittedLat = null, ?float $submittedLng = null): array
    {
        $result = [
            'has_exif' => false,
            'exif_latitude' => null,
            'exif_longitude' => null,
            'exif_device' => null,
            'exif_timestamp' => null,
            'is_valid' => true,
            'deviation_meters' => 0.0,
            'warning' => null,
        ];

        // exif_read_data membutuhkan file JPEG/TIFF asli
        $mime = $file->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/jpg', 'image/tiff'])) {
            $result['warning'] = 'Format file bukan JPEG/TIFF, tidak memiliki metadata EXIF GPS.';
            return $result;
        }

        try {
            if (!function_exists('exif_read_data')) {
                $result['warning'] = 'Ekstensi PHP exif tidak aktif.';
                return $result;
            }

            $exif = @exif_read_data($file->getRealPath(), 0, true);

            if (!$exif) {
                $result['warning'] = 'Header EXIF foto tidak ditemukan (foto kemungkinan hasil screenshot atau editan).';
                $result['is_valid'] = false;
                return $result;
            }

            $result['has_exif'] = true;

            // Ekstraksi Merk & Tipe Perangkat
            $make = $exif['IFD0']['Make'] ?? '';
            $model = $exif['IFD0']['Model'] ?? '';
            $result['exif_device'] = trim("{$make} {$model}") ?: 'Kamera Handphone';

            // Ekstraksi Waktu Pengambilan Foto
            $datetime = $exif['EXIF']['DateTimeOriginal'] ?? ($exif['IFD0']['DateTime'] ?? null);
            if ($datetime) {
                $result['exif_timestamp'] = date('Y-m-d H:i:s', strtotime($datetime));
            }

            // Ekstraksi Koordinat GPS dari Tag EXIF
            if (isset($exif['GPS'])) {
                $gps = $exif['GPS'];
                $lat = $this->getGpsCoords($gps['GPSLatitude'] ?? null, $gps['GPSLatitudeRef'] ?? 'N');
                $lng = $this->getGpsCoords($gps['GPSLongitude'] ?? null, $gps['GPSLongitudeRef'] ?? 'E');

                if ($lat !== null && $lng !== null) {
                    $result['exif_latitude'] = $lat;
                    $result['exif_longitude'] = $lng;

                    // Hitung deviasi dengan koordinat pin yang dikirimkan user
                    if ($submittedLat !== null && $submittedLng !== null) {
                        $deviation = SpatialEngine::haversineDistance($submittedLat, $submittedLng, $lat, $lng);
                        $result['deviation_meters'] = $deviation;

                        // Jika deviasi foto vs lokasi pin > 100 meter, flag sebagai dugaan indikasi kecurangan/manipulasi
                        if ($deviation > 100.0) {
                            $result['is_valid'] = false;
                            $result['warning'] = "Terdeteksi deviasi lokasi foto sebesar {$deviation} meter dari titik pin yang dipilih! (Maksimal toleransi: 100m).";
                        }
                    }
                }
            } else {
                $result['warning'] = 'Tag GPS tidak terdeteksi pada biner foto. Harap gunakan aplikasi GPS Camera saat mengambil foto survei.';
                $result['is_valid'] = false;
            }

        } catch (Exception $e) {
            $result['warning'] = 'Gagal membaca metadata biner foto: ' . $e->getMessage();
            $result['is_valid'] = false;
        }

        return $result;
    }

    /**
     * Konversi koordinat GPS EXIF (Degrees, Minutes, Seconds) ke format desimal
     */
    private function getGpsCoords($coordinate, $hemisphere): ?float
    {
        if (!$coordinate || !is_array($coordinate) || count($coordinate) < 3) {
            return null;
        }

        $degrees = $this->evalRational($coordinate[0]);
        $minutes = $this->evalRational($coordinate[1]);
        $seconds = $this->evalRational($coordinate[2]);

        $flip = ($hemisphere === 'W' || $hemisphere === 'S') ? -1 : 1;

        return round($flip * ($degrees + ($minutes / 60) + ($seconds / 3600)), 8);
    }

    private function evalRational($rational): float
    {
        if (is_numeric($rational)) {
            return (float)$rational;
        }
        
        $parts = explode('/', $rational);
        if (count($parts) === 1) {
            return (float)$parts[0];
        }

        if (count($parts) === 2) {
            if ($parts[1] == 0) return 0.0;
            return (float)$parts[0] / (float)$parts[1];
        }

        return 0.0;
    }
}
