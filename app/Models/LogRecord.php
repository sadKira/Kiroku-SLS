<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRecord extends Model
{
    /** @use HasFactory<\Database\Factories\LogRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'loggable_type',
        'student_id',
        'faculty_id',
        'log_session_id',
        'time_in',
        'time_out',
    ];

    // Table Relationships

    // Many is to One
    public function logSessions()
    {
        return $this->belongsTo(LogSession::class, 'log_session_id');
    }

    // Many is to One
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Many is to One
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Get the loggable entity (either Student or Faculty).
     */
    public function loggable()
    {
        if ($this->loggable_type === 'faculty') {
            return $this->faculty();
        }

        return $this->student();
    }

    /**
     * Get the display name for the loggable entity.
     */
    public function getLoggableNameAttribute()
    {
        $entity = $this->loggable_type === 'faculty' ? $this->faculty : $this->student;

        if (!$entity) {
            return 'Unknown User';
        }

        return $entity->last_name . ', ' . $entity->first_name;
    }

    /**
     * Get the loggable entity model instance.
     */
    public function getLoggableEntityAttribute()
    {
        return $this->loggable_type === 'faculty' ? $this->faculty : $this->student;
    }
}
