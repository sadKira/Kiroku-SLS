<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    /** @use HasFactory<\Database\Factories\FacultyFactory> */
    use HasFactory;

    protected $fillable = [
        'id_faculty',
        'last_name',
        'first_name',
        'instructional_level',
    ];

    /**
     * Boot the model.
     * Auto-generate id_faculty starting with 9 (7 digits).
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($faculty) {
            if (empty($faculty->id_faculty)) {
                $faculty->id_faculty = static::generateFacultyId();
            }
        });
    }

    /**
     * Generate a unique 7-digit faculty ID starting with 9.
     */
    public static function generateFacultyId(): string
    {
        $lastFaculty = static::where('id_faculty', 'like', '9%')
            ->orderByRaw('CAST(id_faculty AS UNSIGNED) DESC')
            ->first();

        if ($lastFaculty) {
            $lastNumber = (int) $lastFaculty->id_faculty;
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

    // Search
    public function scopeSearch($query, $value)
    {
        if (empty(trim($value))) {
            return $query;
        }

        $value = strtolower(trim($value));

        return $query->where(function ($q) use ($value) {
            $q->where('last_name', 'like', "%{$value}%")
                ->orWhere('first_name', 'like', "%{$value}%")
                ->orWhere('id_faculty', 'like', "%{$value}%")
                ->orWhere('instructional_level', 'like', "%{$value}%");
        });
    }
}
