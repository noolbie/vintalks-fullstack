<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('topic_id')->constrained('packages')->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->nullable()->after('price');
            $table->string('package_approval_status')->default('none')->after('discount_amount');
            $table->timestamp('package_approved_at')->nullable()->after('package_approval_status');
            $table->timestamp('package_rejected_at')->nullable()->after('package_approved_at');
            $table->string('package_rejection_reason')->nullable()->after('package_rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('package_id');
            $table->dropColumn([
                'discount_amount',
                'package_approval_status',
                'package_approved_at',
                'package_rejected_at',
                'package_rejection_reason',
            ]);
        });
    }
};