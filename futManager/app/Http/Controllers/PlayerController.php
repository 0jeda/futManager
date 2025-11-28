<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class PlayerController extends Controller
{
    public function store(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:50',
            'number' => 'nullable|integer|min:0|max:255',
            'birthdate' => 'nullable|date',
            'curp' => 'nullable|string|max:32',
            'photo' => 'nullable|image|max:2048',
        ]);

        $playerData = [
            'team_id' => $team->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'position' => $data['position'] ?? null,
            'number' => $data['number'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'curp' => $data['curp'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            $playerData['photo_path'] = $this->processAndStorePhoto($request->file('photo'));
        }

        Player::create($playerData);

        return redirect()->back()->with('status', __('Jugador agregado.'));
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:50',
            'number' => 'nullable|integer|min:0|max:255',
            'birthdate' => 'nullable|date',
            'curp' => 'nullable|string|max:32',
            'photo' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'position' => $data['position'] ?? null,
            'number' => $data['number'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'curp' => $data['curp'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            if ($player->photo_path) {
                Storage::disk('public')->delete($player->photo_path);
            }

            $updateData['photo_path'] = $this->processAndStorePhoto($request->file('photo'));
        }

        $player->update($updateData);

        return redirect()->back()->with('status', __('Jugador actualizado.'));
    }

    public function destroy(Player $player): RedirectResponse
    {
        if ($player->photo_path) {
            Storage::disk('public')->delete($player->photo_path);
        }

        $player->delete();

        return redirect()->back()->with('status', __('Jugador eliminado.'));
    }

    private function processAndStorePhoto(UploadedFile $file): string
    {
        $maxDim = 400; // face photos, keep smaller

        $mime = $file->getMimeType();

        if ($mime === 'image/svg+xml') {
            return $file->store('players', 'public');
        }

        $content = file_get_contents($file->getRealPath());
        $srcImg = @imagecreatefromstring($content);
        if (! $srcImg) {
            return $file->store('players', 'public');
        }

        $width = imagesx($srcImg);
        $height = imagesy($srcImg);

        $scale = min(1, $maxDim / max($width, $height));
        $newW = (int) round($width * $scale);
        $newH = (int) round($height * $scale);

        $dst = imagecreatetruecolor($newW, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);

        $extension = 'png';
        ob_start();
        if (in_array($mime, ['image/png'])) {
            imagepng($dst, null, 6);
            $extension = 'png';
        } elseif (in_array($mime, ['image/gif'])) {
            imagegif($dst);
            $extension = 'gif';
        } else {
            imagejpeg($dst, null, 90);
            $extension = 'jpg';
        }
        $outBuffer = ob_get_clean();

        imagedestroy($srcImg);
        imagedestroy($dst);

        $filename = 'players/' . uniqid('', true) . '.' . $extension;
        Storage::disk('public')->put($filename, $outBuffer);

        return $filename;
    }
}
