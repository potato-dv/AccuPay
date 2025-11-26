# AccuPay System - Quick Reference Card

## 🚀 Quick Start

### Access URLs
- **Employee Portal:** http://localhost/employee/dashboard
- **Admin Portal:** http://localhost/admin/dashboard
- **PhpMyAdmin:** http://localhost:8082

### Default Logins
- **Admin:** admin@accupay.com / password
- **Employee:** lures@gmail.com / EMP00001

---

## 📋 Common Tasks

### Add New Employee (Admin)
1. Admin → Employee List → Add Employee
2. Fill basic info (name, email, department, position)
3. **SELECT WORK SCHEDULE** ⭐ (Required)
4. Add government IDs (TIN, SSS, PhilHealth, Pag-IBIG)
5. Set leave credits (defaults: 15, 15, 5)
6. Save

### Record Attendance (Admin)
1. Admin → Manage Attendance → Add
2. Select employee, date, time in/out
3. System auto-calculates hours + overtime
4. Save

### Apply for Leave (Employee)
1. Employee → Leave Application
2. Select type, dates, reason
3. Submit (goes to admin for approval)

### Generate Payroll (Admin)
1. Admin → Manage Payroll → Create
2. Set period dates
3. System calculates from attendance + schedule
4. Generate payslips
5. Review and approve

---

## 🗓️ Work Schedules Available

| Schedule | Days | Hours/Week | Best For |
|----------|------|------------|----------|
| Mon-Fri (8hrs) | Mon-Fri | 40 | Office workers |
| Mon-Sat (8hrs) ⭐ | Mon-Sat | 48 | Retail, manufacturing |
| Night Shift | Mon-Fri | 40 | BPO, security |
| Part-Time | Mon-Fri | 20 | Part-timers |
| 3-Day Week | Mon,Wed,Sat | 24 | Flexible workers |
| 12-Hour Shift | Tue,Thu,Sat | 36 | Compressed week |

**Current Test Employee:** Lures Lorenzo on "Mon-Sat (8hrs)"

---

## 📊 System Capabilities

### ✅ What It CAN Do
- Manage employees with flexible work schedules
- Track attendance (time in/out, hours, overtime)
- Process leave applications
- Generate payroll and payslips
- Track government IDs (TIN, SSS, PhilHealth, Pag-IBIG)
- Manage leave credits automatically
- Generate reports and analytics

### ⚠️ Current Limitations
- ❌ No biometric device (manual attendance entry)
- ❌ Tax calculation is placeholder
- ❌ SSS/PhilHealth/Pag-IBIG rates are fixed
- ❌ No 13th month pay automation
- ❌ No holiday calendar
- ❌ No loan management
- ❌ No email notifications

**See SYSTEM_DOCUMENTATION.md for complete list**

---

## 🛠️ Laravel Sail Commands

### Start/Stop System
```bash
./vendor/bin/sail up -d     # Start
./vendor/bin/sail down       # Stop
./vendor/bin/sail restart    # Restart
```

### Database
```bash
./vendor/bin/sail artisan migrate              # Run migrations
./vendor/bin/sail artisan db:seed              # Seed data
./vendor/bin/sail artisan migrate:fresh --seed # Fresh start
```

### Work Schedules
```bash
./vendor/bin/sail artisan db:seed --class=WorkScheduleSeeder
```

### Tinker (Database Console)
```bash
./vendor/bin/sail artisan tinker
```

---

## 📁 Important Files

### Documentation
- **SYSTEM_DOCUMENTATION.md** - Complete system overview
- **WORK_SCHEDULE_GUIDE.md** - Work schedule manual
- **TESTING_GUIDE.md** - Testing instructions
- **IMPLEMENTATION_SUMMARY.md** - What was implemented
- **README.md** - Project overview

### Key Code Files
- `app/Models/Employee.php` - Employee model
- `app/Models/WorkSchedule.php` - Work schedule model
- `app/Http/Controllers/Admin/AdminController.php` - Admin functions
- `app/Http/Controllers/Employee/EmployeeController.php` - Employee portal
- `database/seeders/WorkScheduleSeeder.php` - Default schedules

---

## 🔧 Troubleshooting

### Employee Can't Login
- Check email/password in database
- Verify user account exists and is linked to employee
- Check `users` table and `employees` table

### Work Schedule Not Showing
- Verify schedule is active: `is_active = true`
- Check database: `select * from work_schedules;`

### Attendance Not Counting
- Verify employee has work schedule assigned
- Check if date is a scheduled working day
- Ensure `work_schedule_id` is not null

### Payroll Calculation Wrong
- Verify attendance records exist
- Check employee's work schedule settings
- Review basic_salary and hourly_rate fields

---

## 📞 Getting Help

1. **Check Documentation:**
   - SYSTEM_DOCUMENTATION.md - System features
   - WORK_SCHEDULE_GUIDE.md - Schedule questions
   - TESTING_GUIDE.md - How to test

2. **Database Issues:**
   - Access PhpMyAdmin: http://localhost:8082
   - Server: mysql
   - Username: sail
   - Password: password

3. **View Logs:**
   ```bash
   ./vendor/bin/sail logs
   ```

---

## 🎯 Next Priority Tasks

### For Production Use
1. ⚠️ Assign work schedules to all employees
2. ⚠️ Collect government IDs from all employees
3. ⚠️ Configure regular database backups
4. ⚠️ Train HR staff on new features

### For Development
1. 🔴 Implement BIR tax tables (Critical)
2. 🔴 Add SSS/PhilHealth/Pag-IBIG rate tables (Critical)
3. 🔴 13th month pay module (Critical)
4. 🟡 Biometric integration (High)
5. 🟡 Holiday calendar (High)

---

## 💾 Backup Command
```bash
# Backup database (CRITICAL - Setup daily cron)
./vendor/bin/sail artisan backup:run
```

---

## 📈 System Status

**Version:** 1.0 with Work Schedule System  
**Status:** ✅ Production Ready (with documented limitations)  
**Last Updated:** November 25, 2025  
**Database:** ✅ Migrated and Seeded  
**Documentation:** ✅ Complete  
**Testing:** ✅ Sample data loaded  

---

**For detailed information, see:**
- Full Features: SYSTEM_DOCUMENTATION.md
- Work Schedules: WORK_SCHEDULE_GUIDE.md  
- Testing: TESTING_GUIDE.md
- Implementation: IMPLEMENTATION_SUMMARY.md
