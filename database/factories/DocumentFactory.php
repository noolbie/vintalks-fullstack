<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'booking_id' => null,
            'document_type' => fake()->randomElement(['cv', 'supporting_document', 'other']),
            'original_name' => fake()->word().'.pdf',
            'file_name' => fake()->uuid().'.pdf',
            'file_path' => 'documents/'.fake()->randomElement(['cv', 'supporting_document', 'other']).'/'.fake()->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(10000, 1000000),
        ];
    }
}