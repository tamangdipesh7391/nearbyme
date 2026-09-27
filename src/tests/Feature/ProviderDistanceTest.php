<?php

namespace Tests\Feature;

use App\Models\Profession;
use App\Models\ProviderTracker;
use App\Models\User;
use App\Models\UserTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderDistanceTest extends TestCase
{
    use RefreshDatabase;

    private function provider(Profession $profession, $name, $online)
    {
        $provider = User::create(['name' => $name, 'email' => uniqid().'@gmail.com', 'password' => 'x', 'role' => 'provider', 'status' => 'active', 'profession_id' => $profession->id]);
        // Both providers have coordinates saved; only the online one should get a distance.
        ProviderTracker::create(['provider_id' => $provider->id, 'current_latitude' => '27.7172', 'current_longitude' => '85.3240', 'is_active' => $online ? 1 : 0]);

        return $provider;
    }

    private function searchAs($userOnline)
    {
        $profession = Profession::create(['name' => 'Plumber', 'status' => 1]);
        $this->provider($profession, 'Online Provider', true);
        $this->provider($profession, 'Offline Provider', false);
        $user = User::create(['name' => 'U', 'email' => 'u@gmail.com', 'password' => 'x', 'role' => 'user', 'status' => 'active']);
        UserTracker::create(['user_id' => $user->id, 'current_latitude' => '27.6710', 'current_longitude' => '85.4298', 'is_active' => $userOnline ? 1 : 0]);

        $response = $this->withSession(['session_user' => $user])->get(route('home.search', ['search' => 'Plumber']))->assertOk();

        return $response->viewData('professions')->keyBy('user_name');
    }

    public function test_distance_is_only_calculated_for_online_providers()
    {
        $providers = $this->searchAs(true);

        $this->assertGreaterThan(0, $providers['Online Provider']->distance);
        $this->assertEquals(0, $providers['Offline Provider']->distance);
    }

    public function test_distance_is_zero_when_the_user_is_offline()
    {
        $providers = $this->searchAs(false);

        $this->assertEquals(0, $providers['Online Provider']->distance);
        $this->assertEquals(0, $providers['Offline Provider']->distance);
    }
}
