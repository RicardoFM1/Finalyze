<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountShare extends Model
{
    protected $fillable = [
        'owner_id',
        'guest_email',
        'status'
    ];

    public function owner()
    {
        return $this->belongsTo(Usuario::class, 'owner_id');
    }
}
