<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set the super admin key
        $existing = Setting::where('key', 's_a_k')->first();

        if (!$existing) {
            Setting::updateOrCreate(
                ['key' => 's_a_k'],
                ['value' =>Hash::make('123456')] // Key
            );
        }
    }
}
