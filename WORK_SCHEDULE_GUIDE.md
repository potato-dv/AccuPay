# Work Schedule System - Quick Reference Guide

## Overview

The AccuPay system now includes a professional **Work Schedule Management** feature that allows each employee to have their own customized working days and shift hours. This ensures accurate attendance tracking and payroll calculations based on actual scheduled workdays.

---

## Pre-Configured Work Schedules

### 1. Standard Mon-Fri (8hrs)
- **Working Days:** Monday to Friday
- **Shift Hours:** 8:00 AM - 5:00 PM
- **Daily Hours:** 8 hours
- **Weekly Hours:** 40 hours
- **Break:** 12:00 PM - 1:00 PM (unpaid)
- **Overtime:** Allowed (1.25x rate)
- **Grace Period:** 15 minutes
- **Best For:** Regular office employees, standard 5-day work week

### 2. Standard Mon-Sat (8hrs) ⭐
- **Working Days:** Monday to Saturday
- **Shift Hours:** 8:00 AM - 5:00 PM
- **Daily Hours:** 8 hours
- **Weekly Hours:** 48 hours
- **Break:** 12:00 PM - 1:00 PM (unpaid)
- **Overtime:** Allowed (1.25x rate)
- **Grace Period:** 15 minutes
- **Best For:** Retail, manufacturing, service industries

### 3. Night Shift (8hrs)
- **Working Days:** Monday to Friday
- **Shift Hours:** 10:00 PM - 6:00 AM
- **Daily Hours:** 8 hours
- **Weekly Hours:** 40 hours
- **Break:** 2:00 AM - 3:00 AM (unpaid)
- **Overtime:** Allowed (1.30x rate)
- **Grace Period:** 10 minutes
- **Best For:** Night shift workers, BPO employees, security
- **Note:** Eligible for night differential pay

### 4. Part-Time (4hrs)
- **Working Days:** Monday to Friday
- **Shift Hours:** 9:00 AM - 1:00 PM
- **Daily Hours:** 4 hours
- **Weekly Hours:** 20 hours
- **Break:** None
- **Overtime:** Not allowed
- **Grace Period:** 10 minutes
- **Best For:** Part-time staff, students, contractual workers

### 5. Flexible 3-Day Week
- **Working Days:** Monday, Wednesday, Saturday
- **Shift Hours:** 8:00 AM - 5:00 PM
- **Daily Hours:** 8 hours
- **Weekly Hours:** 24 hours
- **Break:** 12:00 PM - 1:00 PM (unpaid)
- **Overtime:** Allowed (1.25x rate)
- **Grace Period:** 15 minutes
- **Best For:** Flexible arrangements, project-based workers

### 6. 12-Hour Shift (3 days)
- **Working Days:** Tuesday, Thursday, Saturday
- **Shift Hours:** 7:00 AM - 7:00 PM
- **Daily Hours:** 12 hours
- **Weekly Hours:** 36 hours
- **Break:** 12:00 PM - 1:00 PM (PAID)
- **Overtime:** Allowed (1.50x rate)
- **Grace Period:** 20 minutes
- **Best For:** Compressed work week, healthcare, security

---

## How to Assign Work Schedules

### For New Employees

1. Go to **Admin Panel** → **Employee List** → **Add Employee**
2. Fill in employee information
3. In the **Work Schedule** dropdown, select the appropriate schedule
4. The dropdown shows: `Schedule Name - Working Days (Weekly Hours)`
5. Save the employee

### For Existing Employees

1. Go to **Admin Panel** → **Employee List**
2. Click **Edit** on the employee
3. Update the **Work Schedule** field
4. The current schedule is shown below the dropdown
5. Save changes

### View Assigned Schedule

**Employee Side:**
- Login to employee portal
- Go to **Profile**
- Work schedule details are displayed in the profile header:
  - Schedule name
  - Shift hours
  - Working days
  - Weekly hours

**Admin Side:**
- **Employee List** table shows work schedule in a dedicated column
- Hover over schedule name to see shift hours

---

## How Work Schedules Affect Payroll

