<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Team;

$teams = Team::all();
foreach ($teams as $t) {
    echo $t->id . ':' . ($t->logo_path ?? 'NULL') . PHP_EOL;
}
