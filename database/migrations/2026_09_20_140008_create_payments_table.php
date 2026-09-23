<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unsigned()->unique()->constrained('bookings')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('transfer');
            $table->string('payment_reference')->nullable();
            $table->foreignId('proof_document_id')->unsigned()->nullable()->constrained('documents')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->unsigned()->nullable()->constrained('users')->onDelete('set null');
            $table->string('rejection_reason')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->index(['booking_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};