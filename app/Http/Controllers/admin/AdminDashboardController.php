<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
