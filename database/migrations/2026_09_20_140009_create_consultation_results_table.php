<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unsigned()->unique()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('mentor_id')->unsigned()->constrained('users')->onDelete('cascade');
            $table->foreignId('participant_id')->unsigned()->constrained('users')->onDelete('cascade');
            $table->text('summary')->nullable();
            $table->text('mentor_notes')->nullable();
            $table->timestamps();

            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_results');
    }
};