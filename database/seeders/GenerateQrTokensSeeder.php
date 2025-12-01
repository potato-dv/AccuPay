<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Str;

class GenerateQrTokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::whereNull('qr_token')->get();

        foreach ($employees as $employee) {
            $employee->qr_token = Str::random(32) . '-' . $employee->id;
            $employee->save();
        }

        $this->command->info('QR tokens generated for ' . $employees->count() . ' employees.');
    }
}
