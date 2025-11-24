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

}