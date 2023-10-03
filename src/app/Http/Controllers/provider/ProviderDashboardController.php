<?php

namespace App\Http\Controllers\provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderTracker;
use App\Models\RequestedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProviderDashboardController extends Controller
{
    public function index(){
        //notification logic
        $user_notification_msg = [];
        $user_notification_count = 0;
        $request_notifications = RequestedService::where('provider_id','=',Session::get('session_provider')->id)->where('is_seen_admin','=',0)->limit(5)->get();
        $user_notification_count = RequestedService::where('provider_id','=',Session::get('session_provider')->id)->where('is_seen_admin','=',0)->count();
        foreach($request_notifications as $key => $request_notification){
                $user_notification_msg[$key]['notification_id'] = $request_notification->id;
                $user_notification_msg[$key]['message'][$key] = $request_notification->user->name.' has sent you a request';
                $user_notification_msg[$key]['time_ago'][$key] = strtotime($request_notification->created_at);
            
        }
        //counter logic
        $request_count = RequestedService::where('provider_id','=',Session::get('session_provider')->id)->count();
        $request_count_today = RequestedService::whereDate('created_at', date('Y-m-d'))->where('provider_id','=',Session::get('session_provider')->id)->count();
        $request_count_month = RequestedService::whereMonth('created_at', date('m'))->where('provider_id','=',Session::get('session_provider')->id)->count();
        $request_count_year = RequestedService::whereYear('created_at', date('Y'))->where('provider_id','=',Session::get('session_provider')->id)->count();
        $years = RequestedService::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as year"))->groupBy('year')->orderBy('year', 'asc')->get();
        
        if($years->count() > 0){
            foreach($years as $year){
                $year_arr[] = $year->year;
            }
            $year_arr = array_unique($year_arr);
           
        }else{
            $year_arr = array();
        }
        
      
      
        $request = [];
        foreach ($year_arr as $key => $value) {
            $request[] = RequestedService::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), '=', $value)->count();
        }

    	return view('provider.index')
        ->with('year',json_encode($year_arr,JSON_NUMERIC_CHECK))
        ->with('request_count',$request_count)
        ->with('request_count_today',$request_count_today)
        ->with('request_count_month',$request_count_month)
        ->with('request_count_year',$request_count_year)
        ->with('request',json_encode($request,JSON_NUMERIC_CHECK))
        ->with('user_notification_msg',$user_notification_msg)
        ->with('user_notification_count',$user_notification_count);
    }
    public function login()
    {
        // if(Session::has('session_provider')){
        //     return redirect()->route('provider.dashboard');
        // }
        return view('provider.login');
    }

    public function logout()
    {
        $provider_data = ProviderTracker::where('provider_id',Session::get('session_provider')->id)->get();
        if(count($provider_data)>0){
            $provider = $provider_data[0]->id;
            $provider_current_data = ProviderTracker::findOrfail($provider);
            $provider_current_data->is_active = 0;
            $provider_current_data->last_latitude = $provider_current_data->current_latitude;
            $provider_current_data->last_longitude = $provider_current_data->current_longitude;
            $provider_current_data->current_latitude = null;
            $provider_current_data->current_longitude = null;
            $provider_current_data->save();
    
        }

        Session::forget('session_provider');
        return redirect()->route('provider.login');
    }
}
