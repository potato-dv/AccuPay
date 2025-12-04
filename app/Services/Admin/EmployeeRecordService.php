<?php

namespace App\Services\Admin;

use App\Models\Employee;
use App\Models\EmployeeRecord;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRecordService
{
    /**
     * Get all employees with their record counts
     */
    public function getEmployeesWithRecordCounts(string $search = ''): Collection
    {
        $query = Employee::query()
            ->withCount('employeeRecords')
            ->orderBy('first_name')
            ->orderBy('last_name');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Get employee records for a specific employee
     */
    public function getEmployeeRecords(int $employeeId): array
    {
        $employee = Employee::with([
            'workSchedule',
            'employeeRecords' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'employeeRecords.approver',
            'employeeRecords.creator',
            'payslips.payroll',
            'loans',
            'leaveApplications'
        ])->findOrFail($employeeId);

        return [
            'employee' => $employee,
            'records' => $employee->employeeRecords,
        ];
    }

    /**
     * Get a specific employee record
     */
    public function getEmployeeRecord(int $recordId): EmployeeRecord
    {
        return EmployeeRecord::with([
            'employee.workSchedule',
            'approvedBy'
        ])->findOrFail($recordId);
    }
}
