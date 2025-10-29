<?php

namespace App\Observers;

use App\MOdels\Task;
class TaskObserver
{
    public function save(Task $task){
        if($task->project){
            $task->project->update(['total_cost' => $task->project->calculateTotalCost()]);
        }
    }

    public function deleted(Task $task){
        if($task->project){
            $task->project->update(['total_cost' => $task->project->calculateTotalCost()]);
        }
    }
}
