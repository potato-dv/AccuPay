# AccuPay System Testing Guide

## Complete Step-by-Step Guide to Test the Payroll System

### Prerequisites
- System is running (Laravel Sail is up)
- Admin account exists: `admin@accupay.com`
- Employee exists: Lures Lorenzo (`lures@gmail.com` / `EMP00001`)

---

## STEP 1: Add Attendance Records (Manual Entry)

Since there's no fingerprint/automatic attendance system yet, we'll add attendance manually through the admin panel.

### Login as Admin
1. Go to `http://localhost`
2. Login with: `admin@accupay.com` / `password`

### Add Attendance for Employee

Navigate to **Manage Attendance** and add the following records for Employee EMP00001:

#### Week 1 (Nov 1-8, 2025):
| Date | Employee | Time In | Time Out | Hours | Overtime | Status |
|------|----------|---------|----------|-------|----------|--------|
| Nov 1 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 4 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 5 | EMP00001 | 08:00 AM | 07:00 PM | 8 | 2 | Present |
| Nov 6 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 7 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 8 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |

#### Week 2 (Nov 11-15, 2025):
| Date | Employee | Time In | Time Out | Hours | Overtime | Status |
|------|----------|---------|----------|-------|----------|--------|
| Nov 11 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 12 | EMP00001 | 08:30 AM | 05:00 PM | 7.5 | 0 | Late |
| Nov 13 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 14 | EMP00001 | 08:00 AM | 06:00 PM | 8 | 1 | Present |
| Nov 15 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |

#### Week 3 (Nov 18-22, 2025):
| Date | Employee | Time In | Time Out | Hours | Overtime | Status |
|------|----------|---------|----------|-------|----------|--------|
| Nov 18 | EMP00001 | - | - | 0 | 0 | Absent |
| Nov 19 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 20 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 21 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |
| Nov 22 | EMP00001 | 08:00 AM | 05:00 PM | 8 | 0 | Present |

**Total Summary:**
- Days Present: 15 days
- Days Absent: 1 day
- Late: 1 day
- Total Hours: 119.5 hours
- Overtime Hours: 3 hours

---

## STEP 2: Generate Payroll (Admin Panel)

### Navigate to Manage Payroll
1. Click **Manage Payroll** in admin sidebar
2. Click **Generate Payroll** button

### Fill Payroll Details:
```
Payroll Period: November 1-15, 2025
Period Start: 2025-11-01
Period End: 2025-11-15
Payment Date: 2025-11-16
Notes: Mid-month payroll for November
```

### System Will Calculate:
For Employee EMP00001 (Lures Lorenzo):
- Basic Salary: ₱50,000.00 (from employee record)
- Hourly Rate: ₱600.00
- Days Worked: 10 days (Nov 1-15 period)
- Hours Worked: 79.5 hours
- Overtime Hours: 3 hours
- Overtime Pay: 3 × ₱600 = ₱1,800.00

**Calculation:**
```
Basic Pay (semi-monthly) = ₱50,000 / 2 = ₱25,000.00
Overtime Pay = 3 hours × ₱600 = ₱1,800.00
Gross Pay = ₱25,000 + ₱1,800 = ₱26,800.00

Deductions:
- SSS: ₱1,125.00
- PhilHealth: ₱1,000.00
- Pag-IBIG: ₱100.00
Total Deductions = ₱2,225.00

Net Pay = ₱26,800 - ₱2,225 = ₱24,575.00
```

---

## STEP 3: Add Leave Applications

### As Employee:
1. Logout from admin
2. Login as employee: `lures@gmail.com` / `EMP00001`
3. Go to **Leave Application**
4. Submit a leave request:
   ```
   Leave Type: Sick Leave
   Start Date: 2025-11-18
   End Date: 2025-11-18
   Reason: Not feeling well
   ```

### As Admin - Approve Leave:
1. Login as admin
2. Go to **Leave Requests**
3. Find the sick leave request
4. Click **Approve**
5. Add admin remarks: "Approved. Get well soon!"

---

## STEP 4: View Reports

### Employee Reports:
1. Login as employee
2. Go to **Reports**
3. You should see:
   - Total Leaves Taken: 1 day (approved sick leave)
   - Last Month Net Pay: ₱24,575.00
   - Average Monthly Pay: ₱24,575.00

### Admin Reports:
1. Login as admin
2. Go to **Reports**
3. Select Report Type:
   - **Payroll Report**: See all payslips generated
   - **Attendance Report**: Filter by date range (Nov 1-25)
   - **Leave Report**: See all leave applications
   - **Employee Report**: View all employee data

---

## ALTERNATIVE: Use SQL Commands for Faster Setup

If you want to quickly populate test data, run these SQL commands:

```bash
# Connect to the database
./vendor/bin/sail artisan tinker
```

Then paste this code:

