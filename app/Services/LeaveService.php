<?php

namespace App\Services;

use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Auth;

class LeaveService
{
    /**
     * Get filtered leave applications
     */
    public function getFilteredLeaves(?string $status, ?string $leaveType)
    {
        $query = LeaveApplication::with('employee')->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($leaveType) {
            $query->where('leave_type', $leaveType);
        }

        return $query->get();
    }

    /**
     * Approve leave application
     */
    public function approveLeave(int $leaveId): void
    {
        $leave = LeaveApplication::findOrFail($leaveId);
        
        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject leave application
     */
    public function rejectLeave(int $leaveId, string $remarks): void
    {
        $leave = LeaveApplication::findOrFail($leaveId);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_remarks' => $remarks,
        ]);
    }
}
