<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $guarded = [];

    /**
     * Summary of user
     * relasi one to one 
     * user dgn client
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Client>
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of projects
     * relasi one to many
     * client dgn project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Project, Client>
     */
    public function projects(){
        return $this->hasMany(Project::class);
    }
}
