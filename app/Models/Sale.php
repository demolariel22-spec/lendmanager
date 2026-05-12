<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'user_id',
        'product_id',
        'barcode',
        'item',
        'qty',
        'price',
        'total',
    ];
}
