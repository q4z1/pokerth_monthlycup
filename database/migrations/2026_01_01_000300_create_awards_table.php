<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->index();
            $table->unsignedTinyInteger('month')->nullable();
            $table->string('type', 64);
            $table->binary('file')->nullable();
            $table->string('filename', 128)->nullable();
            $table->string('mime', 64)->nullable();
            $table->timestamps();

            $table->unique(['year', 'month', 'type']);
        });

        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE `awards` MODIFY `file` LONGBLOB NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
