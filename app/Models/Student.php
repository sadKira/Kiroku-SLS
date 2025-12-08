<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'full_name',
        'year_level',
        'course',
    ];

    // Table Relationships

    // Many is to Many
    public function attendanceSessions()
    {
        return $this->belongsToMany(AttendanceSession::class, 'attendance_records')
            ->withPivot('time_in', 'time_out');
    }

    // One is to Many
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

}
