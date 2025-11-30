<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    public function store(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => 'nullable|string|min:6|max:255',
            'position' => 'nullable|string|max:50',
            'number' => [
                'nullable',
                'integer',
                'min:0',
                'max:255',
                Rule::unique('players', 'number')->where(function ($query) use ($team) {
                    return $query->where('team_id', $team->id);
                }),
            ],
            'birthdate' => 'nullable|date',
            'curp' => 'nullable|string|max:32',
            'photo' => 'nullable|image|max:2048',
        ], [
            'number.unique' => 'Este número de dorsal ya está asignado a otro jugador del equipo.',
        ]);

        // Usar contraseña proporcionada o generar una aleatoria
        $generatedPassword = $data['password'] ?? Str::random(10);
        $passwordWasGenerated = empty($data['password']);

        // Crear usuario para el jugador
        $user = User::create([
            'name' => trim($data['first_name'] . ' ' . ($data['last_name'] ?? '')),
            'email' => $data['email'],
            'password' => Hash::make($generatedPassword),
            'role' => 'player',
        ]);

        // Crear el perfil de jugador
        $playerData = [
            'team_id' => $team->id,
            'user_id' => $user->id,
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

        $sessionData = [
            'status' => __('Jugador agregado correctamente.'),
        ];

        // Solo mostrar la contraseña si fue generada automáticamente
        if ($passwordWasGenerated) {
            $sessionData['player_credentials'] = [
                'email' => $data['email'],
                'password' => $generatedPassword,
            ];
        } else {
            $sessionData['player_credentials_custom'] = [
                'email' => $data['email'],
                'message' => 'La contraseña personalizada fue establecida correctamente.',
            ];
        }

        return redirect()->back()->with($sessionData);
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|max:255',
            'position' => 'nullable|string|max:50',
            'number' => [
                'nullable',
                'integer',
                'min:0',
                'max:255',
                Rule::unique('players', 'number')->where(function ($query) use ($player) {
                    return $query->where('team_id', $player->team_id);
                })->ignore($player->id),
            ],
            'birthdate' => 'nullable|date',
            'curp' => 'nullable|string|max:32',
            'photo' => 'nullable|image|max:2048',
        ], [
            'number.unique' => 'Este número de dorsal ya está asignado a otro jugador del equipo.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $updateData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'position' => $data['position'] ?? null,
            'number' => $data['number'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'curp' => $data['curp'] ?? null,
        ];

        // Actualizar contraseña si se proporcionó
        if (!empty($data['password']) && $player->user) {
            $player->user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        if ($request->hasFile('photo')) {
            if ($player->photo_path) {
                Storage::disk('public')->delete($player->photo_path);
            }

            $updateData['photo_path'] = $this->processAndStorePhoto($request->file('photo'));
        }

        $player->update($updateData);

        $message = __('Jugador actualizado.');
        if (!empty($data['password'])) {
            $message .= ' ' . __('La contraseña fue cambiada correctamente.');
        }

        return redirect()->back()->with('status', $message);
    }

    public function destroy(Player $player): RedirectResponse
    {
        if ($player->photo_path) {
            Storage::disk('public')->delete($player->photo_path);
        }

        // Guardar referencia al usuario antes de eliminar el jugador
        $user = $player->user;

        $player->delete();

        // Eliminar el usuario asociado si existe
        if ($user) {
            $user->delete();
        }

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
