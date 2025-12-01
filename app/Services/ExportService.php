<?php

namespace App\Services;

use Illuminate\Support\Facades\Response;

class ExportService
{
    /**
     * Export report to CSV
     */
    public function exportToCSV(string $type, $data, array $summary, string $dateFrom, string $dateTo)
    {
        $filename = "{$type}_report_" . now()->format('Y-m-d_His');
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($type, $data, $summary, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            $this->writeCSVHeader($file, $type, $dateFrom, $dateTo);
            $this->writeCSVData($file, $type, $data);
            $this->writeCSVSummary($file, $type, $summary);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Write CSV header
     */
    private function writeCSVHeader($file, string $type, string $dateFrom, string $dateTo): void
    {
        fputcsv($file, ['ACCUPAY INC. - ' . strtoupper($type) . ' REPORT']);
        fputcsv($file, ['Period: ' . $dateFrom . ' to ' . $dateTo]);
        fputcsv($file, ['Generated: ' . now()->format('F d, Y h:i A')]);
        fputcsv($file, []);
    }

    /**
     * Write CSV data based on report type
     */
    private function writeCSVData($file, string $type, $data): void
    {
        $method = 'write' . str_replace('_', '', ucwords($type, '_')) . 'Data';
        
        if (method_exists($this, $method)) {
            $this->$method($file, $data);
        }
    }

    /**
     * Write CSV summary
     */
    private function writeCSVSummary($file, string $type, array $summary): void
    {
        fputcsv($file, []);
        fputcsv($file, ['SUMMARY']);
        
        foreach ($summary as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            
            // Handle nested arrays (for activity log by_action and by_module)
            if (is_array($value)) {
                fputcsv($file, [$label . ':']);
                foreach ($value as $subKey => $subValue) {
                    $subLabel = '  ' . ucfirst($subKey);
                    $formattedSubValue = is_numeric($subValue) ? number_format($subValue, 0) : $subValue;
                    fputcsv($file, [$subLabel, $formattedSubValue]);
                }
            } else {
                $formattedValue = is_numeric($value) ? number_format($value, 2) : $value;
                fputcsv($file, [$label, $formattedValue]);
            }
        }
    }

    private function writePayrollData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Payroll Period',
            'Period Start',
            'Period End',
            'Basic Salary',
            'Gross Pay',
            'Deductions',
            'Net Pay',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee->employee_id,
                $row->employee->full_name,
                $row->payroll->payroll_period,
                $row->payroll->period_start->format('M d, Y'),
                $row->payroll->period_end->format('M d, Y'),
                number_format($row->basic_salary, 2),
                number_format($row->gross_pay, 2),
                number_format($row->total_deductions, 2),
                number_format($row->net_pay, 2),
            ]);
        }
    }

    private function writeDeductionsData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Payroll Period',
            'SSS',
            'PhilHealth',
            'Pag-IBIG',
            'Tax',
            'Other Deductions',
            'Total Deductions',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee->employee_id,
                $row->employee->full_name,
                $row->payroll->payroll_period,
                number_format($row->sss, 2),
                number_format($row->philhealth, 2),
                number_format($row->pagibig, 2),
                number_format($row->tax, 2),
                number_format($row->other_deductions, 2),
                number_format($row->total_deductions, 2),
            ]);
        }
    }

    private function writeOvertimeData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Date',
            'Time In',
            'Time Out',
            'Regular Hours',
            'Overtime Hours',
            'Status',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee->employee_id,
                $row->employee->full_name,
                $row->date->format('M d, Y'),
                $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '-',
                $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '-',
                number_format($row->hours_worked, 2),
                number_format($row->overtime_hours, 2),
                ucfirst($row->status),
            ]);
        }
    }

    private function writeAttendanceData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Date',
            'Time In',
            'Time Out',
            'Hours Worked',
            'Overtime Hours',
            'Status',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee->employee_id,
                $row->employee->full_name,
                $row->date->format('M d, Y'),
                $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '-',
                $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '-',
                number_format($row->hours_worked, 2),
                number_format($row->overtime_hours, 2),
                ucfirst($row->status),
            ]);
        }
    }

    private function writeLeaveData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Leave Type',
            'Start Date',
            'End Date',
            'Days',
            'Reason',
            'Status',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee->employee_id,
                $row->employee->full_name,
                ucfirst($row->leave_type),
                $row->start_date->format('M d, Y'),
                $row->end_date->format('M d, Y'),
                $row->days_count,
                $row->reason,
                ucfirst($row->status),
            ]);
        }
    }

    private function writeEmployeeData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Name',
            'Position',
            'Department',
            'Employment Type',
            'Contact',
            'Email',
            'Status',
            'Hire Date',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee_id,
                $row->full_name,
                $row->position,
                $row->department,
                ucfirst($row->employment_type),
                $row->phone,
                $row->email,
                ucfirst($row->status),
                $row->hire_date->format('M d, Y'),
            ]);
        }
    }

    private function writeLeaveBalanceData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Department',
            'Position',
            'Total Leave Credits',
            'Used Leave',
            'Remaining Leave',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee_id,
                $row->full_name,
                $row->department,
                $row->position,
                $row->total_leave ?? 12,
                $row->used_leave ?? 0,
                $row->remaining_leave ?? 12,
            ]);
        }
    }

    private function writeEmployeeCompensationData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Name',
            'Position',
            'Department',
            'Employment Type',
            'Basic Salary',
            'Hourly Rate',
            'Status',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee_id,
                $row->full_name,
                $row->position,
                $row->department,
                ucfirst($row->employment_type),
                number_format($row->basic_salary ?? 0, 2),
                number_format($row->hourly_rate ?? 0, 2),
                ucfirst($row->status),
            ]);
        }
    }

    private function writePayrollDetailedData($file, $data): void
    {
        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Payroll Period',
            'Basic Salary',
            'Allowances',
            'OT Pay',
            'Gross Pay',
            'SSS',
            'PhilHealth',
            'Pag-IBIG',
            'Tax',
            'Loan Deduction',
            'Absence Deduction',
            'Total Deductions',
            'Net Pay',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->employee->employee_id,
                $row->employee->full_name,
                $row->payroll->payroll_period,
                number_format($row->basic_salary, 2),
                number_format($row->allowances, 2),
                number_format($row->overtime_pay, 2),
                number_format($row->gross_pay, 2),
                number_format($row->sss, 2),
                number_format($row->philhealth, 2),
                number_format($row->pagibig, 2),
                number_format($row->tax, 2),
                number_format($row->loan_deduction, 2),
                number_format($row->absence_deduction, 2),
                number_format($row->total_deductions, 2),
                number_format($row->net_pay, 2),
            ]);
        }
    }

    private function writeActivityLogData($file, $data): void
    {
        fputcsv($file, [
            'Date/Time',
            'User',
            'Role',
            'Module',
            'Action',
            'Description',
            'IP Address',
        ]);
        
        foreach ($data as $row) {
            fputcsv($file, [
                $row->created_at->format('M d, Y h:i A'),
                $row->user ? $row->user->name : 'System',
                $row->user ? ucfirst($row->user->role) : 'System',
                ucfirst($row->module),
                ucfirst($row->action),
                $row->description,
                $row->ip_address ?? '-',
            ]);
        }
    }
}
