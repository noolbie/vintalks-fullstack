<?php

namespace Tests;

use App\Models\MentorProfile;
use App\Models\User;
use Spatie\Permission\Models\Role;

trait CreatesRoles
{
    protected function seedRoles(): void
    {
        foreach (['admin', 'mentor', 'participant'] as $name) {
            Role::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    protected function makeParticipant(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('participant');
        $user->participantProfile()->create([
            'phone' => '081234567890',
            'occupation' => 'Fresh Graduate',
            'institution' => 'Universitas Indonesia',
        ]);

        return $user;
    }

    protected function makeMentor(array $attributes = []): MentorProfile
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('mentor');

        return MentorProfile::factory()->create(['user_id' => $user->id]);
    }

    protected function makeAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }
}