<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $companies = [
            [
                'slug' => 'chamonix',
                'name' => 'CHAMONIX',
                'badge_color' => 'primary',
            ],
            [
                'slug' => 'flash',
                'name' => 'FLASH',
                'badge_color' => 'info',
            ],
            [
                'slug' => 'watson',
                'name' => 'WATSON',
                'badge_color' => 'warning',
            ],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['slug' => $company['slug']],
                $company
            );
        }
    }
}
