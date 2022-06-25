<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserDashboardController extends Controller
{
    public function index(){
        return view('user.index');
    }
    public function login()
    {
        return view('user.login');
    }

    public function logout()
    {
        Session::forget('session_user');
        return redirect()->route('user.login');
    }

}
