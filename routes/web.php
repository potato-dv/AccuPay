<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Employee\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Employee Routes
    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('/employee/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
    Route::put('/employee/profile/update', [EmployeeController::class, 'updateProfile'])->name('employee.profile.update');
    Route::get('/employee/leave/application', [EmployeeController::class, 'leaveApplication'])->name('employee.leave.application');
    Route::post('/employee/leave/store', [EmployeeController::class, 'storeLeaveApplication'])->name('employee.leave.store');
    Route::get('/employee/leave/status', [EmployeeController::class, 'leaveStatus'])->name('employee.leave.status');
    Route::get('/employee/payslip', [EmployeeController::class, 'payslip'])->name('employee.payslip');
    Route::get('/employee/report-support', [EmployeeController::class, 'reportSupport'])->name('employee.report');
    Route::post('/employee/support/submit', [EmployeeController::class, 'submitSupportReport'])->name('employee.support.submit');
    Route::get('/employee/settings', [EmployeeController::class, 'settings'])->name('employee.settings');
    Route::put('/employee/settings/password', [EmployeeController::class, 'updatePassword'])->name('employee.password.update');
    
    // Admin Routes
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Attendance routes
    Route::get('/admin/attendance', [AdminController::class, 'manageAttendance'])->name('admin.attendance');
    Route::post('/admin/attendance/store', [AdminController::class, 'storeAttendance'])->name('admin.attendance.store');
    Route::put('/admin/attendance/update/{id}', [AdminController::class, 'updateAttendance'])->name('admin.attendance.update');
    Route::delete('/admin/attendance/delete/{id}', [AdminController::class, 'deleteAttendance'])->name('admin.attendance.delete');
    
    // Employee routes
    Route::get('/admin/employees', [AdminController::class, 'manageEmployees'])->name('admin.employees');
    Route::get('/admin/employees/add', [AdminController::class, 'addEmployee'])->name('admin.employees.add');
    Route::post('/admin/employees/store', [AdminController::class, 'storeEmployee'])->name('admin.employees.store');
    Route::get('/admin/employees/view/{id}', [AdminController::class, 'viewEmployee'])->name('admin.employees.view');
    Route::get('/admin/employees/edit/{id}', [AdminController::class, 'editEmployee'])->name('admin.employees.edit');
    Route::put('/admin/employees/update/{id}', [AdminController::class, 'updateEmployee'])->name('admin.employees.update');
    Route::get('/admin/employees/delete/{id}', [AdminController::class, 'deleteEmployee'])->name('admin.employees.delete');
    Route::delete('/admin/employees/destroy/{id}', [AdminController::class, 'destroyEmployee'])->name('admin.employees.destroy');
    
    // Payroll routes
    Route::get('/admin/payroll', [AdminController::class, 'managePayroll'])->name('admin.payroll');
    Route::post('/admin/payroll/generate', [AdminController::class, 'generatePayroll'])->name('admin.payroll.generate');
    Route::get('/admin/payroll/view/{id}', [AdminController::class, 'viewPayroll'])->name('admin.payroll.view');
    Route::get('/admin/payroll/edit/{id}', [AdminController::class, 'editPayroll'])->name('admin.payroll.edit');
    Route::put('/admin/payroll/update/{id}', [AdminController::class, 'updatePayroll'])->name('admin.payroll.update');
    Route::put('/admin/payroll/approve/{id}', [AdminController::class, 'approvePayroll'])->name('admin.payroll.approve');
    Route::delete('/admin/payroll/delete/{id}', [AdminController::class, 'deletePayroll'])->name('admin.payroll.delete');
    Route::get('/admin/payslip/view/{id}', [AdminController::class, 'viewEmployeePayslip'])->name('admin.viewEmployeePayslip');
    
    Route::get('/admin/payslip', [AdminController::class, 'managePayslip'])->name('admin.payslip');
    
    // Leave management routes
    Route::get('/admin/leave-applications', [AdminController::class, 'manageLeaveApplications'])->name('admin.leave');
    Route::put('/admin/leave-applications/approve/{id}', [AdminController::class, 'approveLeave'])->name('admin.leave.approve');
    Route::put('/admin/leave-applications/reject/{id}', [AdminController::class, 'rejectLeave'])->name('admin.leave.reject');
    
    Route::get('/admin/reports', [AdminController::class, 'manageReports'])->name('admin.reports');
    Route::get('/admin/support-reports', [AdminController::class, 'manageSupportReports'])->name('admin.support.reports');
    Route::post('/admin/support-reports/reply/{id}', [AdminController::class, 'replySupportReport'])->name('admin.support.reply');
    Route::put('/admin/support-reports/status/{id}', [AdminController::class, 'updateSupportStatus'])->name('admin.support.status');
    
    // User Accounts
    Route::get('/admin/user-accounts', [AdminController::class, 'userAccounts'])->name('admin.users');
    Route::post('/admin/user-accounts/create/{employeeId}', [AdminController::class, 'createUserAccount'])->name('admin.users.create');
    Route::delete('/admin/user-accounts/delete/{employeeId}', [AdminController::class, 'deleteUserAccount'])->name('admin.users.delete');
    Route::post('/admin/user-accounts/reset-password/{employeeId}', [AdminController::class, 'resetUserPassword'])->name('admin.users.resetPassword');
    
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
});
