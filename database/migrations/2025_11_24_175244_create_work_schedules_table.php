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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_name')->unique(); // e.g., "Mon-Sat", "Mon-Fri", "Custom Shift A"
            $table->string('description')->nullable();
            
            // Work days (boolean flags for each day)
            $table->boolean('monday')->default(false);
            $table->boolean('tuesday')->default(false);
            $table->boolean('wednesday')->default(false);
            $table->boolean('thursday')->default(false);
            $table->boolean('friday')->default(false);
            $table->boolean('saturday')->default(false);
            $table->boolean('sunday')->default(false);
            
            // Work hours
            $table->time('shift_start')->default('08:00:00');
            $table->time('shift_end')->default('17:00:00');
            $table->decimal('daily_hours', 4, 2)->default(8.00); // Expected hours per day
            $table->decimal('weekly_hours', 5, 2)->nullable(); // Total weekly hours
            
            // Break time
            $table->time('break_start')->nullable()->default('12:00:00');
            $table->time('break_end')->nullable()->default('13:00:00');
            $table->boolean('break_paid')->default(false);
            
            // Overtime settings
            $table->boolean('overtime_allowed')->default(true);
            $table->decimal('overtime_rate_multiplier', 3, 2)->default(1.25); // 125% for OT
            
            // Grace period for late arrivals (in minutes)
            $table->integer('grace_period_minutes')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
