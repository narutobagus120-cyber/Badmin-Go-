<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    protected $table = 'lapangan';

    protected $primaryKey = 'id_lapangan';

    protected $fillable = [
        'nama_lapangan',
        'jenis_lapangan',
        'fasilitas',
        'harga_per_jam',
        'status_lapangan',
    ];
}