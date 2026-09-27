<?php

namespace App\Http\Middleware;

use App\Models\ProviderTracker;
use App\Models\User;
use App\Models\UserTracker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EndSessionForDeletedAccount
{
    /**
     * Session key => [panel URL prefix, login route name].
     *
     * @var array
     */
    protected $sessions = [
        'session_admin' => ['admin-panel', 'admin.login'],
        'session_provider' => ['provider-panel', 'provider.login'],
        'session_user' => ['user-panel', 'user.login'],
    ];

    /**
     * End the session of any logged-in account that has since been deleted, then send the
     * visitor to that account's login page (inside its panel) or to the home page (elsewhere).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        foreach ($this->sessions as $key => [$prefix, $loginRoute]) {
            $account = Session::get($key);
            if (!$account || User::whereKey($account->id)->exists()) {
                continue;
            }

            $this->deactivateTracker($key, $account->id);
            Session::forget($key);

            $message = 'Your account is no longer available. Please contact the administrator.';
            if ($request->is($prefix, $prefix.'/*')) {
                return redirect()->route($loginRoute)->with('error', $message);
            }

            return redirect()->route('home')->with('error', $message);
        }

        return $next($request);
    }

    /**
     * Mark the account offline, as logging out does.
     */
    protected function deactivateTracker($key, $id)
    {
        if ($key === 'session_provider') {
            $tracker = ProviderTracker::where('provider_id', $id)->first();
        } elseif ($key === 'session_user') {
            $tracker = UserTracker::where('user_id', $id)->first();
        } else {
            return;
        }

        if ($tracker) {
            $tracker->is_active = 0;
            $tracker->last_latitude = $tracker->current_latitude;
            $tracker->last_longitude = $tracker->current_longitude;
            $tracker->current_latitude = null;
            $tracker->current_longitude = null;
            $tracker->save();
        }
    }
}
