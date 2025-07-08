<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetoranSampah extends Model
{
    use HasFactory;

    protected $table = 'setoran_sampah';

    protected $fillable = [
        'user_id',
        'jenis_sampah_id',
        'berat_kg',
        'total_harga',
        'metode_penjemputan',
        'alamat_penjemputan',
        'catatan_tambahan',
        'status',
    ];

    protected $casts = [
        'berat_kg' => 'float',
        'total_harga' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class);
    }
}
