<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\SupportReport;
use App\Models\ActivityLog;

class EmployeeSupportService
{
    /**
     * Get all support reports for an employee
     */
    public function getSupportReports(Employee $employee)
    {
        return SupportReport::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new support report
     */
    public function createSupportReport(Employee $employee, array $data): SupportReport
    {
        $report = SupportReport::create([
            'employee_id' => $employee->id,
            'type' => $data['type'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'pending',
        ]);

        ActivityLog::log(
            'create',
            'support',
            "Employee {$employee->full_name} submitted a support ticket ({$data['type']})",
            $report->id,
            ['employee' => $employee->full_name, 'type' => $data['type'], 'subject' => $data['subject']]
        );

        return $report;
    }
}
