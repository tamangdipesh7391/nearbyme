<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\RequestedService;
use App\Models\UserTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserDashboardController extends Controller
{
    public function index(){
        $request_count = RequestedService::where('user_id','=',Session::get('session_user')->id)->count();
        $request_count_today = RequestedService::whereDate('created_at', date('Y-m-d'))->where('user_id','=',Session::get('session_user')->id)->count();
        $request_count_month = RequestedService::whereMonth('created_at', date('m'))->where('user_id','=',Session::get('session_user')->id)->count();
        $request_count_year = RequestedService::whereYear('created_at', date('Y'))->where('user_id','=',Session::get('session_user')->id)->count();
        $years = RequestedService::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as year"))->groupBy('year')->orderBy('year', 'asc')->get();
        
        foreach($years as $year){
            $year_arr[] = $year->year;
        }
        $year_arr = array_unique($year_arr);
       
      
        $request = [];
        foreach ($year_arr as $key => $value) {
            $request[] = RequestedService::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), '=', $value)->count();
        }

    	return view('user.index')
        ->with('year',json_encode($year_arr,JSON_NUMERIC_CHECK))
        ->with('request_count',$request_count)
        ->with('request_count_today',$request_count_today)
        ->with('request_count_month',$request_count_month)
        ->with('request_count_year',$request_count_year)
        ->with('request',json_encode($request,JSON_NUMERIC_CHECK));
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
