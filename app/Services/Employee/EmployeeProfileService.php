<?php

namespace App\Services\Employee;

use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmployeeProfileService
{
    /**
     * Get employee by user
     */
    public function getEmployeeByUser($user): ?Employee
    {
        return Employee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
    }

    /**
     * Auto-link employee to user if not linked
     */
    public function linkEmployeeToUser(Employee $employee, $user): void
    {
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }
    }

    /**
     * Update employee profile
     */
    public function updateProfile(Employee $employee, array $data): Employee
    {
        $validator = Validator::make($data, [
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'address' => 'required|string|max:500',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|regex:/^[0-9]{10,11}$/',
        ], [
            'phone.regex' => 'Phone number must be 10 or 11 digits.',
            'emergency_phone.regex' => 'Emergency phone must be 10 or 11 digits.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $employee->update($validator->validated());

        return $employee->fresh();
    }

    /**
     * Update employee password
     */
    public function updatePassword(Employee $employee, string $currentPassword, string $newPassword): bool
    {
        if (!$employee->user) {
            return false;
        }

        if (!\Illuminate\Support\Facades\Hash::check($currentPassword, $employee->user->password)) {
            return false;
        }

        $employee->user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($newPassword)
        ]);

        return true;
    }
}
