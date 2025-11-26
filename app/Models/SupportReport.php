<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportReport extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'subject',
        'message',
        'status',
        'priority',
        'admin_reply',
        'replied_by',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
