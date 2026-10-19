<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Complain extends Model
{
    protected $fillable = [
        'user_id',
        'keluhan',
        'lampiran',
        'tgl_keluhan',
        'tujuan_id',
        'jenis_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_keluhan' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tujuan()
    {
        return $this->belongsTo(Tujuan::class, 'tujuan_id');
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id');
    }

    public function response()
    {
        return $this->hasOne(Response::class, 'complain_id');
    }

    public function lampiranUrl(): ?string
    {
        if (! $this->lampiran) {
            return null;
        }

        if (str_starts_with($this->lampiran, 'http')) {
            return $this->lampiran;
        }

        return Storage::disk('public')->url('lampiran/'.$this->lampiran);
    }
}
