<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = [
        'complain_id',
        'admin_id',
        'tanggapan',
        'tgl_tanggapan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_tanggapan' => 'date',
        ];
    }

    public function complain()
    {
        return $this->belongsTo(Complain::class, 'complain_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
