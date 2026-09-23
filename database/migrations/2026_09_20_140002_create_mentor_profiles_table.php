<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned()->unique()->constrained('users')->onDelete('cascade');
            $table->string('display_name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->text('bio');
            $table->text('experience')->nullable();
            $table->string('expertise');
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_profiles');
    }
};