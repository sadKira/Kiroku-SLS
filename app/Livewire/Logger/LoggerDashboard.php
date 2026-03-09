<?php

namespace App\Livewire\Logger;

use App\Models\LogSession;
use App\Models\SchoolYearSetting;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.logger-app')]
class LoggerDashboard extends Component
{
    public function getTodayLogSessionProperty()
    {
        $today = Carbon::now('Asia/Manila')->format('Y-m-d');
        $activeSetting = SchoolYearSetting::getActive();
        $schoolYear = $activeSetting ? $activeSetting->school_year : null;

        if (!$schoolYear) {
            return null;
        }

        return LogSession::where('date', $today)
            ->where('school_year', $schoolYear)
            ->withCount(['logRecords', 'students'])
            ->first();
    }

    public function getActiveSchoolYearProperty()
    {
        $setting = SchoolYearSetting::getActive();

        return $setting ? $setting->school_year : null;
    }

    public function render()
    {
        return view('livewire.logger.logger-dashboard', [
            'todayLogSession' => $this->todayLogSession,
            'activeSchoolYear' => $this->activeSchoolYear,
        ]);
    }
}
