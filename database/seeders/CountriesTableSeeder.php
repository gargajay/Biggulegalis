<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country; // Make sure to import the Country model

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'India'],
            ['name' => 'United States'],
            // Add more countries as needed
        ];

        foreach ($countries as $countryData) {
            Country::firstOrCreate(['name' => $countryData['name']], $countryData);
        }
    }
}
