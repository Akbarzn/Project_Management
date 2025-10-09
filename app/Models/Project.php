<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    return $this->belongsToMany(Karyawan::class, 'karyawan_project');
}

}
