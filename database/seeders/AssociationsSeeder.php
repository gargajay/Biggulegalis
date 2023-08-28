<?php

namespace Database\Seeders;

use App\Models\Association;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StateBarCouncil; // Make sure to import the StateBarCouncil model

class AssociationsSeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'India','association_type'=>1],
            ['name' => 'United States','association_type'=>1],
            // Add more countries as needed
        ];


        $states = [          
            ['name' => 'Haryana', 'parent_id' => 1,'association_type'=>2],
            ['name' => 'Punjab', 'parent_id' => 1,'association_type'=>2],
            ['name' => 'Rajasthan', 'parent_id' => 1,'association_type'=>2],
        
        ];

        $statesWithDistricts = [
            

          

           
            // Haryana
            ['name' => 'Haryana', 'districts' => [
                'Ambala', 'Bhiwani', 'Charkhi Dadri', 'Faridabad', 'Fatehabad', 'Gurgaon', 'Hisar', 'Jhajjar', 'Jind', 'Kaithal', 'Karnal', 'Kurukshetra', 'Mahendragarh', 'Nuh', 'Palwal', 'Panchkula', 'Panipat', 'Rewari', 'Rohtak', 'Sirsa', 'Sonipat', 'Yamunanagar',
            ]],

          
          

            // Punjab
            ['name' => 'Punjab', 'districts' => [
                'Amritsar', 'Barnala', 'Bathinda', 'Faridkot', 'Fatehgarh Sahib', 'Fazilka', 'Ferozepur', 'Gurdaspur', 'Hoshiarpur', 'Jalandhar', 'Kapurthala', 'Ludhiana', 'Mansa', 'Moga', 'Muktsar', 'Nawanshahr', 'Pathankot', 'Patiala', 'Rupnagar', 'Sangrur', 'S.A.S. Nagar (Mohali)', 'Sri Muktsar Sahib', 'Tarn Taran',
            ]],

            // Rajasthan
            ['name' => 'Rajasthan', 'districts' => [
                'Ajmer', 'Alwar', 'Banswara', 'Baran', 'Barmer', 'Bharatpur', 'Bhilwara', 'Bikaner', 'Bundi', 'Chittorgarh', 'Churu', 'Dausa', 'Dholpur', 'Dungarpur', 'Hanumangarh', 'Jaipur', 'Jaisalmer', 'Jalore', 'Jhalawar', 'Jhunjhunu', 'Jodhpur', 'Karauli', 'Kota', 'Nagaur', 'Pali', 'Pratapgarh', 'Rajsamand', 'Sawai Madhopur', 'Sikar', 'Sirohi', 'Sri Ganganagar', 'Tonk', 'Udaipur',
            ]],




        ];

        $districtsAndTehsils = [
            // Punjab
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

            // ... other districts and tehsils
        ];

       

        foreach ($countries as $countryData) {
            Association::firstOrCreate(['name' => $countryData['name']], $countryData);
        }

        foreach ($states as $stateData) {
            Association::firstOrNew(['name' => $stateData['name']], $stateData)->save();
        }

        foreach ($statesWithDistricts as $stateData) {
            $state = DB::table('associations')->where('name', $stateData['name'])->first();
            if ($state) {
                foreach ($stateData['districts'] as $districtName) {
                    Association::firstOrCreate([
                        'name' => $districtName,
                        'parent_id' => $state->id,
                        'association_type'=>3
                    ]);
                }
            }
        }


        foreach ($districtsAndTehsils as $districtName => $tehsils) {
            $district = DB::table('associations')->where('name', $districtName)->first();
            if ($district) {
                foreach ($tehsils as $tehsilName) {
                    Association::firstOrCreate([
                        'name' => $tehsilName,
                        'parent_id' => $district->id,
                        'association_type'=>4

                    ]);
                }
            }
        }
    }
}
