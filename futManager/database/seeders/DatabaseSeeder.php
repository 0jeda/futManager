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

        Owner::updateOrCreate(
            ['name' => 'Complejo Deportivo Reyes'],
            [
                'user_id' => $admin->id,
                'contact_email' => 'contacto@canchareyes.com',
                'contact_phone' => '555-000-1234',
                'notes' => 'Dueño principal del complejo.',
            ]
        );
    }
}
