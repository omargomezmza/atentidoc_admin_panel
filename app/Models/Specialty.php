<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $table = 'specialty';
    
    protected $fillable = [
        'name',
        'slug',
    ];
}
