<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::latest('name')->paginate(9);

        return view('teams.index', compact('teams'));
    }

    public function create(): View
    {
        return view('teams.create', [
            'team' => new Team(),
        ]);
    }

    public function store(TeamRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->processAndStoreLogo($request->file('logo'));
        }

        Team::create($data);

        return redirect()->route('teams.index')->with('status', __('Equipo creado correctamente.'));
    }

    public function edit(Team $team): View
    {
        return view('teams.edit', compact('team'));
    }

    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($team->logo_path) {
                Storage::disk('public')->delete($team->logo_path);
            }

            $data['logo_path'] = $this->processAndStoreLogo($request->file('logo'));
        }

        $team->update($data);

        return redirect()->route('teams.index')->with('status', __('Equipo actualizado correctamente.'));
    }

    public function destroy(Team $team): RedirectResponse
    {
        if ($team->logo_path) {
            Storage::disk('public')->delete($team->logo_path);
        }

        $team->delete();

        return redirect()->route('teams.index')->with('status', __('El equipo fue eliminado.'));
    }

    /**
     * Resize/normalize an uploaded logo and store it on the public disk.
     * Returns the stored relative path (e.g. 'teams/xxxx.png').
     */
    private function processAndStoreLogo(UploadedFile $file): string
    {
        $maxDim = 512; // max width/height for stored logos

        $mime = $file->getMimeType();

        // If SVG or unknown non-raster, store as-is
        if ($mime === 'image/svg+xml') {
            return $file->store('teams', 'public');
        }

        $content = file_get_contents($file->getRealPath());
        $srcImg = @imagecreatefromstring($content);
        if (! $srcImg) {
            // fallback: store original file
            return $file->store('teams', 'public');
        }

        $width = imagesx($srcImg);
        $height = imagesy($srcImg);

        $scale = min(1, $maxDim / max($width, $height));
        $newW = (int) round($width * $scale);
        $newH = (int) round($height * $scale);

        $dst = imagecreatetruecolor($newW, $newH);
        // Preserve transparency for PNG/GIF
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);

        // Determine output format from mime
        $extension = 'png';
        $outBuffer = null;
        ob_start();
        if (in_array($mime, ['image/png'])) {
            imagepng($dst, null, 6);
            $extension = 'png';
        } elseif (in_array($mime, ['image/gif'])) {
            imagegif($dst);
            $extension = 'gif';
        } else {
            // default to jpeg for other types
            imagejpeg($dst, null, 90);
            $extension = 'jpg';
        }
        $outBuffer = ob_get_clean();

        imagedestroy($srcImg);
        imagedestroy($dst);

        $filename = 'teams/' . uniqid('', true) . '.' . $extension;
        Storage::disk('public')->put($filename, $outBuffer);

        return $filename;
    }


}
