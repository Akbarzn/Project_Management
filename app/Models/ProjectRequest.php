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

    /**
     * Summary of client
     * relasi many to one
     * client dengan projectRequest
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Client, ProjectRequest>
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

/**
 * Summary of projects
 * relasi one to many
 * projectrequest dgn project
 * @return \Illuminate\Database\Eloquent\Relations\HasMany<Project, ProjectRequest>
 */
public function projects()
{
    return $this->hasMany(Project::class, 'request_id');
}

}
