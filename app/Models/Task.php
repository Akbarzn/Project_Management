<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Task extends Model
{
    
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }

    public function workLogs(){
        return $this->hasMany(TaskWorkLog::class);
    }

    public function calculateCost():float{
        $totalHours = $this->workLogs()->sum('hours');
    $hourlyRate = $this->karyawan?->cost_per_hour ?? 0;

    return $totalHours * $hourlyRate;
    }

    // // accesor
    // public function getTotalWorkHours(){
    //     return(float) $this->workLogs()->sum('hours');
    // }

    // public function getTotalCost(){
    //     if(!$this->karyawan) return 0;
    //     return (float) ceil($this->total_worked_hours * (float) $this->karyawan->cost);
    // }

    // // helpers
    // public function updateStatus(){
    //     if($this->progerss >= 100){
    //         $this->status = 'complete';
    //         $this->saveQuietly();
    //         return;
    //     }

    //     $today = now()->toDateString();
    //     $hasWorkToDay = $this->workLogs()->whereDate('work_date', $today)->exists();
    //     $this->status = $hasWorkToDay ? 'inwork' : 'pending';
    //     $this->saveQuietly();
    // }

    //   // 🧠 Accessor durasi hari kerja (dibulatkan)
    // public function getDurationDaysAttribute()
    // {
    //     if (!$this->start_date_task) return 0;

    //     $start = Carbon::parse($this->start_date_task);
    //     $end = $this->finish_date_task ? Carbon::parse($this->finish_date_task) : now();

    //     $days = ceil($start->diffInHours($end) );
    //     return max(1, ceil($start->floatDiffInDays($end)));
    //     // return max(1, $days);
    // }

    // // 🧠 Accessor jam kerja
    // public function getWorkHoursAttribute()
    // {
    //     return $this->duration_days * 7;
    // }

    // // 🧠 Accessor total biaya task
    // public function getTotalCostAttribute()
    // {
    //     if (!$this->karyawan) return 0;
    //     return ceil($this->work_hours * (float) $this->karyawan->cost);
    // }

    // protected static function booted()
    // {
    //     static::updating(function ($task) {
    //         $dirty = $task->getDirty();

    //         $loggableFields = ['progress', 'description_task', 'start_date_task', 'finish_date_task'];

    //         foreach ($dirty as $field => $newValue) {

    //             if (!in_array($field, $loggableFields)) {
    //                 continue;
    //             }

    //             $oldValue = $task->getOriginal($field);

    //             $oldValueString = (string) ($oldValue ?? '');
    //             $newValueString = (string) ($newValue ?? '');

    //             if ($oldValueString === $newValueString) {
    //                 continue;
    //             }

    //             \App\Models\TaskLog::create([
    //                 'task_id' => $task->id,
    //                 'field' => $field,
    //                 'old_value' => $oldValueString,
    //                 'new_value' => $newValueString,
    //                 'updated_by' => auth()->id(),
    //             ]);
    //         }
    //     });

    //     static::saved(function ($task) {
    //         $project = $task->project;
    //         if ($project) {
    //             $project->update([
    //                 'total_cost' => $project->calculateTotalCost(),
    //             ]);
    //         }
    //     });

    //     static::deleted(function ($task) {
    //         $project = $task->project;
    //         if ($project) {
    //             $project->update([
    //                 'total_cost' => $project->calculateTotalCost(),
    //             ]);
    //         }
    //     });
    // }

    
}