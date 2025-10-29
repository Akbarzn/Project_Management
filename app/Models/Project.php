<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'request_id',
        'start_date_project',
        'finish_date_project',
        'status',
        'created_by',
        'approved_by',
        'is_approved',
        'total_cost',
    ];

    // Relasi ke client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relasi ke project request
    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class, 'request_id');
    }

    // Relasi ke user (manager) yang membuat project
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke user (manager) yang menyetujui project
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke karyawan yang ditugaskan
    public function karyawans()
{
    return $this->belongsToMany(Karyawan::class, 'karyawan_project','project_id','karyawan_id');
}

public function tasks(){
    return $this->hasMany(Task::class, 'project_id');
}

public function calculateTotalCost():float{
     return (float) $this->tasks()
        ->with(['karyawan', 'workLogs'])
        ->get()
        ->sum(fn($task) => $task->calculateCost());
}

public function recalculateTotalCost()
{
    $total = $this->tasks()
        ->with(['workLogs.karyawan'])
        ->get()
        ->sum(function ($task) {
            return $task->workLogs->sum(function ($log) {
                $rate = $log->karyawan->cost ?? 0;
                return $log->hours * $rate;
            });
        });

    $this->update(['total_cost' => $total]);
    return $total;
}

      // 🧠 Accessor durasi hari kerja (dibulatkan)
    public function getDurationDaysAttribute()
    {
        if (!$this->start_date_task) return 0;

        $start = Carbon::parse($this->start_date_task);
        $end = $this->finish_date_task ? Carbon::parse($this->finish_date_task) : now();

        $days = ceil($start->diffInHours($end) / 24);
        return max(1, $days);
    }

    // 🧠 Accessor jam kerja
    public function getWorkHoursAttribute()
    {
        return $this->duration_days * 7;
    }

    public function updateStatus(){
        $endProject = $this->tasks()->where('status', '!=', 'complete')->count() === 0;
        $today = now()->toDateString();

        if($endProject){
            $this->update([
                'status' => 'complete',
                'finish_date_project' => now(),
            ]);
        }elseif($this->finish_date_project && $today > $this->finish_date_project){
            $this->update(['status' => 'overdue']);
        }else{
            $this->update(['status' => 'ongoing']);
        }
    }

}
