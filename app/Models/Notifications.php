<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'status',
        'setoran_sampah_id',
        'penarikan_saldo_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setoranSampah()
    {
        return $this->belongsTo(SetoranSampah::class, 'setoran_sampah_id');
    }

    public function penarikanSaldo()
    {
        return $this->belongsTo(PenarikanSaldo::class, 'penarikan_saldo_id');
    }
}
