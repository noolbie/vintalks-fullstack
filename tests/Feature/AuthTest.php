<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    public function test_guest_can_register_as_participant(): void
    {
        $this->seedRoles();

        $response = $this->post(route('register.attempt'), [
            'name' => 'Rina Oktaviani',
            'email' => 'rina@example.com',
            'phone' => '089911223344',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('participant.dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'rina@example.com']);
        $this->assertTrue(User::where('email', 'rina@example.com')->first()->hasRole('participant'));
        $this->assertDatabaseHas('participant_profiles', ['phone' => '089911223344']);
    }

    public function test_participant_can_login_and_lands_on_their_dashboard(): void
    {
        $this->seedRoles();
        $user = $this->makeParticipant(['password' => 'secret123']);

        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('participant.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_credentials_is_rejected(): void
    {
        $this->seedRoles();
        $this->makeParticipant(['password' => 'secret123']);

        $response = $this->post(route('login.attempt'), [
            'email' => 'someone@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}