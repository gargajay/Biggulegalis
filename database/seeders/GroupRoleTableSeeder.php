<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country; // Make sure to import the Country model
use App\Models\GroupRole;

class GroupRoleTableSeeder extends Seeder
{
    public function run()
    {
        $role = [
            ['name' => 'Office Bear','parent_id'=>0],
            ['name' => 'Notary public','parent_id'=>0],
            ['name' => 'Auth Commissioner','parent_id'=>0],
            ['name' => 'President','parent_id'=>1],
            ['name' => 'Vice president','parent_id'=>1],
            ['name' => 'Secretary','parent_id'=>1],
            ['name' => 'Joint Secretary','parent_id'=>1],
        ];

        foreach ($role as $roleData) {
            GroupRole::firstOrCreate(['name' => $roleData['name'],'parent_id'=>$roleData['parent_id']], $roleData);
        }
    }
}
