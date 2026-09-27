<?php

namespace Tests\Feature;

use App\Models\ProviderTracker;
use App\Models\RequestedService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackProviderTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->account('user');
        $this->provider = $this->account('provider');
    }

    private function account($role)
    {
        return User::create(['name' => ucfirst($role), 'email' => $role.uniqid().'@gmail.com', 'password' => 'x', 'role' => $role, 'status' => 'active']);
    }

    private function booking($status, $user = null)
    {
        return RequestedService::create([
            'user_id' => ($user ?? $this->user)->id,
            'provider_id' => $this->provider->id,
            'status' => $status,
            'user_latitude' => '27.6710',
            'user_longitude' => '85.4298',
        ]);
    }

    public function test_user_can_open_tracking_for_confirmed_booking()
    {
        $booking = $this->booking('confirmed');

        $this->withSession(['session_user' => $this->user])
            ->get(route('user.request.track', $booking->id))
            ->assertOk()
            ->assertViewHas('destination', ['lat' => 27.6710, 'lng' => 85.4298]);
    }

    public function test_tracking_is_refused_for_pending_booking()
    {
        $booking = $this->booking('pending');

        $this->withSession(['session_user' => $this->user])
            ->get(route('user.request.track', $booking->id))
            ->assertRedirect(route('user.request.history', $this->user->id))
            ->assertSessionHas('error');
    }

    public function test_user_cannot_track_someone_elses_booking()
    {
        $booking = $this->booking('confirmed', $this->account('user'));

        $this->withSession(['session_user' => $this->user])->get(route('user.request.track', $booking->id))->assertNotFound();
        $this->withSession(['session_user' => $this->user])->get(route('user.request.track.location', $booking->id))->assertNotFound();
    }

    public function test_guest_cannot_track()
    {
        $booking = $this->booking('confirmed');

        $this->get(route('user.request.track.location', $booking->id))->assertRedirect(route('user.login'));
    }

    public function test_location_feed_returns_live_provider_position()
    {
        $booking = $this->booking('confirmed');
        ProviderTracker::create(['provider_id' => $this->provider->id, 'current_latitude' => '27.7172', 'current_longitude' => '85.3240', 'is_active' => 1]);

        $this->withSession(['session_user' => $this->user])
            ->getJson(route('user.request.track.location', $booking->id))
            ->assertOk()
            ->assertJson(['booking_status' => 'confirmed', 'provider' => ['lat' => 27.7172, 'lng' => 85.3240, 'stale' => false]]);
    }

    public function test_location_feed_hides_offline_provider()
    {
        $booking = $this->booking('confirmed');
        ProviderTracker::create(['provider_id' => $this->provider->id, 'current_latitude' => '27.7172', 'current_longitude' => '85.3240', 'is_active' => 0]);

        $this->withSession(['session_user' => $this->user])
            ->getJson(route('user.request.track.location', $booking->id))
            ->assertJson(['booking_status' => 'confirmed', 'provider' => null]);
    }

    public function test_location_feed_stops_once_booking_is_completed()
    {
        $booking = $this->booking('completed');

        $this->withSession(['session_user' => $this->user])
            ->getJson(route('user.request.track.location', $booking->id))
            ->assertExactJson(['booking_status' => 'completed']);
    }

    public function test_provider_panel_saves_live_location()
    {
        $this->withSession(['session_provider' => $this->provider])
            ->postJson(route('provider.location.update'), ['latitude' => 27.70, 'longitude' => 85.31])
            ->assertOk();

        $tracker = ProviderTracker::where('provider_id', $this->provider->id)->first();
        $this->assertEquals(27.70, (float) $tracker->current_latitude);
        $this->assertEquals(1, $tracker->is_active);
    }

    public function test_provider_location_is_validated()
    {
        $this->withSession(['session_provider' => $this->provider])
            ->postJson(route('provider.location.update'), ['latitude' => 200, 'longitude' => 'abc'])
            ->assertStatus(422);
    }
}
