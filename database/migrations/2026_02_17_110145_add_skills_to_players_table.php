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
            $table->string('skill_1')->nullable();
            $table->string('skill_2')->nullable();
            $table->string('skill_3')->nullable();
            $table->string('skill_4')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['skill_1', 'skill_2', 'skill_3', 'skill_4']);
        });
    }
};
