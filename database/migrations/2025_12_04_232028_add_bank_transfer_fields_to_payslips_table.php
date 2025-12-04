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
            $table->string('transfer_reference_number')->nullable()->after('net_pay');
            $table->string('transfer_bank_name')->nullable()->after('transfer_reference_number');
            $table->string('transfer_account_number')->nullable()->after('transfer_bank_name');
            $table->timestamp('transfer_date')->nullable()->after('transfer_account_number');
            $table->boolean('is_salary_sent')->default(false)->after('transfer_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn([
                'transfer_reference_number',
                'transfer_bank_name',
                'transfer_account_number',
                'transfer_date',
                'is_salary_sent'
            ]);
        });
    }
};
