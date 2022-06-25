<?php

namespace App\Http\Controllers\provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderTracker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stevebauman\Location\Facades\Location;


class ProviderController extends Controller
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
            if(Auth::user()->status == 'new'){
                return redirect()->route('provider.login')->with('error','Your account is not active yet. Please contact admin.');
                }
            if(Auth::user()->role == 'provider'){
                ProviderTracker::updateOrCreate(
                    ['provider_id' => Auth::user()->id],
                    ['provider_id' => Auth::user()->id, 'current_latitude' => $request->current_latitude, 'current_longitude' => $request->current_longitude, 'is_active' => 1]
                );
                return redirect()->route('provider.dashboard');
            }else{
                return redirect()->route('provider.login')->with('error', 'You are not a provider');
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
        return view('provider.register');
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
        $data['status'] = 'new';
        User::create($data);
        return redirect()->route('provider.login')->with('success','User created successfully');


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
        //
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
        //
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

