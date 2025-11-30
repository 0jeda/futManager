<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Tournament;

class BracketGenerator
{
    /**
     * Genera la estructura completa del bracket, dejando casillas con "TBD" para que
     * el usuario pueda asignar equipos manualmente en la primera ronda.
     */
    public function generate(Tournament $tournament): array
    {
        $size = $tournament->bracket_size;

        if (!$size) {
            return [];
        }

        $rounds = (int) log($size, 2);
        $bracket = [];

        $roundNames = $this->getRoundNames($size);
        $positions = $this->generateInitialPositions($tournament->teams, $size);

        for ($round = 0; $round < $rounds; $round++) {
            $matchesInRound = $size / pow(2, $round + 1);
            $bracket[$roundNames[$round]] = [];

            for ($match = 0; $match < $matchesInRound; $match++) {
                if ($round === 0) {
                    // Primera ronda: llenamos con equipos si existen, si no quedan en TBD
                    $bracket[$roundNames[$round]][] = [
                        'match_id' => null,
                        'position' => $match,
                        'team1_id' => $positions[$match * 2]['team_id'] ?? null,
                        'team1_name' => $positions[$match * 2]['team_name'] ?? 'TBD',
                        'team2_id' => $positions[$match * 2 + 1]['team_id'] ?? null,
                        'team2_name' => $positions[$match * 2 + 1]['team_name'] ?? 'TBD',
                        'winner_id' => null,
                        'winner_name' => null,
                        'score1' => null,
                        'score2' => null,
                    ];
                } else {
                    // Rondas posteriores: se llenan con ganadores
                    $bracket[$roundNames[$round]][] = [
                        'match_id' => null,
                        'position' => $match,
                        'team1_id' => null,
                        'team1_name' => 'TBD',
                        'team2_id' => null,
                        'team2_name' => 'TBD',
                        'winner_id' => null,
                        'winner_name' => null,
                        'score1' => null,
                        'score2' => null,
                    ];
                }
            }
        }

        return $bracket;
    }

    /**
     * Nombres de las rondas segun el tamano del bracket.
     */
    private function getRoundNames(int $size): array
    {
        $names = [];

        switch ($size) {
            case 32:
                $names[] = 'Dieciseisavos';
                $names[] = 'Octavos';
                $names[] = 'Cuartos';
                $names[] = 'Semifinales';
                $names[] = 'Final';
                break;
            case 16:
                $names[] = 'Octavos';
                $names[] = 'Cuartos';
                $names[] = 'Semifinales';
                $names[] = 'Final';
                break;
            case 8:
                $names[] = 'Cuartos';
                $names[] = 'Semifinales';
                $names[] = 'Final';
                break;
            case 4:
                $names[] = 'Semifinales';
                $names[] = 'Final';
                break;
        }

        return $names;
    }

    /**
     * Genera las posiciones iniciales para la primera ronda.
     * Mantiene un orden estable; los huecos faltantes quedan en TBD.
     */
    private function generateInitialPositions($teams, int $size): array
    {
        $positions = [];
        $teamArray = $teams->values();

        for ($i = 0; $i < $size; $i++) {
            $team = $teamArray->get($i);
            $positions[] = [
                'team_id' => $team?->id,
                'team_name' => $team?->name ?? 'TBD',
            ];
        }

        return $positions;
    }

    /**
     * Avanza el ganador a la siguiente ronda y crea el partido siguiente si ambos cupos estan listos.
     */
    public function advanceWinner(Tournament $tournament, string $round, int $position, int $winnerId): array
    {
        $bracket = $tournament->bracket_data;

        if (!$bracket) {
            return [];
        }

        $winner = Team::find($winnerId);
        if (!$winner) {
            return $bracket;
        }

        if (isset($bracket[$round][$position])) {
            $bracket[$round][$position]['winner_id'] = $winnerId;
            $bracket[$round][$position]['winner_name'] = $winner->name;
        }

        $roundNames = $this->getRoundNames($tournament->bracket_size ?? 0);
        if (empty($roundNames)) {
            $roundNames = array_keys($bracket);
        }

        $currentRoundIndex = array_search($round, $roundNames, true);
        if ($currentRoundIndex === false) {
            return $bracket;
        }

        if (isset($roundNames[$currentRoundIndex + 1])) {
            $nextRound = $roundNames[$currentRoundIndex + 1];
            $nextPosition = (int) floor($position / 2);

            if (!isset($bracket[$nextRound][$nextPosition])) {
                return $bracket;
            }

            if ($position % 2 === 0) {
                $bracket[$nextRound][$nextPosition]['team1_id'] = $winnerId;
                $bracket[$nextRound][$nextPosition]['team1_name'] = $winner->name;
            } else {
                $bracket[$nextRound][$nextPosition]['team2_id'] = $winnerId;
                $bracket[$nextRound][$nextPosition]['team2_name'] = $winner->name;
            }

            // No crear partido automáticamente: el usuario debe programar con fecha/hora/cancha
        }

        return $bracket;
    }
}
