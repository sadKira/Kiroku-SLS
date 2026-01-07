<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'id_student',
        'last_name',
        'first_name',
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

    // Search
    public function scopeSearch($query, $value)
    {
        if (empty(trim($value))) {
            return $query;
        }

        $value = strtolower(trim($value));

        // Mapping of full course names to abbreviations
        $courseMap = [
            'bachelor of arts in international studies' => 'abis',
            'bachelor of science in information systems' => 'bsis',
            'bachelor of human services' => 'bhs',
            'bachelor of secondary education' => 'bsed',
            'bachelor of elementary education' => 'eced',
            'bachelor of special needs education' => 'sned',
        ];

        return $query->where(function ($q) use ($value, $courseMap) {
            $q->where('last_name', 'like', "%{$value}%")
                ->orWhere('first_name', 'like', "%{$value}%")
                ->orWhere('year_level', 'like', "%{$value}%")
                ->orWhere('id_student', 'like', "%{$value}%");

            // Check both full and simplified course names
            $q->orWhere(function ($q2) use ($value, $courseMap) {
                // Search full course name
                $q2->where('course', 'like', "%{$value}%");

                // Search mapped simplified names
                foreach ($courseMap as $full => $abbr) {
                    if (str_contains($abbr, $value)) {
                        $q2->orWhere('course', 'like', "%{$full}%");
                    }
                }
            });
        });
    }

}
