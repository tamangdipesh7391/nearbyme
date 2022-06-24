<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(){
        return view('admin.index');
    }
    public function login()
    {
        return view('admin.login');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('admin.login');
    }
}
