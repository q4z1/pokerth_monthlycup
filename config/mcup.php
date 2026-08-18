<?php

return [

    /*
     * Directory holding the uploaded PokerTH game log databases (SQLite *.pdb).
     */
    'log_dir' => env('PTH_LOG_DIR', '/var/www/pokerth/log_file_analysis/upload'),

    /*
     * Player profile page at pokerth.net, used for the links in every table.
     */
    'player_url' => 'https://www.pokerth.net/player?u=',

    /*
     * Number of seats available per cup; everybody registering later is listed
     * as a substitute.
     */
    'seats' => 90,

    /*
     * Registration closes this many minutes before the scheduled cup start.
     */
    'registration_closes_before' => 60,

    /*
     * Verify a playername against the PokerTH ranking database before accepting
     * a registration. Disable when that database is not reachable.
     */
    'verify_playername' => env('MCUP_VERIFY_PLAYERNAME', true),
];
