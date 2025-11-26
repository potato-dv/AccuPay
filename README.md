# AccuPay - Professional Payroll Management System

<p align="center">
<img src="public/images/accupay.png" width="200" alt="AccuPay Logo">
</p>

## About AccuPay

AccuPay is a comprehensive payroll management system designed for Philippine businesses, built with Laravel. It provides complete employee management, attendance tracking, leave management, and payroll processing with Philippine labor law compliance features.

### Key Features

✅ **Employee Master File Management**
- Complete employee profiles with government IDs (TIN, SSS, PhilHealth, Pag-IBIG)
- Flexible work schedule assignment per employee
- Leave credits management (Vacation, Sick, Emergency)
- Emergency contact information
- Employment status tracking

✅ **Work Schedule System** ⭐ NEW
- 6 pre-configured schedule templates (Mon-Fri, Mon-Sat, Night Shift, Part-Time, etc.)
- Customizable working days per employee
- Configurable shift hours and break times
- Overtime rate multipliers
- Grace period for late arrivals
- Schedule-aware attendance validation

✅ **Attendance Tracking**
- Daily time in/out recording
- Automatic hours worked calculation
- Overtime hours tracking
- Status management (Present, Absent, Late, On-leave)
- Only counts scheduled workdays

✅ **Leave Management**
- Multiple leave types (Vacation, Sick, Emergency)
- Leave application workflow with approval system
- Automatic leave balance calculation
- Admin remarks and approval tracking

✅ **Payroll Processing**
- Basic salary + overtime calculation
- Philippine government contributions (SSS, PhilHealth, Pag-IBIG)
- Tax withholding (placeholder)
- Allowances and bonuses
- Detailed payslip generation
- Payroll period management

✅ **Employee Self-Service Portal**
- Personal dashboard with attendance stats
- Leave application submission
- Payslip access and download
- Profile management
- Work schedule viewing

✅ **Reporting & Analytics**
- Employee attendance reports
- Leave utilization tracking
- Payroll summaries
- Date range filtering
- Export capabilities

---

### Default Credentials

**Admin Account:**
- Email: `admin@accupay.com`
- Password: `password`

**Employee Account:**
- Email: `lures@gmail.com`
- Password: `EMP00001`

---

## Documentation

📚 **Comprehensive Guides Available:**

1. **[SYSTEM_DOCUMENTATION.md](SYSTEM_DOCUMENTATION.md)**
   - Complete system overview
   - Implemented features and capabilities
   - Missing features and limitations
   - Philippine labor law compliance
   - Security considerations
   - Recommended improvements

2. **[WORK_SCHEDULE_GUIDE.md](WORK_SCHEDULE_GUIDE.md)**
   - 6 pre-configured work schedules explained
   - How to assign schedules to employees
   - Creating custom schedules
   - Leave credits management
   - Government ID requirements
   - Best practices and troubleshooting

3. **[TESTING_GUIDE.md](TESTING_GUIDE.md)**
   - Step-by-step testing instructions
   - Sample data generation
   - Manual attendance entry
   - Payroll testing workflow
   - Expected results

---

## System Capabilities

### ✅ What AccuPay CAN Do

- Manage employee master data with government IDs
- Assign flexible work schedules (different days/shifts per employee)
- Track daily attendance with time in/out
- Process leave applications with approval workflow
- Generate payroll with basic calculations
- Create detailed payslips
- Manage leave credits automatically
- Provide employee self-service portal
- Generate reports and analytics

### ⚠️ Current Limitations (See SYSTEM_DOCUMENTATION.md)

- No biometric device integration (manual attendance entry)
- Tax calculation is placeholder (BIR tables not implemented)
- SSS/PhilHealth/Pag-IBIG use fixed rates (not dynamic)
- No 13th month pay automation
- No holiday calendar management
- No loan management system
- No email notifications
- No government compliance reports (BIR Alphalist, SSS R3)

---

## Work Schedules

AccuPay includes 6 professional work schedule templates:

1. **Standard Mon-Fri (8hrs)** - Regular office, 40 hrs/week
2. **Standard Mon-Sat (8hrs)** - Retail/manufacturing, 48 hrs/week
3. **Night Shift (8hrs)** - BPO/security, with night differential
4. **Part-Time (4hrs)** - 20 hrs/week
5. **Flexible 3-Day Week** - Custom days, 24 hrs/week
6. **12-Hour Shift (3 days)** - Compressed week, 36 hrs/week

Each schedule includes:
- Customizable working days
- Shift start/end times
- Break periods (paid/unpaid)
- Overtime settings
- Grace period for late arrivals

---

## Technical Stack

- **Framework:** Laravel 12.39.0
- **PHP:** 8.4.15
- **Database:** MySQL 8.0
- **Frontend:** Blade Templates, CSS
- **Icons:** Font Awesome 7.0.1
- **Server:** Laravel Sail (Docker)

---

## Database Schema

### Core Tables
- `employees` - Employee master file with work schedules
- `work_schedules` - Work schedule templates
- `attendance` - Daily attendance records
- `leave_applications` - Leave requests and approvals
- `payrolls` - Payroll period information
- `payslips` - Employee payslips
- `users` - System authentication

---

## Usage Examples

### Adding an Employee with Work Schedule

1. Admin → Employee List → Add Employee
2. Fill in employee details
3. Select work schedule: "Standard Mon-Sat (8hrs)"
4. Add government IDs (TIN, SSS, PhilHealth, Pag-IBIG)
5. Set leave credits (defaults: 15, 15, 5)
6. Save

### Recording Attendance

1. Admin → Manage Attendance → Add Attendance
2. Select employee
3. Enter date, time in, time out
4. System auto-calculates hours worked and overtime
5. Save

### Processing Payroll

1. Admin → Manage Payroll → Create Payroll
2. Set payroll period (e.g., "November 1-15, 2025")
3. System calculates based on attendance and schedule
4. Generate payslips for all employees
5. Review and approve

---

## Philippine Labor Law Compliance

### Implemented Features
✅ 8-hour workday support  
✅ Overtime tracking (1.25x - 1.50x rates)  
✅ Leave benefits (SIL compliant)  
✅ Government contributions (SSS, PhilHealth, Pag-IBIG)  
✅ Holiday pay multipliers  
✅ Night differential tracking  

### Required Enhancements
⚠️ BIR tax tables (TRAIN Law 2023)  
⚠️ 13th month pay computation  
⚠️ Dynamic government contribution rates  
⚠️ Holiday calendar management  
⚠️ Compliance reports (BIR Alphalist, SSS R3)  

---

## Support & Maintenance

### Regular Maintenance
- **Monthly:** Update government contribution rates (if changed)
- **Quarterly:** Review tax tables
- **Annually:** Update holiday calendar and leave credits
- **Daily:** Database backups (critical)

### Backup Command
```bash
./vendor/bin/sail artisan backup:run
```

---

## Security

AccuPay implements:
- Password hashing (bcrypt)
- CSRF protection
- Role-based access control
- SQL injection protection (Eloquent ORM)

**Recommended additions:**
- Two-factor authentication
- Session timeout
- IP whitelisting
- Activity logging

---

## Contributing

This is a private payroll system. For feature requests or bug reports, contact your development team.


