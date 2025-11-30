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
        Schema::table('tournaments', function (Blueprint $table) {
            $table->boolean('is_bracket')->default(false)->after('status');
            $table->integer('bracket_size')->nullable()->after('is_bracket');
            $table->json('bracket_data')->nullable()->after('bracket_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['is_bracket', 'bracket_size', 'bracket_data']);
        });
    }
};
