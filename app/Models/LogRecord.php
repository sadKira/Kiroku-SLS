<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRecord extends Model
{
    /** @use HasFactory<\Database\Factories\LogRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'log_session_id',
        'time_in',
        'time_out',
    ];

    // Table Relationships

    // Many is to One
    public function logSessions()
    {
        return $this->belongsTo(LogSession::class);
    }

    // Many is to One
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
