<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenarikanSaldo extends Model
{
    use HasFactory;

    protected $table = 'penarikan_saldo';

    protected $fillable = [
        'user_id',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

