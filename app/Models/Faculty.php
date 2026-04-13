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
     * Auto-generate id_faculty as a unique random 7-digit ID starting with 1.
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
     * Generate a unique random 7-digit faculty ID starting with 1.
     * Retries until a non-colliding ID is found.
     */
    public static function generateFacultyId(): string
    {
        do {
            // Start with 1, followed by 6 random digits (1000000–1999999)
            $id = (string) random_int(1000000, 1999999);
        } while (static::where('id_faculty', $id)->exists());

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
