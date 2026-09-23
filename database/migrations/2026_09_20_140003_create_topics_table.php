<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('mentor_topic', function (Blueprint $table) {
            $table->foreignId('mentor_id')->unsigned()->constrained('mentor_profiles')->onDelete('cascade');
            $table->foreignId('topic_id')->unsigned()->constrained('topics')->onDelete('cascade');
            $table->primary(['mentor_id', 'topic_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_topic');
        Schema::dropIfExists('topics');
    }
};