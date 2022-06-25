<?php

namespace App\Http\Controllers\provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProviderDashboardController extends Controller
{
    public function index(){
        return view('provider.index');
    }
    public function login()
    {
        return view('provider.login');
    }

    public function logout(Request $request)
    {
        $provider_data = ProviderTracker::where('provider_id',auth()->user()->id)->get();
        $provider = $provider_data[0]->id;
        $provider_current_data = ProviderTracker::findOrfail($provider);
        $provider_current_data->is_active = 0;
        $provider_current_data->last_latitude = $provider_current_data->current_latitude;
        $provider_current_data->last_longitude = $provider_current_data->current_longitude;
        $provider_current_data->current_latitude = null;
        $provider_current_data->current_longitude = null;
        $provider_current_data->save();


        Session::forget('session_provider');
        return redirect()->route('provider.login');
    }
}
