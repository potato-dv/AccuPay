<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrAttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Generate QR code for employee
     */
    public function generateQrCode()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Generate or retrieve QR token
        if (!$employee->qr_token) {
            $employee->qr_token = Str::random(32) . '-' . $employee->id;
            $employee->save();
        }

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($employee->qr_token);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Show QR scanner page
     */
    public function showScanner()
    {
        return view('qr-attendance.scanner');
    }

    /**
     * Process QR code scan for check-in/check-out
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
            'action' => 'required|in:check-in,check-out'
        ]);

        // Find employee by QR token
        $employee = Employee::where('qr_token', $request->qr_token)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Employee not found.'
            ], 404);
        }

        $today = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');

        // Find or create today's attendance record
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($request->action === 'check-in') {
            if ($attendance && $attendance->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked in today at ' . Carbon::parse($attendance->time_in)->format('h:i A')
                ]);
            }

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->employee_id = $employee->id;
                $attendance->date = $today;
            }

            $attendance->time_in = $currentTime;
            $attendance->status = 'present';
            
            // Check if employee is late based on work schedule
            $isLate = false;
            $minutesLate = 0;
            
            if ($employee->workSchedule && $employee->workSchedule->shift_start) {
                try {
                    // Parse shift start time for today
                    $shiftStart = Carbon::parse($today . ' ' . $employee->workSchedule->shift_start);
                    $gracePeriod = $employee->workSchedule->grace_period_minutes ?? 0;
                    $lateThreshold = $shiftStart->copy()->addMinutes($gracePeriod);
                    $checkInTime = Carbon::parse($today . ' ' . $currentTime);
                    
                    if ($checkInTime->greaterThan($lateThreshold)) {
                        // Employee is late
                        $isLate = true;
                        $minutesLate = $checkInTime->diffInMinutes($shiftStart);
                        $attendance->status = 'late';
                        $attendance->remarks = 'Late by ' . $minutesLate . ' minutes';
                    } elseif ($checkInTime->lessThan($shiftStart)) {
                        // Employee arrived early
                        $minutesEarly = $shiftStart->diffInMinutes($checkInTime);
                        $attendance->status = 'present';
                        $attendance->remarks = 'Arrived ' . $minutesEarly . ' minutes early';
                    } else {
                        // On time (within grace period)
                        $attendance->status = 'present';
                        $attendance->remarks = 'On time';
                    }
                } catch (\Exception $e) {
                    // If there's an error parsing times, default to present
                    $isLate = false;
                    $minutesLate = 0;
                    $attendance->status = 'present';
                }
            }
            
            $attendance->save();

            $statusMessage = $isLate 
                ? 'Check-in successful (Late by ' . $minutesLate . ' minutes)' 
                : 'Check-in successful!';
            
            // Add early arrival message if applicable
            if (!$isLate && $attendance->remarks && strpos($attendance->remarks, 'early') !== false) {
                $statusMessage = 'Check-in successful (' . $attendance->remarks . ')';
            }

            return response()->json([
                'success' => true,
                'message' => $statusMessage,
                'employee' => [
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name
                ],
                'time' => Carbon::parse($currentTime)->format('h:i A'),
                'status' => $attendance->status,
                'is_late' => $isLate,
                'minutes_late' => $minutesLate,
                'remarks' => $attendance->remarks,
                'action' => 'check-in'
            ]);
        } else {
            // Check-out
            if (!$attendance || !$attendance->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check in first before checking out.'
                ]);
            }

            if ($attendance->time_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked out today at ' . Carbon::parse($attendance->time_out)->format('h:i A')
                ]);
            }

            $attendance->time_out = $currentTime;

            // Calculate hours worked
            try {
                $result = $this->attendanceService->calculateHoursWorked(
                    $today,
                    $attendance->time_in,
                    $currentTime,
                    $employee
                );

                $attendance->hours_worked = $result['hours_worked'];
                $attendance->overtime_hours = $result['overtime_hours'];
            } catch (\Exception $e) {
                // If calculation fails, just set to 0
                $attendance->hours_worked = 0;
                $attendance->overtime_hours = 0;
            }
            
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Check-out successful!',
                'employee' => [
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name
                ],
                'time' => Carbon::parse($currentTime)->format('h:i A'),
                'status' => $attendance->status,
                'hours_worked' => number_format($attendance->hours_worked ?? 0, 2),
                'action' => 'check-out'
            ]);
        }
    }

    /**
     * Get employee QR code info
     */
    public function getEmployeeQr()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Check today's attendance status
        $today = Carbon::now()->format('Y-m-d');
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        return response()->json([
            'employee_name' => $employee->full_name,
            'employee_id' => $employee->employee_id,
            'has_checked_in' => $attendance && $attendance->time_in ? true : false,
            'has_checked_out' => $attendance && $attendance->time_out ? true : false,
            'check_in_time' => $attendance && $attendance->time_in ? Carbon::parse($attendance->time_in)->format('h:i A') : null,
            'check_out_time' => $attendance && $attendance->time_out ? Carbon::parse($attendance->time_out)->format('h:i A') : null,
        ]);
    }

    /**
     * Show employee QR code page
     */
    public function showEmployeeQrPage()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee) {
            return redirect()->route('employee.dashboard')->with('error', 'Employee not found');
        }

        return view('employee.qr-code', compact('employee'));
    }
}
