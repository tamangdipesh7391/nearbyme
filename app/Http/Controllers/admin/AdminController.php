<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            if(Auth::user()->role == 'admin'){
                $request->session()->put('session_admin', Auth::user());
                return redirect()->route('admin.dashboard');
            }else{
                return redirect()->route('admin.login')->with('error', 'You are not an admin');
            }
       
        }else{
            return redirect()->route('admin.login')->with('error','Invalid Credentials');
        }
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
            'password' => 'required|min:6|max:16|confirmed'
        ]);
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['role'] = $request->role;
        $data['status'] = 'active';
        User::create($data);
        return redirect()->route('admin.login')->with('success','User created successfully');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = User::findOrfail($id);
        return view('admin.pages.admin.edit',compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrfail($id);
        $request->has('avatar') ? $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]) : '';
        if($request->has('avatar')){
            $file = $request->file('avatar');
            $fileName = md5(microtime()).'_'.$file->getClientOriginalName();
            $file->move(public_path('admin_avatar'),$fileName);
            if($admin->avatar != null){
                $old_avatar = $admin->avatar;
                if(file_exists(public_path('admin_avatar/'.$old_avatar))){
                    unlink(public_path('admin_avatar/'.$old_avatar));
                }
             }
            $admin->avatar = $fileName;
            $admin->save();
        }else{
            $admin['name'] = $request->name;
            $admin['email'] = $request->email;
            $admin['phone'] = $request->phone;
            $admin['address'] = $request->address;
            $admin->save();
        }
        return redirect()->route('admins.edit',$admin->id)->with('success','Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
