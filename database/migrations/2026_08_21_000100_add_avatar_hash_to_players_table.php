<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            // MD5 of the avatar image — the same value PokerTH stores as
            // player.avatar_hash, so it can be matched against avatar_blacklist
            // without loading every blob.
            $table->char('avatar_hash', 32)->nullable()->after('avatar_mime')->index();
        });

        // MD5() is a MySQL function; on the SQLite used by the test suite
        // there is nothing to backfill anyway.
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('UPDATE `players` SET `avatar_hash` = MD5(`avatar`) WHERE `avatar` IS NOT NULL AND LENGTH(`avatar`) > 0');
        }
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex(['avatar_hash']);
            $table->dropColumn('avatar_hash');
        });
    }
};
