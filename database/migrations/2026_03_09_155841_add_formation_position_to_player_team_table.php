<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_team', function (Blueprint $table) {
            $table->integer('formation_position')->nullable()->after('quantity');
            $table->string('formation')->nullable()->after('formation_position');
        });
    }

    public function down(): void
    {
        Schema::table('player_team', function (Blueprint $table) {
            $table->dropColumn(['formation_position', 'formation']);
        });
    }
};