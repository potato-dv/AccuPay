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
            $table->string('middle_name')->nullable()->after('first_name');
            $table->date('birthdate')->nullable()->after('email');
            $table->enum('sex', ['Male', 'Female'])->nullable()->after('birthdate');
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated'])->default('Single')->after('sex');
            $table->string('bank_account_number')->nullable()->after('pagibig_number');
            $table->string('bank_name')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'birthdate',
                'sex',
                'civil_status',
                'bank_account_number',
                'bank_name',
            ]);
        });
    }
};
