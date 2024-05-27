<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_type',
        'bank_name',
        'card_number',
        'available_limit',
        'holder_dni',
        'holder_first_name',
        'holder_last_name',
    ];
}
