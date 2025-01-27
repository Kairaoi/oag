<?php

namespace App\Models\OAG\Legal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LegalTask extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'legal_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'task',
        'ministry',
        'legal_advice_meeting',
        'allocated_date',
        'allocated_to',
        'status',
        'onward_action',
        'date_task_achieved',
        'date_approved_by_ag',
        'meeting_date',
        'time_frame',
        'notes',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'allocated_date' => 'date',
        'date_task_achieved' => 'date',
        'date_approved_by_ag' => 'date',
        'meeting_date' => 'date',
    ];

    /**
     * Get the user who created the task.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the task.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
