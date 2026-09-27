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
    public function softDeleteProvider(Request $request, $id)
    {
        $provider = User::findOrFail($id);
        if ($error = $this->activeBookingsError($provider, 'provider')) {
            return $this->backToTab('admin.providers', $request)->with('error', $error);
        }
        $provider->delete();
        return $this->backToTab('admin.providers', $request)->with('success','Provider deleted successfully');
    }
    public function restoreProvider(Request $request, $id)
    {
        $provider = User::withTrashed()->find($id);
        $provider->restore();
        return $this->backToTab('admin.providers', $request)->with('success','Provider restored successfully');
    }
    public function deleteProvider(Request $request, $id)
    {
        $provider = User::withTrashed()->findOrFail($id);
        if ($error = $this->activeBookingsError($provider, 'provider')) {
            return $this->backToTab('admin.providers', $request)->with('error', $error);
        }
        $provider->forceDelete();
        return $this->backToTab('admin.providers', $request)->with('success','Provider deleted successfully');
    }
    public function manageProvider(Request $request,$id)
    {
       
        $provider = User::findOrfail($id);
        if($request->has('status')){
            $provider->status = $request->status;
            $provider->save();
            return $this->backToTab('admin.providers', $request)->with('success','Provider status updated successfully');
        }
        return $this->backToTab('admin.providers', $request)->with('error','Something went wrong');
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
    public function softDeleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($error = $this->activeBookingsError($user, 'user')) {
            return $this->backToTab('admin.users', $request)->with('error', $error);
        }
        $user->delete();
        return $this->backToTab('admin.users', $request)->with('success', 'User deleted successfully');
    }
    public function restoreUser(Request $request, $id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        return $this->backToTab('admin.users', $request)->with('success', 'User restored successfully');
    }
    public function deleteUser(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($error = $this->activeBookingsError($user, 'user')) {
            return $this->backToTab('admin.users', $request)->with('error', $error);
        }
        $user->forceDelete();
        return $this->backToTab('admin.users', $request)->with('success', 'User deleted successfully');
    }
    public function manageUser(Request $request,$id)
    {
        $user = User::findOrfail($id);
        if($request->has('status')){
            $user->status = $request->status;
            $user->save();
            return $this->backToTab('admin.users', $request)->with('success','User status updated successfully');
        }
        return $this->backToTab('admin.users', $request)->with('error','Something went wrong');
    }

    /**
     * Redirect to a list page, reopening the tab (new/active/suspended/trashed) the action came from.
     */
    private function backToTab($route, Request $request)
    {
        return redirect()->route($route)->with('tab', $request->query('tab'));
    }

    /**
     * Build a warning if the account still has pending or in-progress bookings, otherwise null.
     */
    private function activeBookingsError(User $account, $label)
    {
        $counts = $account->unresolvedBookingCounts();
        if ($counts['pending'] + $counts['confirmed'] === 0) {
            return null;
        }

        $parts = [];
        if ($counts['pending'] > 0) {
            $parts[] = $counts['pending'].' pending';
        }
        if ($counts['confirmed'] > 0) {
            $parts[] = $counts['confirmed'].' in-progress';
        }

        return 'Cannot delete this '.$label.': they have '.implode(' and ', $parts).' '
            .\Illuminate\Support\Str::plural('booking', $counts['pending'] + $counts['confirmed'])
            .'. Resolve them before deleting.';
    }
}
