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
        Schema::create('attacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attack_id');
            $table->timestamp('timestamp_started');
            $table->timestamp('timestamp_ended');
            $table->unsignedBigInteger('attacker_id');
            $table->string('attacker_name');
            $table->unsignedBigInteger('attacker_faction');
            $table->string('attacker_factionname');
            $table->unsignedBigInteger('defender_id');
            $table->string('defender_name');
            $table->unsignedBigInteger('defender_faction');
            $table->string('defender_factionname');
            $table->string('result');
            $table->boolean('stealthed');
            $table->decimal('respect');
            $table->integer('chain');
            $table->boolean('raid');
            $table->boolean('ranked_war');
            $table->decimal('respect_gain');
            $table->decimal('respect_loss');
            $table->json('modifiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attacks');
    }
};
