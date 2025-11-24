<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Karyawan extends Model
{
    //
    use HasFactory;

    /**
     * Summary of guarded
     * semua kolom bisa diisi
     * @var array
     */
    protected $guarded = [];

    /**
     * Summary of user
     * relasi one to one
     * client dgn karyawan
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Karyawan>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of projects
     * relasi many to many
     * karyawan dgn project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Project, Karyawan, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'karyawan_projects', 'karyawan_id', 'project_id')
        // simpan cost dan job_title snapshot agar tidak berubah kalo cost dan job_title karyawan diupdate atau berubah
            ->withPivot('cost_snapshot', 'job_title_snapshot')
            // otomatis isi created_at dan update_at utk pivot table
            ->withTimestamps();
    }

    /**
     * Summary of tasks
     * relasi one to many
     * karyawan dgn task
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Task, Karyawan>
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
