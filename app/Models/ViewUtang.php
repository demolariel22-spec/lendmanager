<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewUtang extends Model
{
    protected $table = 'utang';
    protected $fillable = [
        'user_id',
        'person_id',
        'item',
        'qty',
        'price',
        'total',
        'status'
    ];
}
