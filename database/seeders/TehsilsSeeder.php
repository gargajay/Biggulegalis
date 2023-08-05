<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tehsil; // Make sure to import the Tehsil model

class TehsilsSeeder extends Seeder
{
    public function run()
    {
        $districtsAndTehsils = [
            // Punjab
            ['name' => 'Punjab', 'districts' => [
                'Amritsar' => ['Ajnala', 'Amritsar', 'Baba Bakala', 'Fatehgarh Churian', 'Jandiala Guru', 'Patti', 'Rajasansi', 'Rayya'],
                'Barnala' => ['Barnala', 'Handiaya', 'Sehna'],
                'Bathinda' => ['Bathinda', 'Maur', 'Rampura Phul', 'Sangat', 'Talwandi Sabo'],
                'Faridkot' => ['Faridkot', 'Jaitu', 'Kotkapura'],
                'Fatehgarh Sahib' => ['Amloh', 'Bassi Pathana', 'Fatehgarh Sahib', 'Khamano', 'Mandi Gobindgarh', 'Sirsan', 'Tarn Taran'],
                'Fazilka' => ['Abohar', 'Fazilka', 'Jalalabad'],
                'Ferozepur' => ['Ferozepur', 'Ferozepur Cantt.', 'Guru Har Sahai', 'Jalalabad', 'Zira'],
                'Gurdaspur' => ['Batala', 'Dera Baba Nanak', 'Dhariwal', 'Dina Nagar', 'Gurdaspur', 'Pathankot', 'Qadian'],
                'Hoshiarpur' => ['Dasuya', 'Garhshankar', 'Hoshiarpur', 'Mukerian', 'Sham Chaurasi', 'Tanda'],
                'Jalandhar' => ['Adampur', 'Bhogpur', 'Jalandhar', 'Jalandhar Cantt.', 'Nakodar', 'Phillaur', 'Shahkot'],
                'Kapurthala' => ['Bhulath', 'Kapurthala', 'Phagwara', 'Sultanpur Lodhi'],
                'Ludhiana' => ['Doraha', 'Jagraon', 'Khanna', 'Ludhiana East', 'Ludhiana North', 'Ludhiana South', 'Ludhiana West', 'Machhiwara', 'Raikot', 'Samrala', 'Sudhar'],
                'Mansa' => ['Budhlada', 'Mansa', 'Sardulgarh'],
                'Moga' => ['Baghapurana', 'Dharamkot', 'Moga', 'Nihal Singh Wala'],
                'Muktsar' => ['Gidderbaha', 'Malout', 'Muktsar'],
                'Nawanshahr' => ['Balachaur', 'Nawanshahr', 'Rahon'],
                'Pathankot' => ['Dhar Kalan', 'Pathankot'],
                'Patiala' => ['Nabha', 'Patiala', 'Rajpura', 'Samana', 'Sanaur', 'Shutrana'],
                'Rupnagar' => ['Anandpur Sahib', 'Morinda', 'Nangal', 'Rupnagar'],
                'Sangrur' => ['Ahmedgarh', 'Barnala', 'Dhuri', 'Lehragaga', 'Longowal', 'Malerkotla', 'Moonak', 'Sangrur', 'Sunam'],
                'S.A.S. Nagar (Mohali)' => ['Kharar', 'Mohali'],
                'Sri Muktsar Sahib' => ['Giddarbaha', 'Malout'],
                'Tarn Taran' => ['Bhikhiwind', 'Goindwal Sahib', 'Khem Karan', 'Patti', 'Tarn Taran'],
            ]],
            // ... Add more states and districts
        ];

        foreach ($districtsAndTehsils as $stateName => $districts) {
            $state = DB::table('state_bar_councils')->where('name', $stateName)->first();
            if ($state) {
                foreach ($districts as $districtName => $tehsils) {
                    $district = DB::table('district_bar_associations')->where('name', $districtName)->first();
                    if ($district) {
                        info("Inserting tehsil: {$district->id}");
                        foreach ($tehsils as $tehsilName) {
                            Tehsil::firstOrCreate([
                                'name' => $tehsilName,
                                'district_bar_association_id' => $district->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
