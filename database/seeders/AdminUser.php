<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'strengthenapp@gmail.com',
                'password' => bcrypt('r^Fx33sbJ8SoQ!'),
                'user_type' => 'admin',
                'country_code' => '+1',
                'mobile' => '9988776655'
            ],
            [
                'first_name' => 'Official',
                'last_name' => 'Admin',
                'email' => 'office3.cepoch@gmail.com',
                'password' => bcrypt('Ql9@!cN38sm2'),
                'user_type' => 'admin',
                'country_code' => '+1',
                'mobile' => '9988776655'
            ],
            [
                'first_name' => 'Developer',
                'last_name' => 'Admin',
                'email' => 'venu@cepoch.com',
                'password' => bcrypt('o5^usmPfg1R5'),
                'user_type' => 'admin',
                'country_code' => '+1',
                'mobile' => '9988776655'
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@yopmail.com',
                'password' => bcrypt('Admin@123'),
                'user_type' => 'admin',
                'country_code' => '+91',
                'mobile' => '9898989898'
            ]
        ];

        foreach ($account as $value) {

            $udata = [
                'email' =>  $value['email'],
                'password' =>  $value['password'],
                'user_type' =>  $value['user_type'],
            ];

            if(isset($value['first_name']) && !empty($value['first_name'])){
                $udata['first_name'] = $value['first_name'];
            }
            if(isset($value['last_name']) && !empty($value['last_name'])){
                $udata['first_name'] = $value['first_name'];
            }
            if(!empty($value['first_name']) && !empty($value['last_name'])){
                $udata['full_name'] = $value['first_name'] . '' .$value['last_name'];
            }


            $count = User::where('email', $value['email'])->where('user_type', 'admin')->count();
            if ($count == 0) {
                User::create($udata);
            } elseif ($count == 1) {
                $update = User::where('email', $value['email'])->where('user_type', 'admin')->first();
                $update->password =  $value['password'];
                $update->save();
            } elseif ($count > 1) {
                $delete = User::where('email', $value['email'])->where('user_type', 'admin')->forceDelete();
                //$delete->delete();
                User::create($udata);
            }
        }
    }
}
