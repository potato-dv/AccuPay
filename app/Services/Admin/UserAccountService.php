<?php

namespace App\Services\Admin;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAccountService
{
    /**
     * Create user account for employee
     */
    public function createUserAccount(int $employeeId, array $data): array
    {
        $employee = Employee::findOrFail($employeeId);

        if ($employee->user_id) {
            throw new \Exception('This employee already has a user account!');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($employee->employee_id),
            'role' => 'employee',
        ]);

        $employee->update(['user_id' => $user->id]);

        return [
            'user' => $user,
            'default_password' => $employee->employee_id,
        ];
    }

    /**
     * Delete user account for employee
     */
    public function deleteUserAccount(int $employeeId): void
    {
        $employee = Employee::findOrFail($employeeId);

        if (!$employee->user_id) {
            throw new \Exception('This employee does not have a user account!');
        }

        $user = $employee->user;
        $employee->update(['user_id' => null]);
        $user->delete();
    }

    /**
     * Reset user password to employee ID
     */
    public function resetPassword(int $employeeId): string
    {
        $employee = Employee::findOrFail($employeeId);

        if (!$employee->user_id) {
            throw new \Exception('This employee does not have a user account!');
        }

        $user = $employee->user;
        $user->update(['password' => Hash::make($employee->employee_id)]);

        return $employee->employee_id;
    }
}
