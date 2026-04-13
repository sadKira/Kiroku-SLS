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
     * Auto-generate id_student as a unique random 7-digit ID starting with 9.
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
     * Generate a unique random 7-digit student ID starting with 9.
     * Retries until a non-colliding ID is found.
     */
    public static function generateStudentId(): string
    {
        do {
            // Start with 9, followed by 6 random digits (9000000–9999999)
            $id = (string) random_int(9000000, 9999999);
        } while (static::where('id_student', $id)->exists());

        return $id;
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
