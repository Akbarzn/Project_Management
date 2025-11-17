<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Karyawan extends Model
{
    //
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function projects()
{
    return $this->belongsToMany(Project::class, 'karyawan_projects','karyawan_id','project_id')
    ->withPivot('cost_snapshot')
        ->withTimestamps();
}

public function tasks(){
    return $this->hasMany(Task::class);
}
}
