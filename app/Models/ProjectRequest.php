<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRequest extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'client_id',
    //     'tiket',
    //     'kategori',
    //     'name_project',
    //     'description',
    //     'document',
    //     'status',
    //     'note',
    // ];

    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // app/Models/ProjectRequest.php
public function projects()
{
    return $this->hasMany(Project::class, 'request_id');
}

}
