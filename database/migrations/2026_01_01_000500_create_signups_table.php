<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signups', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('playername', 64);
            $table->dateTime('registered_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('fp', 64)->nullable();
            $table->string('fpnew', 64)->nullable();
            $table->boolean('valid')->default(false);
            $table->timestamps();

            $table->index(['year', 'month']);
            $table->index(['year', 'month', 'valid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signups');
    }
};
