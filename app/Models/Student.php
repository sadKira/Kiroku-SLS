<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_type',
        'id_student',
        'last_name',
        'first_name',
        'year_level',
        'course',
        'strand',
    ];

    /**
     * Boot the model.
     * Auto-generate id_student starting with 9 (7 digits).
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->id_student)) {
                $student->id_student = static::generateStudentId();
            }
        });
    }

    /**
     * Generate a unique 7-digit student ID starting with 9.
     */
    public static function generateStudentId(): string
    {
        $lastStudent = static::where('id_student', 'like', '9%')
            ->orderByRaw('CAST(id_student AS UNSIGNED) DESC')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) $lastStudent->id_student;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 9000001;
        }

        return str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    }

    // Table Relationships

    // One is to Many
    public function logRecords()
    {
        return $this->hasMany(LogRecord::class);
    }

    // Many is to Many
    public function logSessions()
    {
        return $this->belongsToMany(LogSession::class, 'log_records')
            ->withPivot('time_in', 'time_out');
    }

    // Scope: College students only
    public function scopeCollege($query)
    {
        return $query->where('user_type', 'college');
    }

    // Scope: SHS students only
    public function scopeShs($query)
    {
        return $query->where('user_type', 'shs');
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
                ->orWhere('id_student', 'like', "%{$value}%")
                ->orWhere('strand', 'like', "%{$value}%");

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
