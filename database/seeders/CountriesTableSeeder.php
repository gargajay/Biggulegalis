<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    
    public function run()
    {
        DB::table('countries')->truncate();

        $countries = [
            ['name' => 'India'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
