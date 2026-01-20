<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'lancamentos'; // Portuguese table name

    protected $fillable = [
        'user_id',
        'type', // 'income', 'expense' (keeping internal values in English or Portuguese? User asked for table names. Code values can remain English for standard, or I can change ENUMs too. Let's keep ENUMs English for easier coding unless requested)
        'amount',
        'category',
        'description',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
