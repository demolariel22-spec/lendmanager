<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addperson extends Model
{
    protected $table = 'persons';
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'total'
    ];
}
