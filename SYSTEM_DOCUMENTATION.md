# AccuPay Payroll System - Professional Documentation

## System Overview

AccuPay is a comprehensive payroll management system designed for Philippine businesses, complying with local labor laws and government requirements (SSS, PhilHealth, Pag-IBIG).

**Version:** 1.0  
**Last Updated:** November 25, 2025

---

## Core Features

### ✅ Implemented Features

#### 1. **Employee Management**
- Complete employee master file with personal and employment details
- Work schedule assignment (flexible schedules per employee)
- Government ID tracking (SSS, PhilHealth, Pag-IBIG, TIN)
- Emergency contact information
- Leave credits management (Vacation, Sick, Emergency)
- Employment status tracking (Active, On-leave, Terminated)

#### 2. **Work Schedule System** NEW
- **Pre-defined Schedule Templates:**
  - Standard Mon-Fri (8hrs) - 40 hours/week
  - Standard Mon-Sat (8hrs) - 48 hours/week
  - Night Shift (8hrs) - with night differential
  - Part-Time (4hrs) - 20 hours/week
  - Flexible 3-Day Week - customizable days
  - 12-Hour Shift (3 days) - compressed work week

- **Schedule Features:**
  - Custom working days per employee
  - Configurable shift times (start/end)
  - Break time configuration (paid/unpaid)
  - Grace period for late arrivals
  - Overtime rate multipliers
  - Weekly and daily hour tracking

#### 3. **Attendance Tracking**
- Daily attendance recording (time in/out)
- Automatic hours worked calculation
- Overtime hours tracking
- Status tracking: Present, Absent, Late, On-leave
- Schedule-aware validation (only count scheduled workdays)
- Attendance reports and analytics

#### 4. **Leave Management**
- Multiple leave types (Vacation, Sick, Emergency)
- Leave application workflow (Submit → Pending → Approved/Rejected)
- Annual leave credits allocation per employee
- Automatic leave balance tracking
- Admin approval with remarks
- Leave history and reports

#### 5. **Payroll Processing**
- Payroll period management
- Basic salary computation
- Overtime pay calculation (with custom multipliers)
- Allowances and bonuses
- **Deductions:**
  - SSS (Social Security System)
  - PhilHealth
  - Pag-IBIG
  - Tax withholding
  - Custom deductions
- Gross pay and net pay calculation
- Payroll status tracking

#### 6. **Payslip Generation**
- Detailed payslip per employee per period
- Breakdown of earnings and deductions
- Hours worked and overtime summary
- Government contributions breakdown
- Download/print capability
- Historical payslip access

#### 7. **Reporting & Analytics**
- Employee attendance reports
- Leave utilization reports
- Payroll summary reports
- Filter by date range, department, employee
- Export capabilities
- Statistical dashboards

#### 8. **User Management**
- Role-based access control (Admin, Employee)
- Secure authentication
- Password management
- Auto-linking employee records to user accounts

#### 9. **Employee Self-Service Portal**
- Personal dashboard
- Attendance history view
- Leave application submission
- Payslip access
- Profile management
- Leave balance tracking

---

## Database Structure

### Key Tables

1. **employees** - Master employee data with schedule assignment
2. **work_schedules** - Work schedule templates
3. **attendance** - Daily attendance records
4. **leave_applications** - Leave requests and approvals
5. **payrolls** - Payroll period information
6. **payslips** - Individual employee payslips
7. **users** - System user accounts

### Critical Fields Added

**In `employees` table:**
- `work_schedule_id` - Link to work schedule template
- `tax_id_number` - TIN (Tax Identification Number)
- `sss_number` - Social Security System number
- `philhealth_number` - PhilHealth member number
- `pagibig_number` - Pag-IBIG member number
- `vacation_leave_credits` - Annual vacation days (default: 15)
- `sick_leave_credits` - Annual sick days (default: 15)
- `emergency_leave_credits` - Emergency leave days (default: 5)
- `night_differential_rate` - Additional pay for night shifts
- `holiday_rate_multiplier` - Holiday pay multiplier (default: 200%)

---

## Philippine Labor Law Compliance

### ✅ Implemented Compliance Features

