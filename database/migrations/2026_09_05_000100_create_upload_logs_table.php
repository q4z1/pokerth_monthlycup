<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per uploaded table (not per player), so the game log link
        // can be quoted back in the final round seeding forum post without
        // repeating it on every player row of that table.
        Schema::create('upload_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->enum('type', ['firstround', 'final']);
            $table->string('table_name', 16);
            $table->unsignedTinyInteger('month');
            $table->string('pdb', 64);
            $table->unsignedInteger('game_id');
            $table->timestamps();

            $table->unique(['year', 'month', 'type', 'table_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_logs');
    }
};
