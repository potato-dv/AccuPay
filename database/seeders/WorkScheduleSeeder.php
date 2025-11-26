<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkSchedule;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'schedule_name' => 'Standard Mon-Fri (8hrs)',
                'description' => 'Standard Monday to Friday work week, 8 hours per day',
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
                'sunday' => false,
                'shift_start' => '08:00:00',
                'shift_end' => '17:00:00',
                'daily_hours' => 8.00,
                'weekly_hours' => 40.00,
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'break_paid' => false,
                'overtime_allowed' => true,
                'overtime_rate_multiplier' => 1.25,
                'grace_period_minutes' => 15,
                'is_active' => true,
            ],
            [
                'schedule_name' => 'Standard Mon-Sat (8hrs)',
                'description' => 'Monday to Saturday work week, 8 hours per day',
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => true,
                'sunday' => false,
                'shift_start' => '08:00:00',
                'shift_end' => '17:00:00',
                'daily_hours' => 8.00,
                'weekly_hours' => 48.00,
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'break_paid' => false,
                'overtime_allowed' => true,
                'overtime_rate_multiplier' => 1.25,
                'grace_period_minutes' => 15,
                'is_active' => true,
            ],
            [
                'schedule_name' => 'Night Shift (8hrs)',
                'description' => 'Night shift Monday to Friday, 8 hours with night differential',
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
                'sunday' => false,
                'shift_start' => '22:00:00',
                'shift_end' => '06:00:00',
                'daily_hours' => 8.00,
                'weekly_hours' => 40.00,
                'break_start' => '02:00:00',
                'break_end' => '03:00:00',
                'break_paid' => false,
                'overtime_allowed' => true,
                'overtime_rate_multiplier' => 1.30,
                'grace_period_minutes' => 10,
                'is_active' => true,
            ],
            [
                'schedule_name' => 'Part-Time (4hrs)',
                'description' => 'Part-time schedule, 4 hours per day Monday to Friday',
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
                'sunday' => false,
                'shift_start' => '09:00:00',
                'shift_end' => '13:00:00',
                'daily_hours' => 4.00,
                'weekly_hours' => 20.00,
                'break_start' => null,
                'break_end' => null,
                'break_paid' => false,
                'overtime_allowed' => false,
                'overtime_rate_multiplier' => 1.25,
                'grace_period_minutes' => 10,
                'is_active' => true,
            ],
            [
                'schedule_name' => 'Flexible 3-Day Week',
                'description' => 'Three days per week (Mon, Wed, Sat) - 8 hours each',
                'monday' => true,
                'tuesday' => false,
                'wednesday' => true,
                'thursday' => false,
                'friday' => false,
                'saturday' => true,
                'sunday' => false,
                'shift_start' => '08:00:00',
                'shift_end' => '17:00:00',
                'daily_hours' => 8.00,
                'weekly_hours' => 24.00,
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'break_paid' => false,
                'overtime_allowed' => true,
                'overtime_rate_multiplier' => 1.25,
                'grace_period_minutes' => 15,
                'is_active' => true,
            ],
            [
                'schedule_name' => '12-Hour Shift (3 days)',
                'description' => 'Compressed work week - 12 hours per day, 3 days (Tue, Thu, Sat)',
                'monday' => false,
                'tuesday' => true,
                'wednesday' => false,
                'thursday' => true,
                'friday' => false,
                'saturday' => true,
                'sunday' => false,
                'shift_start' => '07:00:00',
                'shift_end' => '19:00:00',
                'daily_hours' => 12.00,
                'weekly_hours' => 36.00,
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'break_paid' => true,
                'overtime_allowed' => true,
                'overtime_rate_multiplier' => 1.50,
                'grace_period_minutes' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($schedules as $schedule) {
            WorkSchedule::updateOrCreate(
                ['schedule_name' => $schedule['schedule_name']],
                $schedule
            );
        }

        $this->command->info('Default work schedules created successfully!');
    }
}