1. **Working Hours**
   - Standard 8-hour workday
   - 40-48 hour work week support
   - Overtime tracking beyond regular hours

2. **Leave Benefits**
   - Service Incentive Leave (SIL) - 15 days minimum
   - Sick Leave tracking
   - Emergency Leave provisions

3. **Government Contributions**
   - SSS deductions
   - PhilHealth deductions
   - Pag-IBIG deductions
   - Tax withholding

4. **Holiday Pay**
   - Configurable holiday rate multipliers
   - Default 200% for regular holidays

5. **Night Differential**
   - Night shift rate tracking (10pm - 6am)
   - Additional compensation for night work

---

## Missing Features & Limitations

### ⚠️ Current System Gaps

#### 1. **Biometric/RFID Integration**
- **Status:** Not Implemented
- **Impact:** HIGH
- **Description:** No automatic attendance capture via fingerprint/RFID devices
- **Current Workaround:** Manual entry by admin through web interface
- **Recommendation:** Integrate with biometric devices (ZKTeco, Anviz, etc.)

#### 2. **Tax Computation Engine**
- **Status:** Placeholder Only
- **Impact:** CRITICAL
- **Description:** BIR tax tables not implemented (2023 TRAIN Law)
- **Current State:** Tax field exists but calculation is manual/zero
- **Required:** 
  - Implement graduated tax rates
  - 13th month pay tax exemption (₱90,000)
  - De minimis benefits consideration
  - Tax tables for 2025
- **Recommendation:** Implement Philippine BIR tax calculation based on employee's taxable income

#### 3. **Holiday Calendar Management**
- **Status:** Not Implemented
- **Impact:** MEDIUM
- **Description:** No system to manage Philippine holidays (regular and special)
- **Affects:** 
  - Holiday pay calculations
  - Work schedule adjustments
  - Attendance validation
- **Recommendation:** Create holidays table with regular/special classification

#### 4. **13th Month Pay Computation**
- **Status:** Not Implemented
- **Impact:** HIGH (Required by law)
- **Description:** Mandatory 13th month pay calculation not automated
- **Legal Requirement:** Must be paid by December 24
- **Calculation:** (Total basic salary for the year) ÷ 12
- **Recommendation:** Add 13th month pay module with pro-rated calculations

#### 5. **SSS/PhilHealth/Pag-IBIG Contribution Tables**
- **Status:** Placeholder Only
- **Impact:** CRITICAL
- **Description:** Government contribution tables not implemented
- **Current State:** Fixed amounts, not based on actual salary brackets
- **Required:**
  - SSS contribution table (2025 rates)
  - PhilHealth contribution table (4% of basic salary, max ₱5,000)
  - Pag-IBIG contribution (employee: 2%, employer: 2%, max ₱200)
- **Recommendation:** Implement dynamic calculation based on official tables

#### 6. **Loan Management**
- **Status:** Not Implemented
- **Impact:** MEDIUM
- **Description:** No employee loan tracking (SSS loans, company loans, cash advances)
- **Recommendation:** Create loans module with amortization schedules

#### 7. **Tardiness/Undertime Computation**
- **Status:** Partial
- **Impact:** MEDIUM
- **Description:** Late status tracked but no automatic salary deduction
- **Current:** Grace period exists in schedule, but deduction not computed
- **Recommendation:** Add tardiness/undertime deduction calculator

#### 8. **Shift Differential Automation**
- **Status:** Manual
- **Impact:** LOW
- **Description:** Night differential field exists but not auto-calculated
- **Recommendation:** Auto-apply night differential based on shift hours (10pm-6am)

#### 9. **Payroll Audit Trail**
- **Status:** Not Implemented
- **Impact:** MEDIUM
- **Description:** No detailed log of payroll changes/approvals
- **Recommendation:** Add audit logging for compliance and accountability

#### 10. **Bank Integration/Direct Deposit**
- **Status:** Not Implemented
- **Impact:** LOW (Nice to have)
- **Description:** No automatic bank file generation for salary deposit
- **Recommendation:** Generate PESONET/InstaPay batch files

#### 11. **Email Notifications**
- **Status:** Not Implemented
- **Impact:** LOW
- **Description:** No email notifications for:
  - Payslip availability
  - Leave approval/rejection
  - Attendance reminders
