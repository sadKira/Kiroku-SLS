<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'school_year',
    ];

    // Table Relationships

    // One is to Many
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    // Many is to Many
    public function students()
    {
        return $this->belongsToMany(Student::class, 'attendance_records')
            ->withPivot('time_in', 'time_out');
    }
   
}
