<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('participant_id')->unsigned()->constrained('users')->onDelete('restrict');
            $table->foreignId('mentor_id')->unsigned()->constrained('users')->onDelete('restrict');
            $table->foreignId('mentor_availability_id')->unsigned()->nullable()->constrained('mentor_availabilities')->onDelete('set null');
            $table->foreignId('topic_id')->unsigned()->nullable()->constrained('topics')->onDelete('set null');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 12, 2);
            $table->string('booking_status');
            $table->string('payment_status');
            $table->enum('meeting_provider', ['google_meet', 'zoom', 'other'])->nullable();
            $table->string('meeting_url')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['participant_id']);
            $table->index(['mentor_id']);
            $table->index(['session_date']);
            $table->index(['booking_status']);
            $table->index(['payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};