### Attendance Validation
- **Scheduled Workdays Only:** System only expects attendance on scheduled working days
- **Rest Days:** No attendance required on non-working days
- **Absence Detection:** Missing attendance on scheduled days = absent

### Hours Calculation
- **Regular Hours:** Based on `daily_hours` setting (e.g., 8 hours)
- **Overtime:** Hours beyond `daily_hours` on scheduled workdays
- **Overtime Rate:** Multiplied by `overtime_rate_multiplier` (e.g., 1.25x = 125% of hourly rate)

### Late Arrival
- **Grace Period:** Configurable per schedule (0-20 minutes)
- **Within Grace Period:** Counted as on-time
- **Beyond Grace Period:** Marked as "Late" status
- **Future Enhancement:** Automatic tardiness deduction (not yet implemented)

### Example Calculation

**Employee:** Juan Dela Cruz  
**Schedule:** Standard Mon-Sat (8hrs)  
**Basic Salary:** ₱25,000/month  
**Hourly Rate:** ₱150/hr  

**Attendance in November (26 days):**
- Scheduled workdays: 22 days (Mon-Sat, excluding Sundays)
- Actual present: 21 days
- Absent: 1 day
- Overtime: 5 hours total

**Payroll Calculation:**
- Regular pay: ₱25,000 (basic salary)
- Overtime pay: 5 hrs × ₱150 × 1.25 = ₱937.50
- Gross pay: ₱25,937.50
- Deductions: SSS + PhilHealth + Pag-IBIG + Tax
- Net pay: (After deductions)

---

## Creating Custom Work Schedules

### Via Database Seeder (Recommended for Admins)

1. Edit `database/seeders/WorkScheduleSeeder.php`
2. Add new schedule array:
```php
[
    'schedule_name' => 'Custom Schedule Name',
    'description' => 'Description of this schedule',
    'monday' => true,    // true = working day
    'tuesday' => true,
    'wednesday' => false, // false = rest day
    'thursday' => true,
    'friday' => true,
    'saturday' => false,
    'sunday' => false,
    'shift_start' => '09:00:00',
    'shift_end' => '18:00:00',
    'daily_hours' => 8.00,
    'weekly_hours' => 32.00, // 8 hrs × 4 days
    'break_start' => '12:00:00',
    'break_end' => '13:00:00',
    'break_paid' => false,
    'overtime_allowed' => true,
    'overtime_rate_multiplier' => 1.25,
    'grace_period_minutes' => 15,
    'is_active' => true,
],
```
3. Run: `./vendor/bin/sail artisan db:seed --class=WorkScheduleSeeder`

### Via Tinker (Quick Method)

```bash
./vendor/bin/sail artisan tinker
```

```php
use App\Models\WorkSchedule;

WorkSchedule::create([
    'schedule_name' => 'Morning Shift',
    'description' => 'Early morning shift 6am-2pm',
    'monday' => true,
    'tuesday' => true,
    'wednesday' => true,
    'thursday' => true,
    'friday' => true,
    'saturday' => false,
    'sunday' => false,
    'shift_start' => '06:00:00',
    'shift_end' => '14:00:00',
    'daily_hours' => 8.00,
    'weekly_hours' => 40.00,
    'grace_period_minutes' => 10,
    'is_active' => true,
]);
```

---

## Work Schedule Fields Explained

| Field | Type | Description | Example |
|-------|------|-------------|---------|
| `schedule_name` | String | Unique name for the schedule | "Standard Mon-Fri" |
| `description` | String | Detailed description | "Regular 5-day work week" |
| `monday` - `sunday` | Boolean | Working day flags | `true` = work, `false` = rest |
| `shift_start` | Time | Start of shift | "08:00:00" |
| `shift_end` | Time | End of shift | "17:00:00" |
| `daily_hours` | Decimal | Expected hours per day | 8.00 |
| `weekly_hours` | Decimal | Total hours per week | 40.00 |
| `break_start` | Time | Break start time | "12:00:00" |
| `break_end` | Time | Break end time | "13:00:00" |
| `break_paid` | Boolean | Is break paid? | `false` |
| `overtime_allowed` | Boolean | Can work overtime? | `true` |
| `overtime_rate_multiplier` | Decimal | OT rate multiplier | 1.25 (125%) |
| `grace_period_minutes` | Integer | Late grace period | 15 minutes |
| `is_active` | Boolean | Schedule available? | `true` |

