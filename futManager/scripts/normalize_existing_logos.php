<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Team;
use Illuminate\Support\Facades\Storage;

$maxDim = 512;

foreach (Team::all() as $team) {
    $path = $team->logo_path;
    if (! $path) continue;
    if (! Storage::disk('public')->exists($path)) {
        echo "Skip {$team->id}: file not found: {$path}\n";
        continue;
    }

    $content = Storage::disk('public')->get($path);
    $tmp = tmpfile();
    $meta = stream_get_meta_data($tmp);
    $tmpFilename = $meta['uri'];
    file_put_contents($tmpFilename, $content);

    $srcImg = @imagecreatefromstring($content);
    if (! $srcImg) {
        echo "Skip {$team->id}: not an image or unreadable: {$path}\n";
        fclose($tmp);
        continue;
    }

    $width = imagesx($srcImg);
    $height = imagesy($srcImg);
    $scale = min(1, $maxDim / max($width, $height));
    $newW = (int) round($width * $scale);
    $newH = (int) round($height * $scale);
    $dst = imagecreatetruecolor($newW, $newH);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $srcImg, 0,0,0,0, $newW, $newH, $width, $height);

    ob_start();
    // default to png output
    imagepng($dst, null, 6);
    $out = ob_get_clean();

    Storage::disk('public')->put($path, $out);

    imagedestroy($srcImg);
    imagedestroy($dst);
    fclose($tmp);

    echo "Processed {$team->id}: {$path}\n";
}
