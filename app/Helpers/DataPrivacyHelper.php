<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Exception;

class DataPrivacyHelper
{
    /**
     * Mask NIK string (e.g. 3515012345678901 -> 3515************)
     */
    public static function maskNik(?string $nik): string
    {
        if (!$nik || strlen($nik) < 6) {
            return $nik ?? '';
        }
        
        $prefix = substr($nik, 0, 4);
        $maskedLength = max(0, strlen($nik) - 4);
        return $prefix . str_repeat('*', $maskedLength);
    }

    /**
     * Mask Nomor KK string
     */
    public static function maskKk(?string $kk): string
    {
        return self::maskNik($kk);
    }

    /**
     * Format tampilan NIK berdasarkan role user yang sedang login
     */
    public static function displayNik(string $nik, $user = null): string
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return self::maskNik($nik);
        }

        // Only ESDM & Super Admin & Kepala Desa of that village can see unmasked NIK
        if ($user->isVerifikatorEsdm() || $user->isSuperAdmin()) {
            return $nik;
        }

        return self::maskNik($nik);
    }

    /**
     * Enkripsi string data sensitif
     */
    public static function encryptData(string $value): string
    {
        return Crypt::encryptString($value);
    }

    /**
     * Dekripsi string data sensitif
     */
    public static function decryptData(string $payload): ?string
    {
        try {
            return Crypt::decryptString($payload);
        } catch (Exception $e) {
            return null;
        }
    }
}
