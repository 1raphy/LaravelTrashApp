<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisSampah extends Model
{
    use HasFactory;

    protected $table = 'jenis_sampah';

    protected $fillable = [
        'nama_sampah',
        'harga_per_kg',
    ];

    protected $casts = [
        'harga_per_kg' => 'decimal:2',
    ];

    public function setoranSampah()
    {
        return $this->hasMany(SetoranSampah::class);
    }
}
