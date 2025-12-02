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
        $admin = User::updateOrCreate(
            ['email' => 'admin@futmanager.com'],
            [
                'name' => 'Administrador FutManager',
                'password' => Hash::make('Admin!234'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $normalUser = User::updateOrCreate(
            ['email' => 'usuario@futmanager.com'],
            [
                'name' => 'Usuario Normal',
                'password' => Hash::make('User!234'),
                'role' => 'player',
                'email_verified_at' => now(),
            ]
        );

        Owner::updateOrCreate(
            ['name' => 'Complejo Deportivo Reyes'],
            [
                'user_id' => $admin->id,
                'contact_email' => 'contacto@canchareyes.com',
                'contact_phone' => '555-000-1234',
                'notes' => 'Dueño principal del complejo.',
            ]
        );

        // Crear un equipo de ejemplo
        $team = Team::updateOrCreate(
            ['name' => 'Equipo Demo'],
            [
                'owner_name' => 'Juan Pérez',
                'short_name' => 'DEMO',
                'coach_name' => 'Carlos López',
                'contact_email' => 'demo@equipo.com',
                'contact_phone' => '555-1234',
                'is_active' => true,
            ]
        );

        // Crear jugador para el usuario normal
        Player::updateOrCreate(
            ['user_id' => $normalUser->id],
            [
                'team_id' => $team->id,
                'first_name' => 'Usuario',
                'last_name' => 'Normal',
                'position' => 'Delantero',
                'number' => 10,
            ]
        );
    }
}
