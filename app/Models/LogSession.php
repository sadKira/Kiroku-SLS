<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LogSession extends Model
{
    /** @use HasFactory<\Database\Factories\LogSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'school_year',
    ];

    // Table Relationships

    // One is to Many
    public function logRecords()
    {
        return $this->hasMany(LogRecord::class);
    }

    // Many is to Many
    public function students()
    {
        return $this->belongsToMany(Student::class, 'log_records')
            ->withPivot('time_in', 'time_out');
    }

    // Search scope - database-agnostic
    public function scopeSearch($query, $value)
    {
        if (empty(trim($value))) {
            return $query;
        }

        $searchTerm = strtolower(trim($value));

        // Month name mapping
        $months = [
            'january' => 1, 'jan' => 1,
            'february' => 2, 'feb' => 2,
            'march' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'may' => 5,
            'june' => 6, 'jun' => 6,
            'july' => 7, 'jul' => 7,
            'august' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9, 'sept' => 9,
            'october' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'december' => 12, 'dec' => 12,
        ];

        // Day name mapping (Carbon: 0=Sunday, 6=Saturday)
        $days = [
            'sunday' => 0, 'sun' => 0,
            'monday' => 1, 'mon' => 1,
            'tuesday' => 2, 'tue' => 2, 'tues' => 2,
            'wednesday' => 3, 'wed' => 3,
            'thursday' => 4, 'thu' => 4, 'thur' => 4, 'thurs' => 4,
            'friday' => 5, 'fri' => 5,
            'saturday' => 6, 'sat' => 6,
        ];

        // Get database connection type once
        $connection = $query->getConnection()->getDriverName();
        
        return $query->where(function ($q) use ($searchTerm, $months, $days, $connection) {
            // Check if search term matches a month name (exact match)
            if (isset($months[$searchTerm])) {
                $q->whereMonth('date', $months[$searchTerm]);
            }

            // Check if search term matches a day name (exact match)
            if (isset($days[$searchTerm])) {
                $dayOfWeek = $days[$searchTerm];
                
                if ($connection === 'sqlite') {
                    // SQLite: strftime('%w', date) returns 0-6 where 0=Sunday
                    $q->orWhereRaw("CAST(strftime('%w', date) AS INTEGER) = ?", [$dayOfWeek]);
                } else {
                    // MySQL: DAYOFWEEK returns 1-7 where 1=Sunday, so we add 1
                    $q->orWhereRaw('DAYOFWEEK(date) = ?', [$dayOfWeek + 1]);
                }
            }

            // Search in academic year (case-insensitive, partial match)
            $q->orWhereRaw('LOWER(school_year) LIKE ?', ["%{$searchTerm}%"]);

            // Search for partial matches in day and month names
            if ($connection === 'sqlite') {
                // SQLite: Use strftime for day and month names
                // %A = full weekday name, %B = full month name
                $q->orWhereRaw("LOWER(strftime('%A', date)) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(strftime('%B', date)) LIKE ?", ["%{$searchTerm}%"]);
            } else {
                // MySQL: Use DATE_FORMAT
                // %W = weekday name, %M = month name
                $q->orWhereRaw('LOWER(DATE_FORMAT(date, "%W")) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(DATE_FORMAT(date, "%M")) LIKE ?', ["%{$searchTerm}%"]);
            }
        });
    }

}
