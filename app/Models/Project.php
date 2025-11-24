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
        'approved_by',
        'is_approved',
        'total_cost',
    ];

    /**
     * Summary of client
     * relasi one to many
     * client dengan project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Client, Project>
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Summary of projectRequest
     * relasi one to one 
     * project dengan projectRequest
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ProjectRequest, Project>
     */
    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class, 'request_id');
    }

    /**
     * Summary of creator
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Project>
     */
    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    /**
     * Summary of approver
     * manger bisa approve atau menyetujui project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Project>
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Summary of karyawans
     * relasi many to many
     * karyawan dgn project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Karyawan, Project, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function karyawans()
{
    return $this->belongsToMany(Karyawan::class, 'karyawan_projects','project_id','karyawan_id')
    ->withPivot('cost_snapshot', 'job_title_snapshot')
    ->withTimestamps();
}

/**
 * Summary of tasks
 * relasi one to many
 * tasks dengan project
 * @return \Illuminate\Database\Eloquent\Relations\HasMany<Task, Project>
 */
public function tasks(){
    return $this->hasMany(Task::class, 'project_id');
}

/**
 * Summary of getKaryawanCost
 * fungsi untuk hitung biaya
 * ambil cost karyawan berdasarkan pivot snapshot
 * dan untuk hitung total cost
 * @param mixed $karyawanId
 */
public function getKaryawanCost($karyawanId)
{
    $karyawan = $this->karyawans()->where('karyawan_id', $karyawanId)->first();
    return $karyawan ? $karyawan->pivot->cost_snapshot : 0;
}

}
