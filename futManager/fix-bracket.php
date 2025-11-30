<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tournament = \App\Models\Tournament::latest()->first();
$bracket = $tournament->bracket_data;

echo "Torneo actual: {$tournament->name}\n";
echo "Ganador Semifinal 1: {$bracket['Semifinales'][0]['winner_name']}\n";
echo "Ganador Semifinal 2: {$bracket['Semifinales'][1]['winner_name']}\n\n";

// Asignar ganadores a la Final
$bracket['Final'][0]['team1_id'] = $bracket['Semifinales'][0]['winner_id'];
$bracket['Final'][0]['team1_name'] = $bracket['Semifinales'][0]['winner_name'];
$bracket['Final'][0]['team2_id'] = $bracket['Semifinales'][1]['winner_id'];
$bracket['Final'][0]['team2_name'] = $bracket['Semifinales'][1]['winner_name'];

// Crear el partido de la Final
$game = $tournament->matches()->create([
    'field_id' => $tournament->field_id,
    'scheduled_at' => now()->addDays(3),
    'round' => 'Final',
    'status' => 'scheduled',
]);

$game->participants()->create([
    'team_id' => $bracket['Final'][0]['team1_id'],
    'is_home' => true,
    'goals' => 0,
    'points_awarded' => 0,
]);

$game->participants()->create([
    'team_id' => $bracket['Final'][0]['team2_id'],
    'is_home' => false,
    'goals' => 0,
    'points_awarded' => 0,
]);

$bracket['Final'][0]['match_id'] = $game->id;

$tournament->update(['bracket_data' => $bracket]);

echo "✅ Final creada correctamente:\n";
echo "   {$bracket['Final'][0]['team1_name']} vs {$bracket['Final'][0]['team2_name']}\n";
echo "   Partido ID: {$game->id}\n";
