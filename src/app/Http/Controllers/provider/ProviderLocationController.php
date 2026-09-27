<?php

namespace App\Http\Controllers\provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProviderLocationController extends Controller
{
    /**
     * Store the provider's live GPS position, sent periodically by the provider panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        ProviderTracker::updateOrCreate(
            ['provider_id' => Session::get('session_provider')->id],
            [
                'current_latitude' => $request->latitude,
                'current_longitude' => $request->longitude,
                'is_active' => 1,
            ]
        );

        return response()->json(['saved' => true]);
    }
}
