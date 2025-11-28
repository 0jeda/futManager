<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Game;
use App\Models\MatchParticipant;
use App\Models\Owner;
use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $admin = User::updateOrCreate(
                ['email' => 'admin@futmanager.com'],
                [
                    'name' => 'Administrador FutManager',
                    'password' => Hash::make('Admin!234'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );

            $playerUser = User::updateOrCreate(
                ['email' => 'jugador@futmanager.com'],
                [
                    'name' => 'Carlos Jugador',
                    'password' => Hash::make('Player!234'),
                    'role' => 'player',
                    'email_verified_at' => now(),
                ]
            );

            $owner = Owner::updateOrCreate(
                ['name' => 'Complejo Deportivos Reyes'],
                [
                    'user_id' => $admin->id,
                    'contact_email' => 'contacto@canchareyes.com',
                    'contact_phone' => '555-000-1234',
                    'notes' => 'Dueno principal del complejo.',
                ]
            );

            $mainField = Field::updateOrCreate(
                ['name' => 'Cancha Principal'],
                [
                    'owner_id' => $owner->id,
                    'location' => 'Av. Revolucion 120, CDMX',
                    'surface' => 'Sintetica',
                    'is_active' => true,
                ]
            );

            $tournament = Tournament::updateOrCreate(
                ['name' => 'Clausura Fut Rapido'],
                [
                    'field_id' => $mainField->id,
                    'category' => 'Libre',
                    'format' => 'Round Robin + Final',
                    'status' => 'active',
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addMonth()->toDateString(),
                    'description' => 'Torneo de temporada para equipos locales.',
                ]
            );

            $teams = collect([
                [
                    'name' => 'Toros Rojos',
                    'owner_name' => 'Club Deportivo Torres',
                    'short_name' => 'TOR',
                    'coach_name' => 'Luis Mendez',
                    'contact_email' => 'toros@example.com',
                    'contact_phone' => '555-000-1111',
                ],
                [
                    'name' => 'Rayos Azules',
                    'owner_name' => 'Agrupacion Rayos',
                    'short_name' => 'RAZ',
                    'coach_name' => 'Miguel Varela',
                    'contact_email' => 'rayos@example.com',
                    'contact_phone' => '555-000-2222',
                ],
            ])->map(function (array $data) {
                return Team::updateOrCreate(
                    ['name' => $data['name']],
                    array_merge($data, ['is_active' => true])
                );
            });

            $teams->each(function (Team $team, int $index) use ($playerUser) {
                $basePlayers = [
                    [
                        'first_name' => 'Carlos',
                        'last_name' => 'Ramirez',
                        'position' => 'Delantero',
                        'number' => 9,
                    ],
                    [
                        'first_name' => 'Hugo',
                        'last_name' => 'Lopez',
                        'position' => 'Portero',
                        'number' => 1,
                    ],
                    [
                        'first_name' => 'Esteban',
                        'last_name' => 'Cano',
                        'position' => 'Defensa',
                        'number' => 4,
                    ],
                ];

                foreach ($basePlayers as $playerData) {
                    $linkUser = $playerData['first_name'] === 'Carlos' && $index === 0
                        ? $playerUser->id
                        : null;

                    Player::updateOrCreate(
                        [
                            'team_id' => $team->id,
                            'first_name' => $playerData['first_name'],
                            'last_name' => $playerData['last_name'],
                        ],
                        array_merge($playerData, [
                            'team_id' => $team->id,
                            'user_id' => $linkUser,
                            'birthdate' => now()->subYears(25 + $index)->subDays($playerData['number']),
                        ])
                    );
                }
            });

            $tournament->teams()->sync($teams->mapWithKeys(fn (Team $team, $index) => [
                $team->id => ['group' => $index === 0 ? 'A' : 'B'],
            ])->toArray());

            $nextMatch = Game::updateOrCreate(
                [
                    'tournament_id' => $tournament->id,
                    'scheduled_at' => now()->addDays(3)->startOfHour(),
                    'round' => 'Jornada 1',
                ],
                [
                    'field_id' => $mainField->id,
                    'status' => 'scheduled',
                    'notes' => 'Partido inaugural.',
                ]
            );

            $homeTeam = $teams->first();
            $awayTeam = $teams->last();

            MatchParticipant::updateOrCreate(
                ['match_id' => $nextMatch->id, 'team_id' => $homeTeam->id],
                ['is_home' => true, 'goals' => 0, 'points_awarded' => 0]
            );

            MatchParticipant::updateOrCreate(
                ['match_id' => $nextMatch->id, 'team_id' => $awayTeam->id],
                ['is_home' => false, 'goals' => 0, 'points_awarded' => 0]
            );
        });
    }
}
