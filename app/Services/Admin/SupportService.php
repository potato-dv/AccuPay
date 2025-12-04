<?php

namespace App\Services\Admin;

use App\Models\SupportReport;
use Illuminate\Support\Facades\Auth;

class SupportService
{
    /**
     * Get filtered support reports
     */
    public function getFilteredReports(?string $status, ?string $type, ?string $search)
    {
        $query = SupportReport::with(['employee', 'repliedBy'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('first_name', 'like', '%' . $search . '%')
                           ->orWhere('last_name', 'like', '%' . $search . '%')
                           ->orWhere('employee_id', 'like', '%' . $search . '%');
                    });
            });
        }

        return $query->get();
    }

    /**
     * Reply to support report
     */
    public function replyToReport(int $reportId, string $reply): void
    {
        $report = SupportReport::findOrFail($reportId);
        
        $report->update([
            'admin_reply' => $reply,
            'replied_by' => Auth::id(),
            'replied_at' => now(),
            'status' => 'in-progress',
        ]);
    }

    /**
     * Update support report status
     */
    public function updateStatus(int $reportId, string $status): void
    {
        $report = SupportReport::findOrFail($reportId);
        $report->update(['status' => $status]);
    }
}
