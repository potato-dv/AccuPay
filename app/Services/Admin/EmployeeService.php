<?php

namespace App\Services\Admin;

use App\Models\Employee;

class EmployeeService
{
    /**
     * Generate unique employee ID
     */
    public function generateEmployeeId(): string
    {
        $lastEmployee = Employee::latest('id')->first();
        $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
        
        return 'EMP' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get all employees
     */
    public function getAllEmployees()
    {
        return Employee::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get employee by ID with relationships
     */
    public function getEmployeeById(int $id, array $relationships = [])
    {
        return Employee::with($relationships)->findOrFail($id);
    }

    /**
     * Get active employees
     */
    public function getActiveEmployees()
    {
        return Employee::where('status', 'active')
            ->orderBy('employee_id')
            ->get();
    }
}
