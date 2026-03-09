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
    Schema::create('players', function (Blueprint $table) {
        $table->id();
        $table->integer('player_id')->unique(); // L'ID du CSV
        $table->string('name');
        $table->string('nickname')->nullable();
        $table->string('position'); // GK, DF, MF, FW
        $table->string('element'); // Fire, Wind, Mountain, Forest
        $table->string('rarity')->nullable();
        $table->string('team_origin')->nullable(); // Équipe d'origine
        $table->text('description')->nullable();
        $table->integer('kick')->nullable();
        $table->integer('control')->nullable();
        $table->integer('technique')->nullable();
        $table->integer('intelligence')->nullable();
        $table->integer('pressure')->nullable();
        $table->integer('physical')->nullable();
        $table->integer('agility')->nullable();
        $table->integer('total')->nullable(); // OVR
        $table->string('image_url')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
