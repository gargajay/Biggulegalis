<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Dashboard', 'page_icon' => 'fa-dashboard'];
        $data['totalUser'] = User::where('user_type', USER_TYPE['USER'])->count();

        $data['monthlyUserData'] = lastOneYearMontlyData(User::where('user_type', USER_TYPE['USER']));


        return view('web.dashboard.index', $data);
    }
}
