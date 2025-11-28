<?php

use App\Http\Controllers\FieldController;
use App\Http\Controllers\TeamController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\PlayerController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('can:manage-fields')->resource('fields', FieldController::class)->except('show');
    Route::middleware('can:manage-teams')->resource('teams', TeamController::class)->except('show');
    // Player CRUD nested under teams (add) and standalone update/delete
    Route::middleware('can:manage-teams')->post('teams/{team}/players', [PlayerController::class, 'store'])->name('teams.players.store');
    Route::middleware('can:manage-teams')->put('players/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::middleware('can:manage-teams')->delete('players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');
});
