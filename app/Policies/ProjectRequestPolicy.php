<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProjectRequest;

class ProjectRequestPolicy{
    public function view(User $user, ProjectRequest $projectRequest){
        return $user->hasRole('manager') || $projectRequest->client_id === $user->client->id;
    }

    public function create(User $user, ProjectRequest $projectRequest){
        return $user->hasRole('manager') || $projectRequest->client_id === $user->client->id;
    }

    public function update(User $user, ProjectRequest $projectRequest){
        return $user->hasRole('manager') || $projectRequest->client_id === $user->client->id;
    }

    public function delete(User $user, ProjectRequest $projectRequest){
        return $user->hasRole('manager') || $projectRequest->client_id === $user->client->id;
    }
}