<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Safety net: the suite must never touch the live database.
     *
     * phpunit.xml points the connection at in-memory SQLite and sets
     * APP_CONFIG_CACHE to a path that does not exist, so a production config
     * cache (php artisan config:cache) cannot override those values.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        $default = config('database.default');
        $connection = config("database.connections.$default");

        if (($connection['driver'] ?? null) !== 'sqlite' || ($connection['database'] ?? null) !== ':memory:') {
            throw new RuntimeException(sprintf(
                'Refusing to run the test suite against "%s" (%s). Tests require in-memory SQLite.',
                $default,
                $connection['database'] ?? 'unknown'
            ));
        }

        return $app;
    }
}
