<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StateBarCouncil; // Make sure to import the StateBarCouncil model

class IndianStatesSeeder extends Seeder
{
    public function run()
    {
        $states = [
            ['name' => 'Andhra Pradesh', 'country_id' => 1],
            ['name' => 'Arunachal Pradesh', 'country_id' => 1],
            ['name' => 'Assam', 'country_id' => 1],
            ['name' => 'Bihar', 'country_id' => 1],
            ['name' => 'Chhattisgarh', 'country_id' => 1],
            ['name' => 'Dadra and Nagar Haveli', 'country_id' => 1],
            ['name' => 'Daman and Diu', 'country_id' => 1],
            ['name' => 'Delhi', 'country_id' => 1],
            ['name' => 'Goa', 'country_id' => 1],
            ['name' => 'Gujarat', 'country_id' => 1],
            ['name' => 'Haryana', 'country_id' => 1],
            ['name' => 'Himachal Pradesh', 'country_id' => 1],
            ['name' => 'Jammu and Kashmir', 'country_id' => 1],
            ['name' => 'Jharkhand', 'country_id' => 1],
            ['name' => 'Karnataka', 'country_id' => 1],
            ['name' => 'Kerala', 'country_id' => 1],
            ['name' => 'Lakshadweep', 'country_id' => 1],
            ['name' => 'Madhya Pradesh', 'country_id' => 1],
            ['name' => 'Maharashtra', 'country_id' => 1],
            ['name' => 'Manipur', 'country_id' => 1],
            ['name' => 'Meghalaya', 'country_id' => 1],
            ['name' => 'Mizoram', 'country_id' => 1],
            ['name' => 'Nagaland', 'country_id' => 1],
            ['name' => 'Odisha', 'country_id' => 1],
            ['name' => 'Puducherry', 'country_id' => 1],
            ['name' => 'Punjab', 'country_id' => 1],
            ['name' => 'Rajasthan', 'country_id' => 1],
            ['name' => 'Sikkim', 'country_id' => 1],
            ['name' => 'Tamil Nadu', 'country_id' => 1],
            ['name' => 'Telangana', 'country_id' => 1],
            ['name' => 'Tripura', 'country_id' => 1],
            ['name' => 'Uttar Pradesh', 'country_id' => 1],
            ['name' => 'Uttarakhand', 'country_id' => 1],
            ['name' => 'West Bengal', 'country_id' => 1],
        ];

        foreach ($states as $stateData) {
            StateBarCouncil::firstOrNew(['name' => $stateData['name']], $stateData)->save();
        }
    }
}
