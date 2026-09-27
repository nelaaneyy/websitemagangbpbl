<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class, 'kabupaten');
    }

    public function desas()
    {
        return $this->hasMany(Desa::class, 'kabupaten');
    }
}
