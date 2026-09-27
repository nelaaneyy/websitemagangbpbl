<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kabupaten;
use App\Models\Desa;

class Kecamatan extends Model
{
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten');
    }

    public function desas()
    {
        return $this->hasMany(Desa::class, 'kecamatan_id');
    }
}