- **Recommendation:** Configure Laravel mail queue system

#### 12. **Advanced Reporting**
- **Status:** Basic Implementation
- **Impact:** MEDIUM
- **Missing Reports:**
  - BIR Alphalist (Annual report of employees)
  - SSS R3 (Monthly remittance report)
  - PhilHealth Monthly Report
  - Pag-IBIG Monthly Contribution Report
  - DOLE compliance reports
- **Recommendation:** Add government-required report templates

#### 13. **Payroll Approval Workflow**
- **Status:** Not Implemented
- **Impact:** MEDIUM
- **Description:** No multi-level approval (Prepared → Reviewed → Approved)
- **Recommendation:** Add approval workflow with reviewer roles

#### 14. **Employee Benefits Module**
- **Status:** Not Implemented
- **Impact:** MEDIUM
- **Description:** No tracking for:
  - HMO/Health insurance
  - Rice subsidy
  - Transportation allowance
  - Meal allowance
  - De minimis benefits
- **Recommendation:** Create benefits configuration module

#### 15. **Overtime Pre-Approval**
- **Status:** Not Implemented
- **Impact:** LOW
- **Description:** Overtime auto-calculated but no approval workflow
- **Recommendation:** Add OT request/approval before payroll processing

---

## Security Considerations

### ✅ Implemented
- Password hashing (bcrypt)
- CSRF protection
- Role-based access control
- SQL injection protection (Eloquent ORM)

### ⚠️ Missing
- Two-factor authentication (2FA)
- Session timeout settings
- IP whitelisting for admin
- Detailed activity logging
- Data encryption at rest
- Regular security audits

---

## Recommended Immediate Improvements

### Priority 1 (Critical - Legal Compliance)
1. **Implement BIR Tax Tables** - Required for accurate withholding tax
2. **SSS/PhilHealth/Pag-IBIG Contribution Tables** - Legal requirement
3. **13th Month Pay Module** - Mandatory by December 24

### Priority 2 (High - System Functionality)
4. **Biometric Integration** - Eliminate manual attendance entry
5. **Holiday Calendar** - Proper holiday pay calculation
6. **Loan Management** - Employee convenience and tracking

### Priority 3 (Medium - Operational Efficiency)
7. **Government Compliance Reports** - BIR Alphalist, SSS R3, etc.
8. **Payroll Approval Workflow** - Better control and accountability
9. **Email Notifications** - Improved communication

### Priority 4 (Low - Enhancement)
10. **Bank Integration** - Automated salary disbursement
11. **Advanced Analytics** - Business intelligence dashboards
12. **Mobile App** - Employee self-service on mobile

---

## System Capabilities

### What the System CAN Do:
✅ Manage employee master data with detailed information  
✅ Assign flexible work schedules to employees  
✅ Track daily attendance with time in/out  
✅ Process leave applications and approvals  
✅ Generate payroll with basic calculations  
✅ Create payslips with earnings and deduction breakdown  
✅ Track government IDs and contributions  
✅ Manage leave credits automatically  
✅ Provide employee self-service portal  
✅ Generate basic reports and analytics  

### What the System CANNOT Do Yet:
❌ Automatically capture attendance from biometric devices  
❌ Calculate correct BIR withholding tax  
❌ Apply dynamic SSS/PhilHealth/Pag-IBIG rates  
❌ Process 13th month pay automatically  
❌ Manage employee loans and deductions  
❌ Generate government compliance reports (BIR Alphalist, SSS R3)  
❌ Handle holiday calendar and special pay rates  
❌ Send email notifications  
❌ Create bank deposit files  
❌ Track shift differentials automatically  

---

## Support and Maintenance

### Recommended Maintenance Schedule:
- **Monthly:** Update government contribution tables (if rates change)
- **Quarterly:** Review tax tables for BIR updates
- **Annually:** Update holiday calendar
- **As needed:** Add new employees, update schedules

---

## Technical Stack

- **Framework:** Laravel 12.39.0
- **PHP:** 8.4.15
- **Database:** MySQL 8.0
- **Frontend:** Blade Templates, CSS, JavaScript
- **Server:** Laravel Sail (Docker)

