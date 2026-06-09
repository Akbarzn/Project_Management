<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$skill = 'Laravel';
$driver = \Illuminate\Support\Facades\DB::getDriverName();
echo "Database Driver: " . $driver . "\n";

// Raw skills dump
echo "Raw skills in database for ID >= 12:\n";
foreach (\App\Models\Karyawan::where('id', '>=', 12)->get() as $k) {
    echo "ID: {$k->id}, Name: {$k->name}, Raw Skills (DB): " . \Illuminate\Support\Facades\DB::table('karyawans')->where('id', $k->id)->value('skills') . ", Casted: " . json_encode($k->skills) . "\n";
}

$repo = app()->make(\App\Repositories\Eloquent\KaryawanRepository::class);
$results = $repo->getAvailableBySkill($skill);

echo "\ngetAvailableBySkill('Laravel') returned candidates:\n";
foreach ($results as $k) {
    echo "- ID: {$k->id}, Name: {$k->name}\n";
}
