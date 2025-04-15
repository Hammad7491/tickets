<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{


    public function dashboard()
    {
        return view('admin.pages.dashboard');  
    }
    public function userDashboard()
    {
        return view('user.pages.dashboard');  
    }



}
