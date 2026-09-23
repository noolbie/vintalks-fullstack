<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_result_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_result_id')->unsigned()->constrained('consultation_results')->onDelete('cascade');
            $table->string('title');
            $table->string('document_type');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_result_documents');
    }
};