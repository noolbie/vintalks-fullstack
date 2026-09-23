<?php

namespace Tests\Feature;

use App\Models\MentorProfile;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    public function test_the_landing_page_returns_a_successful_response(): void
    {
        $this->seedRoles();
        $mentorUser = User::factory()->create();
        $mentorUser->assignRole('mentor');
        MentorProfile::factory()->create(['user_id' => $mentorUser->id]);
        Topic::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('VinTalks');
    }
}