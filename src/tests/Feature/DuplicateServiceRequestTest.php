<?php

namespace Tests\Feature;

use App\Models\RequestedService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create(['name' => 'U', 'email' => 'u@gmail.com', 'password' => 'x', 'role' => 'user', 'status' => 'active']);
        $this->provider = User::create(['name' => 'Gladyce Kautzer', 'email' => 'p@gmail.com', 'password' => 'x', 'role' => 'provider', 'status' => 'active']);
    }

    private function requestProvider()
    {
        return $this->withSession(['session_user' => $this->user])
            ->from(route('home.search'))
            ->post(route('request.service'), ['provider_id' => $this->provider->id]);
    }

    public function test_first_request_is_created()
    {
        $this->requestProvider()->assertRedirect('user-panel/request-history/'.$this->user->id);

        $this->assertEquals(1, RequestedService::count());
    }

    public function test_second_request_while_pending_is_blocked()
    {
        $this->requestProvider();

        $this->requestProvider()
            ->assertRedirect(route('home.search'))
            ->assertSessionHas('error', 'You already have a pending request with Gladyce Kautzer. You can request them again once it is completed, rejected or cancelled.');

        $this->assertEquals(1, RequestedService::count());
    }

    public function test_request_while_in_progress_is_blocked()
    {
        RequestedService::create(['user_id' => $this->user->id, 'provider_id' => $this->provider->id, 'status' => 'confirmed']);

        $this->requestProvider()->assertSessionHas('error', 'You already have an in-progress booking with Gladyce Kautzer. You can request them again once it is completed, rejected or cancelled.');

        $this->assertEquals(1, RequestedService::count());
    }

    public function test_can_request_again_after_previous_booking_is_resolved()
    {
        RequestedService::create(['user_id' => $this->user->id, 'provider_id' => $this->provider->id, 'status' => 'completed']);

        $this->requestProvider()->assertSessionHas('success');

        $this->assertEquals(2, RequestedService::count());
    }

    public function test_booking_uses_logged_in_user_not_form_input()
    {
        $other = User::create(['name' => 'Other', 'email' => 'o@gmail.com', 'password' => 'x', 'role' => 'user', 'status' => 'active']);

        $this->withSession(['session_user' => $this->user])
            ->post(route('request.service'), ['provider_id' => $this->provider->id, 'user_id' => $other->id]);

        $this->assertEquals($this->user->id, RequestedService::first()->user_id);
    }

    public function test_guest_cannot_request()
    {
        $this->post(route('request.service'), ['provider_id' => $this->provider->id])->assertRedirect(route('user.login'));

        $this->assertEquals(0, RequestedService::count());
    }
}
