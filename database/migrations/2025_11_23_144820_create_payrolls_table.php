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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_period'); // e.g., 'November 2025'
            $table->date('period_start');
            $table->date('period_end');
            $table->date('payment_date');
            $table->decimal('total_amount', 12, 2);
            $table->integer('total_employees');
            $table->string('status')->default('draft'); // draft, processing, paid
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
