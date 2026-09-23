<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unsigned()->unique()->constrained('bookings')->onDelete('cascade');
            $table->string('linkedin_url')->nullable();
            $table->text('career_goal')->nullable();
            $table->string('consultation_topic')->nullable();
            $table->text('description')->nullable();
            $table->text('additional_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_requirements');
    }
};