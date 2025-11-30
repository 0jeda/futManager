<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            // Eliminar la constraint incorrecta
            $table->dropForeign(['team_id']);
            
            // Recrear la constraint correctamente:
            // Cuando se elimina un TEAM, se eliminan los PLAYERS (cascadeOnDelete)
            // Cuando se elimina un PLAYER, NO afecta al TEAM
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->cascadeOnDelete();
        });
    }
};