```php
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\Payroll;
use App\Models\Payslip;

// Get the employee
$employee = Employee::first();

// Add attendance records for November 1-25, 2025
$attendanceData = [
    ['2025-11-01', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-04', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-05', '08:00:00', '19:00:00', 8, 2, 'present'],
    ['2025-11-06', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-07', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-08', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-11', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-12', '08:30:00', '17:00:00', 7.5, 0, 'late'],
    ['2025-11-13', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-14', '08:00:00', '18:00:00', 8, 1, 'present'],
    ['2025-11-15', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-18', null, null, 0, 0, 'absent'],
    ['2025-11-19', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-20', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-21', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-22', '08:00:00', '17:00:00', 8, 0, 'present'],
    ['2025-11-25', '08:00:00', '17:00:00', 8, 0, 'present'],
];

foreach($attendanceData as $data) {
    Attendance::create([
        'employee_id' => $employee->id,
        'date' => $data[0],
        'time_in' => $data[1],
        'time_out' => $data[2],
        'hours_worked' => $data[3],
        'overtime_hours' => $data[4],
        'status' => $data[5],
    ]);
}

echo "Attendance records created!\n";

// Add leave application
LeaveApplication::create([
    'employee_id' => $employee->id,
    'leave_type' => 'Sick Leave',
    'start_date' => '2025-11-18',
    'end_date' => '2025-11-18',
    'days_count' => 1,
    'reason' => 'Not feeling well',
    'status' => 'approved',
]);

echo "Leave application created!\n";

// Create payroll
$payroll = Payroll::create([
    'payroll_period' => 'November 1-15, 2025',
    'period_start' => '2025-11-01',
    'period_end' => '2025-11-15',
    'payment_date' => '2025-11-16',
    'total_amount' => 24575.00,
    'total_employees' => 1,
    'status' => 'processed',
    'notes' => 'Mid-month payroll',
]);

// Create payslip
Payslip::create([
    'payroll_id' => $payroll->id,
    'employee_id' => $employee->id,
    'basic_salary' => 25000.00,
    'overtime_pay' => 1800.00,
    'allowances' => 0,
    'bonuses' => 0,
    'gross_pay' => 26800.00,
    'tax' => 0,
    'sss' => 1125.00,
    'philhealth' => 1000.00,
    'pagibig' => 100.00,
    'other_deductions' => 0,
    'total_deductions' => 2225.00,
    'net_pay' => 24575.00,
    'hours_worked' => 79.5,
    'overtime_hours' => 3,
]);

echo "Payroll and payslip created!\n";
echo "Setup complete! You can now test the system.";
```

---

## Testing Checklist

### ✅ Employee Side:
- [ ] Login as employee
- [ ] View dashboard with attendance summary
- [ ] Check leave balance (should be 11 days remaining if 1 approved)
- [ ] View profile and update contact info
- [ ] Submit leave application
- [ ] View leave status
- [ ] View payslip with breakdown
- [ ] Check reports (leave summary, payroll summary)
- [ ] Change password in settings

### ✅ Admin Side:
- [ ] Login as admin
- [ ] View dashboard statistics
- [ ] Add/edit/delete attendance records
- [ ] View all employees
- [ ] Add new employee
- [ ] Edit employee information
- [ ] Delete employee
- [ ] Generate payroll
- [ ] View payroll details
- [ ] Approve/reject leave applications
- [ ] Generate reports (payroll, attendance, leave, employee)
- [ ] Create user account for employee
- [ ] Delete user account

---

## Expected Results

After following these steps, you should have:

1. **17 attendance records** for employee EMP00001
2. **1 approved leave application** (Sick Leave)
3. **1 payroll period** (November 1-15, 2025)
4. **1 payslip** with calculated earnings and deductions
5. **Working reports** showing all the data

The employee can now:
- View their dashboard with real attendance data
- See their payslip with detailed breakdown
- Check leave balance and status
- View personal reports

The admin can:
- Manage all employees and attendance
- Generate payroll for any period
- Approve/reject leave requests
- View comprehensive reports with filters

---

## Notes

- **Manual Attendance Entry**: Until fingerprint/biometric system is integrated, attendance must be entered manually by admin
- **Payroll Calculation**: System automatically calculates based on basic salary, hourly rate, and attendance records
- **Deductions**: SSS, PhilHealth, Pag-IBIG are currently set with fixed amounts (can be customized later)
- **Leave Balance**: Default is 12 days/year, decreases when leaves are approved

---

## Troubleshooting

**Problem**: No attendance showing in employee dashboard
- **Solution**: Make sure attendance dates are in the current month

**Problem**: Payroll not calculating correctly
- **Solution**: Check that employee has `basic_salary` and `hourly_rate` set

**Problem**: Employee can't login
- **Solution**: Admin needs to create user account in "User Accounts" page

**Problem**: Reports showing no data
- **Solution**: Generate data first using the SQL commands above
