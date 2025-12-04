# Clean Architecture - AccuPay Code Organization

## Layer Structure

```
┌─────────────────────────────────────────────────────────────────┐
│                      PRESENTATION LAYER                         │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Controllers (Handle HTTP Requests/Responses)             │  │
│  │  - EmployeeController.php                                 │  │
│  │  - AdminController.php                                    │  │
│  └────────────┬─────────────────────────────────────────────┘  │
└───────────────┼─────────────────────────────────────────────────┘
                │ Dependency Injection
                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      BUSINESS LOGIC LAYER                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Services (Business Rules & Logic)                        │  │
│  │                                                            │  │
│  │  Admin/                      Employee/                    │  │
│  │  └─ EmployeeRecordService    ├─ EmployeeDashboardService │  │
│  │                               ├─ EmployeeProfileService   │  │
│  │  Shared/                      └─ EmployeeLoanService      │  │
│  │  ├─ AttendanceService                                     │  │
│  │  ├─ PayrollService                                        │  │
│  │  ├─ LeaveService                                          │  │
│  │  ├─ LoanService                                           │  │
│  │  └─ ...more                                               │  │
│  └────────────┬─────────────────────────────────────────────┘  │
└───────────────┼─────────────────────────────────────────────────┘
                │ Uses
                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATA LAYER                               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Models (Database Entities)                               │  │
│  │  - Employee, Attendance, Payslip, Loan, etc.             │  │
│  └────────────┬─────────────────────────────────────────────┘  │
└───────────────┼─────────────────────────────────────────────────┘
                │ Eloquent ORM
                ▼
┌─────────────────────────────────────────────────────────────────┐
│                       DATABASE (MySQL)                          │
└─────────────────────────────────────────────────────────────────┘
```

## Request Flow Example: Employee Dashboard

```
User Request
    │
    ▼
┌──────────────────────────────────────┐
│ GET /employee/dashboard?month=12     │
└──────────────┬───────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────────────┐
│ EmployeeController::dashboard(Request $request)     │
│                                                      │
│  1. Get authenticated employee                      │
│  2. Extract month/year from request                 │
│  3. Call service method ──────┐                     │
└───────────────────────────────┼──────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│ EmployeeDashboardService::getDashboardData()               │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐  │
│  │ getAttendanceStatistics()                           │  │
│  │  └─> Query Attendance model                        │  │
│  │  └─> Count present/late/absent                     │  │
│  │  └─> Sum overtime hours                            │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐  │
│  │ getLeaveStatistics()                                │  │
│  │  └─> Query LeaveApplication model                  │  │
│  │  └─> Calculate remaining leave                     │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐  │
│  │ getLoanStatistics()                                 │  │
│  │  └─> Query Loan model                              │  │
│  │  └─> Count active loans                            │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                             │
│  Return consolidated data array                             │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│ EmployeeController                                   │
│  └─> Return view with data                           │
└──────────────────────────┬───────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│ Blade View: employee.empDashboard                    │
│  └─> Render HTML with attendance stats               │
└──────────────────────────┬───────────────────────────┘
                           │
                           ▼
                    HTML Response to User
```

## Benefits of This Architecture

### ✅ Single Responsibility
Each class has one job:
- **Controllers**: Handle HTTP
- **Services**: Business logic
- **Models**: Database access

### ✅ Dependency Injection
```php
public function __construct(
    EmployeeDashboardService $dashboardService,
    EmployeeProfileService $profileService,
    EmployeeLoanService $loanService
) {
    // Laravel auto-injects these
}
```

### ✅ Testability
```php
// Easy to mock services in tests
$mockService = Mockery::mock(EmployeeDashboardService::class);
$mockService->shouldReceive('getDashboardData')->andReturn([...]);
```

### ✅ Reusability
Services can be used in:
- Controllers
- Console commands
- Queue jobs
- API endpoints

### ✅ Maintainability
```
Need to change attendance calculation?
  → Edit EmployeeDashboardService::getAttendanceStatistics()
  → All controllers automatically use updated logic
  → No need to hunt through multiple controllers
```
