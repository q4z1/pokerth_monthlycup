<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->enum('type', ['firstround', 'final']);
            $table->string('table_name', 16);
            $table->unsignedTinyInteger('month');
            $table->string('playername', 64);
            $table->unsignedTinyInteger('position');
            $table->integer('points');
            $table->timestamps();

            $table->index(['year', 'month']);
            $table->index(['year', 'month', 'type', 'table_name']);
            $table->index(['year', 'playername']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
