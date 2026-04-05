<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructionalLevel extends Model
{
    /** @use HasFactory<\Database\Factories\InstructionalLevelFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];
}
