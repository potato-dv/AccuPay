<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('work_schedule_id')->nullable()->after('emergency_phone')
                ->constrained('work_schedules')->onDelete('set null');
            
            // Rest days (for custom schedules not using work_schedule template)
            $table->json('custom_rest_days')->nullable()->comment('Array of custom rest days if not using template');
            
            // Shift differential pay (if applicable)
            $table->decimal('night_differential_rate', 8, 2)->nullable()->default(0);
            $table->decimal('holiday_rate_multiplier', 3, 2)->default(2.00); // 200% for holidays
            
            // Tax information
            $table->string('tax_id_number')->nullable()->unique();
            $table->string('sss_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('pagibig_number')->nullable();
            
            // Leave entitlements (annual allocation)
            $table->integer('vacation_leave_credits')->default(15);
            $table->integer('sick_leave_credits')->default(15);
            $table->integer('emergency_leave_credits')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['work_schedule_id']);
            $table->dropColumn([
                'work_schedule_id',
                'custom_rest_days',
                'night_differential_rate',
                'holiday_rate_multiplier',
                'tax_id_number',
                'sss_number',
                'philhealth_number',
                'pagibig_number',
                'vacation_leave_credits',
                'sick_leave_credits',
                'emergency_leave_credits',
            ]);
        });
    }
};
