<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class ProjectCostLivewire extends Component
{

    public $project;
    public $savedCost;
    public $realtimeCost;

    public function mount(Project $project){
        $this->project = $project;
        // $this->updateCosts();
        $this->realtimeCost = $project->calculateTotalCost();
    }

    public function refreshCosts(){
        // ambil nilai dari db
        // $this->savedCost = $this->project->total_cost;

        // hitung realtime
        $this->realtimeCost = $this->project->calculateTotalCost();
        $this->project->refresh();
    }
    public function render()
    {
        return view('livewire.project-cost-livewire');
    }
}
