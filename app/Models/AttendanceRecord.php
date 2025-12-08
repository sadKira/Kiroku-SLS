<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_session_id',
        'time_in',
        'time_out',
    ];

    // Table Relationships

    // Many is to One
    public function attendanceSessions()
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    // Many is to One
    public function students()
    {
        return $this->belongsTo(Student::class);
    }

   
}
