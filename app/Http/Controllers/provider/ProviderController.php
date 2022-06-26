<?php

namespace App\Http\Controllers\provider;

use App\Http\Controllers\Controller;
use App\Models\Profession;
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
                $request->session()->put('session_provider', Auth::user());
                ProviderTracker::updateOrCreate(
                    ['provider_id' => Auth::user()->id],
                    [
                        'provider_id' => Auth::user()->id, 
                        'current_latitude' => $request->current_latitude, 
                        'current_longitude' => $request->current_longitude, 
                        'is_active' => 1
                        ]
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
        $professions = Profession::where('status', 1)->get();
        return view('provider.register',['professions' => $professions]);
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
            'password' => 'required|min:6|max:16|confirmed',
            'profession_id' => 'required',
        ]);
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['role'] = $request->role;
        $data['status'] = 'new';
        $data['profession_id'] = $request->profession_id;
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
        $provider = User::findOrfail($id);
        $professions = Profession::where('status', 1)->get();
        return view('provider.pages.provider.edit',['provider' => $provider, 'professions' => $professions]);


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
        
        $provider = User::findOrfail($id);
        $request->has('avatar') ? $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]) : '';
        if($request->has('avatar')){
            $file = $request->file('avatar');
            $fileName = md5(microtime()).'_'.$file->getClientOriginalName();
            $file->move(public_path('provider_avatar'),$fileName);
            if($provider->avatar != null){
                $old_avatar = $provider->avatar;
                if(file_exists(public_path('provider_avatar/'.$old_avatar))){
                    unlink(public_path('provider_avatar/'.$old_avatar));
                }
             }
            $provider->avatar = $fileName;
            $provider->save();
        }else{
            if($request->has('citizenship')){
                $file = $request->file('citizenship');
                $fileName = md5(microtime()).'_'.$file->getClientOriginalName();
                $file->move(public_path('provider_citizenship'),$fileName);
                if($provider->citizenship != null){
                    $old_citizenship = $provider->citizenship;
                    if(file_exists(public_path('provider_citizenship/'.$old_citizenship))){
                        unlink(public_path('provider_citizenship/'.$old_citizenship));
                    }
                 }
                $provider->citizenship = $fileName;
            }
            $provider['name'] = $request->name;
            $provider['email'] = $request->email;
            $provider['phone'] = $request->phone;
            $provider['profession_id'] = $request->profession_id;
            $provider['address'] = $request->address;

            $provider->save();
        }
        return redirect()->route('providers.edit',$provider->id)->with('success','User updated successfully');
        
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

