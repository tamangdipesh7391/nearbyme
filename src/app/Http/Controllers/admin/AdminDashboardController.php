<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RequestedService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class AdminDashboardController extends Controller
{
    public function index(){
        $user_count = User::where('role', '=', 'user')->count();
        $provider_count = User::where('role', '=', 'provider')->count();
        $request_count = RequestedService::count();
        $request_count_today = RequestedService::whereDate('created_at', date('Y-m-d'))->count();
        $request_count_month = RequestedService::whereMonth('created_at', date('m'))->count();
        $request_count_year = RequestedService::whereYear('created_at', date('Y'))->count();
        $years = User::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as year"))->groupBy('year')->orderBy('year', 'asc')->get();
        if($years->count() > 0){
            foreach($years as $year){
                $year_arr[] = $year->year;
            }
            $year_arr = array_unique($year_arr);
           
        }else{
            $year_arr = array();
        }
       
        $user = [];
        $provider = [];
        $request = [];
        foreach ($year_arr as $key => $value) {
            $user[] = User::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), '=', $value)->where('role','=','user')->count();
            $provider[] = User::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), '=', $value)->where('role','=','provider')->count();
            $request[] = RequestedService::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), '=', $value)->count();
        }

    	return view('admin.index')
        ->with('year',json_encode($year_arr,JSON_NUMERIC_CHECK))
        ->with('user',json_encode($user,JSON_NUMERIC_CHECK))
        ->with('user_count',$user_count)
        ->with('provider_count',$provider_count)
        ->with('request_count',$request_count)
        ->with('request_count_today',$request_count_today)
        ->with('request_count_month',$request_count_month)
        ->with('request_count_year',$request_count_year)
        ->with('provider',json_encode($provider,JSON_NUMERIC_CHECK))
        ->with('request',json_encode($request,JSON_NUMERIC_CHECK));
    }
    public function login()
    {
        return view('admin.login');
    }

    public function logout()
    {
        Session::forget('session_admin');
        return redirect()->route('admin.login')->with('success','Logout Successfully');
    }
    public function listProviders()
    {
        $active_providers = User::where('role', 'provider')->where('status','active')->get();
        $new_providers = User::where('role', 'provider')->where('status','new')->get();
        $trashed_providers = User::onlyTrashed()->where('role', 'provider')->get();
        $suspended_providers = User::where('role', 'provider')->where('status','suspended')->get();
        return view('admin.pages.providers.index',[
            'active_providers' => $active_providers,
            'new_providers' => $new_providers,
            'trashed_providers' => $trashed_providers,
            'suspended_providers' => $suspended_providers
        ]);
    }
    public function softDeleteProvider($id)
    {
        $provider = User::find($id);
        $provider->delete();
        return redirect()->route('admin.providers')->with('success','Provider deleted successfully');
    }
    public function restoreProvider($id)
    {
        $provider = User::withTrashed()->find($id);
        $provider->restore();
        return redirect()->route('admin.providers')->with('success','Provider restored successfully');
    }
    public function deleteProvider($id)
    {
        $provider = User::withTrashed()->find($id);
        $provider->forceDelete();
        return redirect()->route('admin.providers')->with('success','Provider deleted successfully');
    }
    public function manageProvider(Request $request,$id)
    {
       
        $provider = User::findOrfail($id);
        if($request->has('status')){
            $provider->status = $request->status;
            $provider->save();
            return redirect()->route('admin.providers')->with('success','Provider status updated successfully');
        }
        return redirect()->route('admin.providers')->with('error','Something went wrong');
    }
    public function listUsers()
    {
        $active_users = User::where('role', 'user')->where('status','active')->get();
        $new_users = User::where('role', 'user')->where('status','new')->get();
        $trashed_users = User::onlyTrashed()->where('role', 'user')->get();
        $suspended_users = User::where('role', 'user')->where('status','suspended')->get();
        return view('admin.pages.users.index',[
            'active_users' => $active_users,
            'new_users' => $new_users,
            'trashed_users' => $trashed_users,
            'suspended_users' => $suspended_users
        ]);
    }
    public function softDeleteUser($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
    public function restoreUser($id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        return redirect()->route('admin.users')->with('success', 'User restored successfully');
    }
    public function deleteUser($id)
    {
        $user = User::withTrashed()->find($id);
        $user->forceDelete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
    public function manageUser(Request $request,$id)
    {
        $user = User::findOrfail($id);
        if($request->has('status')){
            $user->status = $request->status;
            $user->save();
            return redirect()->route('admin.users')->with('success','User status updated successfully');
        }
        return redirect()->route('admin.users')->with('error','Something went wrong');
    }

}
