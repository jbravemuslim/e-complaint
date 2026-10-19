<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tujuan extends Model
{
    protected $fillable = ['nama_unit'];

    public function complains()
    {
        return $this->hasMany(Complain::class, 'tujuan_id');
    }
}
