<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'category',
        'estimated_pomodoros',
        'completed_pomodoros',
        'due_date',
        'completed_at',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'due_date' => 'date:Y-m-d',
        'completed_at' => 'datetime',
        'estimated_pomodoros' => 'integer',
        'completed_pomodoros' => 'integer',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('due_date', Carbon::today())
            ->orWhereNull('due_date')
            ->orWhere('status', 'in_progress');
    }

    public function scopeTodo($query)
    {
        return $query->where('status', 'todo');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    public function markAsCompleted(): self
    {
        $this->update([
            'status' => 'done',
            'completed_at' => Carbon::now(),
        ]);
        return $this;
    }
}
