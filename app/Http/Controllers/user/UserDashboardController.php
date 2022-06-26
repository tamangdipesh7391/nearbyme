<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\RequestedService;
use App\Models\UserTracker;
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
        $user_data = UserTracker::where('user_id',Session::get('session_user')->id)->get();
        if(count($user_data)>0){
            $user = $user_data[0]->id;
            $user_current_data = UserTracker::findOrfail($user);
            $user_current_data->is_active = 0;
            $user_current_data->last_latitude = $user_current_data->current_latitude;
            $user_current_data->last_longitude = $user_current_data->current_longitude;
            $user_current_data->current_latitude = null;
            $user_current_data->current_longitude = null;
            $user_current_data->save();

        }
        Session::forget('session_user');
        return redirect()->route('user.login');
    }
   

}