---

## Employee Leave Credits

Each employee has configurable annual leave credits:

### Default Allocations
- **Vacation Leave:** 15 days per year
- **Sick Leave:** 15 days per year
- **Emergency Leave:** 5 days per year

### How to Set Custom Leave Credits

**When Adding Employee:**
- Fields are pre-filled with defaults (15, 15, 5)
- Modify as needed before saving

**When Editing Employee:**
- Update the leave credit fields
- Changes apply immediately

**Leave Balance Tracking:**
- System automatically deducts approved leaves from credits
- Remaining balance shown in employee dashboard
- Formula: `Total Credits - Approved Leaves (current year)`

---

## Government IDs and Contributions

### Required IDs (For Philippine Compliance)

1. **TIN (Tax Identification Number)**
   - Format: `123-456-789-000`
   - Required for tax withholding
   - Must be unique per employee

2. **SSS Number (Social Security System)**
   - Format: `12-3456789-0`
   - For SSS contributions and benefits

3. **PhilHealth Number**
   - Format: `12-345678901-2`
   - For PhilHealth contributions

4. **Pag-IBIG Number**
   - Format: `1234-5678-9012`
   - For Pag-IBIG Fund contributions

### Where to Enter
- **Add Employee** form has dedicated fields for all government IDs
- **Edit Employee** form allows updating IDs
- All fields are optional but recommended for compliance

---

## Best Practices

### 1. Schedule Selection
✅ Match schedule to actual working requirements  
✅ Consider overtime needs when selecting multiplier  
✅ Set appropriate grace periods (service industry: 5-10min, office: 15min)  
✅ Night shifts should use Night Shift schedule for proper differential tracking  

### 2. Leave Credits
✅ Adjust based on company policy and tenure  
✅ Pro-rate credits for mid-year hires  
✅ Review annually and reset for new year  
✅ Track unused credits for potential payouts  

### 3. Government IDs
✅ Collect all IDs during onboarding  
✅ Verify formats with government agencies  
✅ Update immediately if employee reports changes  
✅ Required for accurate contribution calculations  

### 4. Maintenance
✅ Review schedules quarterly for effectiveness  
✅ Create new schedules for new shift patterns  
✅ Mark inactive schedules rather than deleting  
✅ Document custom schedules and their purposes  

---

## Troubleshooting

### Employee Not Showing Expected Attendance
**Problem:** Employee marked absent on a rest day  
**Solution:** Check if they're on the correct work schedule. Their schedule may not include that day as a working day.

### Overtime Not Calculating
**Problem:** Overtime hours showing as 0  
**Solution:** Check if schedule has `overtime_allowed = true` and employee worked beyond `daily_hours`.

### Cannot Assign Schedule
**Problem:** Schedule doesn't appear in dropdown  
**Solution:** Ensure schedule has `is_active = true` in database.

### Wrong Weekly Hours
**Problem:** Weekly hours don't match working days  
**Solution:** Manually calculate: `working_days_count × daily_hours` and update `weekly_hours` field.

---

## Future Enhancements

The following features are planned for future releases:

1. **Rotating Shifts** - Support for weekly rotating schedule patterns
2. **Shift Swapping** - Allow employees to swap shifts with approval
3. **Schedule Templates** - Clone and modify existing schedules
4. **Bulk Assignment** - Assign schedules to multiple employees at once
5. **Schedule History** - Track schedule changes over time
6. **Split Shifts** - Support for schedules with multiple shift periods in one day
7. **Flexible Hours** - Core hours + flexible start/end times

---

## Support

For questions about work schedules:
- Check `SYSTEM_DOCUMENTATION.md` for overall system capabilities
- Review this guide for schedule-specific questions
- Contact your system administrator for custom schedule creation

**Last Updated:** November 25, 2025  
**Version:** 1.0
