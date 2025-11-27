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
        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('late_deduction', 10, 2)->default(0)->after('loan_deductions');
            $table->decimal('sss_rate', 5, 2)->default(4.5)->after('sss');
            $table->decimal('philhealth_rate', 5, 2)->default(2.0)->after('philhealth');
            $table->decimal('pagibig_rate', 5, 2)->default(2.0)->after('pagibig');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn(['late_deduction', 'sss_rate', 'philhealth_rate', 'pagibig_rate', 'tax_rate']);
        });
    }
};
