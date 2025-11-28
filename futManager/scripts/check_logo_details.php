<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Team;
use Illuminate\Support\Facades\Storage;

foreach (Team::all() as $t) {
    $path = $t->logo_path ?? 'NULL';
    $url = $t->logo_path ? Storage::disk('public')->url($t->logo_path) : 'NULL';
    $exists = $t->logo_path ? (Storage::disk('public')->exists($t->logo_path) ? 'yes' : 'no') : 'no-path';
    echo "{$t->id}: path={$path} | url={$url} | exists={$exists}" . PHP_EOL;
}
