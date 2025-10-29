<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWorkLog extends Model
{
    protected $fillable = ['task_id', 'karyawan_id', 'work_date', 'hours'];


    protected $casts = [
        'work_date' => 'date',
        'hours' => 'float',
    ];

    public function task(){
        return $this->belongsTo(Task::class);
    }

    public function karyawan(){
        return $this->belongsTo(Karyawan::class);
    }
}
