<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'code', 'name', 'states'
    ];

    public $timestamps = false;

    use HasFactory;
}
