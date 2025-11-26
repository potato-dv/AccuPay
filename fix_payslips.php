<?php
// This script updates existing payslips to recalculate attendance counts

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payslip;
use App\Models\Attendance;

$payslips = Payslip::with(['employee', 'payroll'])->get();

echo "Updating " . $payslips->count() . " payslips...\n";

foreach ($payslips as $payslip) {
    // Get attendance records for this payroll period
    $attendances = Attendance::where('employee_id', $payslip->employee_id)
        ->whereBetween('date', [$payslip->payroll->period_start, $payslip->payroll->period_end])
        ->get();
    
    // Recalculate working days
    $periodStart = \Carbon\Carbon::parse($payslip->payroll->period_start);
    $periodEnd = \Carbon\Carbon::parse($payslip->payroll->period_end);
    $totalWorkingDays = 0;
    $currentDate = $periodStart->copy();
    
    while ($currentDate->lte($periodEnd)) {
        if ($payslip->employee->isScheduledToWork($currentDate->format('Y-m-d'))) {
            $totalWorkingDays++;
        }
        $currentDate->addDay();
    }
    
    // Recalculate attendance counts (late employees are also present)
    $daysPresent = $attendances->whereIn('status', ['present', 'late'])->count();
    $daysLate = $attendances->where('status', 'late')->count();
    $daysOnLeave = $attendances->where('status', 'on-leave')->count();
    $daysAbsent = max(0, $totalWorkingDays - $daysPresent - $daysOnLeave);
    
    // Update payslip
    $payslip->update([
        'days_present' => $daysPresent,
        'days_late' => $daysLate,
        'days_absent' => $daysAbsent,
    ]);
    
    echo "Updated payslip #{$payslip->id}: Present={$daysPresent}, Late={$daysLate}, Absent={$daysAbsent}\n";
}

echo "Done!\n";
