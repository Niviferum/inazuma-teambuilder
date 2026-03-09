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
        Schema::table('player_team', function (Blueprint $table) {
            $table->string('formation_position')->nullable(); // ex: "DF_1", "MF_2", "FW_3"
            $table->string('formation')->nullable(); // ex: "4-3-3"
        });
    }

    public function down(): void
    {
        Schema::table('player_team', function (Blueprint $table) {
            $table->dropColumn(['formation_position', 'formation']);
        });
    }
};
