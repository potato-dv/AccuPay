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
            $table->integer('days_present')->default(0)->after('overtime_hours');
            $table->integer('days_absent')->default(0)->after('days_present');
            $table->integer('days_late')->default(0)->after('days_absent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn(['days_present', 'days_absent', 'days_late']);
        });
    }
};
