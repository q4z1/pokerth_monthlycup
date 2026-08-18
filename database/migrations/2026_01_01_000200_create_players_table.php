<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->index();
            $table->string('playername', 64);
            $table->binary('avatar')->nullable();
            $table->string('avatar_mime', 64)->nullable();
            $table->timestamps();

            $table->unique(['year', 'playername']);
        });

        // Avatars regularly exceed the 64 KB a plain BLOB can hold.
        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE `players` MODIFY `avatar` LONGBLOB NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
