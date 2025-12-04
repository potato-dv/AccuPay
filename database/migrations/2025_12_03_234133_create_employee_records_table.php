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
        Schema::create('employee_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('record_type')->default('snapshot'); // snapshot, update, termination, promotion
            $table->date('record_date');
            
            // Personal Information
            $table->string('employee_number');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->date('birthdate')->nullable();
            $table->string('sex')->nullable();
            $table->string('civil_status')->nullable();
            $table->text('address')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            
            // Employment Details
            $table->string('department');
            $table->string('position');
            $table->date('hire_date');
            $table->string('employment_type')->default('full-time');
            $table->string('status')->default('active');
            
            // Compensation
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('night_differential_rate', 8, 2)->nullable();
            $table->decimal('holiday_rate_multiplier', 8, 2)->nullable();
            
            // Government IDs
            $table->string('tax_id_number')->nullable();
            $table->string('sss_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('pagibig_number')->nullable();
            
            // Banking Information
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            
            // Leave Credits
            $table->decimal('vacation_leave_credits', 5, 2)->default(0);
            $table->decimal('sick_leave_credits', 5, 2)->default(0);
            $table->decimal('emergency_leave_credits', 5, 2)->default(0);
            
            // Work Schedule
            $table->foreignId('work_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->json('custom_rest_days')->nullable();
            
            // Metadata
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['employee_id', 'record_date']);
            $table->index('record_type');
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_records');
    }
};
