<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSession extends Model
{
    /** @use HasFactory<\Database\Factories\LogSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'school_year',
    ];

    // Table Relationships

    // One is to Many
    public function logRecords()
    {
        return $this->hasMany(LogRecord::class);
    }

    // Many is to Many
    public function students()
    {
        return $this->belongsToMany(Student::class, 'log_records')
            ->withPivot('time_in', 'time_out');
    }

}
