<?php

use Illuminate\Support\Facades\Schedule;

// Keep the player avatars of the running season up to date.
Schedule::command('mcup:fetch-avatars')->weeklyOn(1, '04:30');
