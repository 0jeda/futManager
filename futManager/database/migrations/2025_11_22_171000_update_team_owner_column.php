<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('teams') || !Schema::hasColumn('teams', 'owner_id')) {
            return;
        }

        if (!Schema::hasColumn('teams', 'owner_name')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->string('owner_name')->nullable()->after('name');
            });
        }

        DB::table('teams')
            ->leftJoin('owners', 'teams.owner_id', '=', 'owners.id')
            ->update([
                'owner_name' => DB::raw('COALESCE(owners.name, owners.contact_email, owners.contact_phone)'),
            ]);

        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('teams') || !Schema::hasColumn('teams', 'owner_name')) {
            return;
        }

        if (!Schema::hasColumn('teams', 'owner_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->foreignId('owner_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('owner_name');
        });
    }
};
