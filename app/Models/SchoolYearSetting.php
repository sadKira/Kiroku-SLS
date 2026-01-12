<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYearSetting extends Model
{
    protected $fillable = [
        'school_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active school year setting
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Set a school year as active and deactivate others
     */
    public static function setActive(string $schoolYear)
    {
        // Deactivate all
        static::query()->update(['is_active' => false]);
        
        // Activate the specified one
        $setting = static::firstOrCreate(
            ['school_year' => $schoolYear],
            ['is_active' => true]
        );
        
        $setting->update(['is_active' => true]);
        
        return $setting;
    }
}
