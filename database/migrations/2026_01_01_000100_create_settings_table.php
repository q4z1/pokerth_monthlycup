<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->index();
            $table->string('type', 64);
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['year', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
