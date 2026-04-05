<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strand extends Model
{
    /** @use HasFactory<\Database\Factories\StrandFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];
}
