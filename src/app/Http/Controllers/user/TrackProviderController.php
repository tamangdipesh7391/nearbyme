<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\ProviderTracker;
use App\Models\RequestedService;
use App\Models\UserTracker;
use Illuminate\Support\Facades\Session;

class TrackProviderController extends Controller
{
    /**
     * Location updates older than this are shown as stale.
     */
    const STALE_AFTER_SECONDS = 120;

    /**
     * Map page showing where the provider of a confirmed booking is.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $booking = $this->findOwnBooking($id);
        if ($booking->status != 'confirmed') {
            return redirect()->route('user.request.history', Session::get('session_user')->id)
                ->with('error', 'You can only track a provider while the booking is confirmed.');
        }

        return view('user.pages.user.trackProvider', array_merge($this->headerNotifications(), [
            'booking' => $booking,
            'destination' => $this->destination($booking),
        ]));
    }

    /**
     * Latest provider position for the map page to poll.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function location($id)
    {
        $booking = $this->findOwnBooking($id);
        if ($booking->status != 'confirmed') {
            return response()->json(['booking_status' => $booking->status]);
        }

        $tracker = ProviderTracker::where('provider_id', $booking->provider_id)->first();
        $hasLocation = $tracker && $tracker->is_active == 1 && $tracker->current_latitude != null && $tracker->current_longitude != null;
        $secondsAgo = $hasLocation ? now()->diffInSeconds($tracker->updated_at) : null;

        return response()->json([
            'booking_status' => $booking->status,
            'provider' => $hasLocation ? [
                'lat' => (float) $tracker->current_latitude,
                'lng' => (float) $tracker->current_longitude,
                'seconds_ago' => $secondsAgo,
                'stale' => $secondsAgo > self::STALE_AFTER_SECONDS,
            ] : null,
        ]);
    }

    /**
     * Notification data the user panel header expects (same as the other user panel pages).
     */
    protected function headerNotifications()
    {
        $user_id = Session::get('session_user')->id;
        $user_notification_msg = [];
        $request_notifications = RequestedService::where('user_id', $user_id)->where('is_seen', 0)->limit(5)->get();
        foreach ($request_notifications as $key => $request_notification) {
            if (in_array($request_notification->status, ['confirmed', 'rejected'])) {
                $user_notification_msg[$key]['status'] = $request_notification->status;
                $user_notification_msg[$key]['notification_id'] = $request_notification->id;
                $user_notification_msg[$key]['message'][$key] = 'Your request has been '.$request_notification->status.' by '.$request_notification->provider->name;
                $user_notification_msg[$key]['time_ago'][$key] = strtotime($request_notification->created_at);
            }
        }

        return [
            'user_notification_msg' => $user_notification_msg,
            'user_notification_count' => RequestedService::where('user_id', $user_id)->where('is_seen', 0)->count(),
        ];
    }

    /**
     * Only the user who made the booking may track it.
     */
    protected function findOwnBooking($id)
    {
        return RequestedService::where('user_id', Session::get('session_user')->id)->findOrFail($id);
    }

    /**
     * Where the provider is heading: the location saved with the booking, else the user's last known location.
     */
    protected function destination(RequestedService $booking)
    {
        if ($booking->user_latitude != null && $booking->user_longitude != null) {
            return ['lat' => (float) $booking->user_latitude, 'lng' => (float) $booking->user_longitude];
        }

        $tracker = UserTracker::where('user_id', $booking->user_id)->first();
        if ($tracker) {
            $lat = $tracker->current_latitude ?? $tracker->last_latitude;
            $lng = $tracker->current_longitude ?? $tracker->last_longitude;
            if ($lat != null && $lng != null) {
                return ['lat' => (float) $lat, 'lng' => (float) $lng];
            }
        }

        return null;
    }
}
