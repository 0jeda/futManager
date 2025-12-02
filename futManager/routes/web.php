<?php

use App\Http\Controllers\FieldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentTeamController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\MatchResultController;
use App\Http\Controllers\BracketSlotController;
use App\Http\Controllers\PlayerMatchController;
use App\Http\Controllers\TournamentReportController;
use App\Http\Controllers\TeamReportController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)
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

    Route::get('my-matches', [PlayerMatchController::class, 'index'])->name('player.matches');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('can:manage-fields')->resource('fields', FieldController::class)->except('show');
    Route::middleware('can:manage-teams')->resource('teams', TeamController::class)->except('show');
    Route::middleware('can:manage-teams')->get('teams/{team}/pdf', [TeamReportController::class, 'download'])->name('teams.pdf');
    Route::middleware('can:manage-teams')->resource('tournaments', TournamentController::class)->except('show');
    Route::middleware('can:manage-teams')->get('tournaments/{tournament}/pdf', [TournamentReportController::class, 'downloadSchedule'])->name('tournaments.pdf');

    // Tournament teams management
    Route::middleware('can:manage-teams')->post('tournaments/{tournament}/teams', [TournamentTeamController::class, 'store'])->name('tournaments.teams.store');
    Route::middleware('can:manage-teams')->delete('tournaments/{tournament}/teams/{team}', [TournamentTeamController::class, 'destroy'])->name('tournaments.teams.destroy');

    // Matches management
    Route::middleware('can:manage-teams')->post('tournaments/{tournament}/matches', [MatchController::class, 'store'])->name('tournaments.matches.store');
    Route::middleware('can:manage-teams')->delete('matches/{match}', [MatchController::class, 'destroy'])->name('matches.destroy');
    Route::middleware('can:manage-teams')->put('matches/{match}/result', [MatchResultController::class, 'update'])->name('matches.result.update');

    // Bracket slot management
    Route::middleware('can:manage-teams')->post('tournaments/{tournament}/bracket/assign', [BracketSlotController::class, 'assignTeam'])->name('tournaments.bracket.assign');
    Route::middleware('can:manage-teams')->post('tournaments/{tournament}/bracket/remove', [BracketSlotController::class, 'removeTeam'])->name('tournaments.bracket.remove');
    Route::middleware('can:manage-teams')->post('tournaments/{tournament}/bracket/create-match', [BracketSlotController::class, 'createMatch'])->name('tournaments.bracket.createMatch');

    // Player CRUD nested under teams (add) and standalone update/delete
    Route::middleware('can:manage-teams')->post('teams/{team}/players', [PlayerController::class, 'store'])->name('teams.players.store');
    Route::middleware('can:manage-teams')->put('players/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::middleware('can:manage-teams')->delete('players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');
});
