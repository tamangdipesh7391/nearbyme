<?php

namespace App\Http\Controllers\provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProviderDashboardController extends Controller
{
    public function index(){
        return view('provider.index');
    }
    public function login()
    {
        return view('provider.login');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('provider.login');
    }
}
