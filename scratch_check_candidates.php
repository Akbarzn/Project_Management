<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = \App\Models\Project::findOrFail(2);
$service = app()->make(\App\Services\ProjectAssignmentService::class);
$workloadService = app()->make(\App\Services\WorkloadService::class);

$candidates = $service->getSuggestedCandidates($project);

echo "Candidates returned by getSuggestedCandidates, sorted by workload:\n";
foreach ($candidates as $k) {
    $workload = $workloadService->calculateWorkload($k);
    $hasSkill = $k->hasSkill('Laravel');
    echo "- ID: {$k->id}, Name: {$k->name}, Workload: {$workload}, Has Laravel Skill: " . ($hasSkill ? 'YES' : 'NO') . "\n";
}
