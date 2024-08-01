<?php

namespace Ghostscypher\Mpesa\Tests;

use Ghostscypher\Mpesa\MpesaServiceProvider;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     *
     * @param  Application $app
     * @return void
     */
    protected function loadConfig($app)
    {
        // Alter the testing mpesa environment
        // Load the package config file
        $configs = require __DIR__ . '/../config/mpesa.php';
        
        foreach($configs as $key => $value) {
            $app['config']->set('mpesa.' . $key, $value);
        }

    }

    protected function loadMigrations()
    {
        if(Schema::hasTable('mpesa_logs')) {
            return;
        }

        // Get all files in the migrations directory
        $files = glob(__DIR__.'/../database/migrations/*.php.stub');

        foreach($files as $file) {
            $migration = include $file;
            $migration->up();
        }
    }

    /*
     * Define environment setup.
     *
     * @param  Application $app
     * @return void
    */

    protected function getEnvironmentSetUp($app)
    {
        // Load .env file
        $original_env = $_ENV;
        \Dotenv\Dotenv::createImmutable(__DIR__ . '/../', '.env')->safeLoad();
        $GLOBALS['_ENV'] = array_merge($_ENV, $original_env);

        $this->loadConfig($app);
        $this->loadMigrations();
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MpesaServiceProvider::class,
        ];
    }

    public function refreshDatabase()
    {
        return false;
    }
}
