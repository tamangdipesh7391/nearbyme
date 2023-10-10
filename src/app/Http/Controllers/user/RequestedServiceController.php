<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\RequestedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class RequestedServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function requestHistory($id,$hid = null){

        // hilight row logic 
        $hilight_id = $hid;
        if($hid != null){
            $requested_service = RequestedService::find($hid);
            $requested_service->is_seen = 1;
            $requested_service->save();
            $hilight_id = $hid;
        }
        //notification logic
        $user_notification_msg = [];
        $user_notification_count = 0;
        $request_notifications = RequestedService::where('user_id','=',Session::get('session_user')->id)->where('is_seen','=',0)->limit(5)->get();
        $user_notification_count = RequestedService::where('user_id','=',Session::get('session_user')->id)->where('is_seen','=',0)->count();
        foreach($request_notifications as $key => $request_notification){
           if($request_notification->status == 'confirmed'){
                $user_notification_msg[$key]['status'] = 'confirmed';
                $user_notification_msg[$key]['notification_id'] = $request_notification->id;
                $user_notification_msg[$key]['message'][$key] = 'Your request has been confirmed by '.$request_notification->provider->name;
                $user_notification_msg[$key]['time_ago'][$key] = strtotime($request_notification->created_at);
            }elseif($request_notification->status == 'rejected'){
                $user_notification_msg[$key]['status'] = 'rejected';
                $user_notification_msg[$key]['notification_id'] = $request_notification->id;
                $user_notification_msg[$key]['message'][$key] = 'Your request has been rejected by '.$request_notification->provider->name;
                $user_notification_msg[$key]['time_ago'][$key] = strtotime($request_notification->created_at);
            }
        }
        //dashboard data
        $requested_services = RequestedService::where('user_id', $id)->where('is_canceled',0)->get();
        $pending_services = RequestedService::where('user_id', $id)->where('status', 'pending')->get();
        $confirmed_services = RequestedService::where('user_id', $id)->where('status', 'confirmed')->get();
        $completed_services = RequestedService::where('user_id', $id)->where('status', 'completed')->get();
        $rejected_services = RequestedService::where('user_id', $id)->where('status', 'rejected')->get();
        $canceled_services = RequestedService::where('user_id', $id)->where('is_canceled', 1)->get();
        $trashed_services = RequestedService::where('user_id', $id)->onlyTrashed()->get();
        return view('user.pages.user.requestHistory', compact('requested_services', 'pending_services', 'confirmed_services', 'completed_services', 'rejected_services', 'trashed_services', 'canceled_services', 'user_notification_msg', 'user_notification_count', 'hilight_id'));
    }
    public function softDeleteRequest($id){
        $requested_services = RequestedService::where('id', $id)->first();
        $requested_services->delete();
        return redirect()->back()->with('success', 'Request deleted successfully');
    }
    public function restoreRequest($id){
        $requested_services = RequestedService::withTrashed()->find($id);
        $requested_services->restore();
        return redirect()->back()->with('success', 'Request restored successfully');
    }
    public function deleteRequest($id){
        $requested_services = RequestedService::withTrashed()->find($id);
        $requested_services->forceDelete();
        return redirect()->back()->with('success', 'Request deleted successfully');
    }
    public function manageRequest($id){
        $requested_services = RequestedService::findOrfail($id);
        if($requested_services->is_canceled == 1){
            $requested_services->is_canceled = 0;
        }else{
            $requested_services->is_canceled = 1;
        }
            $requested_services->save();
            return redirect()->back()->with('success', 'Request accepted successfully');
    }

    public function sendFeedback(Request $request,$id){
        $user = RequestedService::findOrfail($id);
        if($request->has('feedback')){
            $user->feedback = $request->feedback;
            $user->save();
            return redirect()->back()->with('success', 'Feedback send successfully');
        }
        return redirect()->route('provider.requestList',$user->provider_id)->with('error','Something went wrong');
    }

    public function providerRating(Request $request,$id){
        $requested_services = RequestedService::findOrfail($id);
        $requested_services->rating = request()->rating;
        $requested_services->save();
        return redirect()->back()->with('success', 'Rating submitted successfully');
    }

}
