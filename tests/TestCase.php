<?php

namespace Ghostscypher\Mpesa\Tests;

use Ghostscypher\Mpesa\MpesaServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param  Application  $app
     * @return void
     */
    protected function loadConfig($app)
    {
        // Alter the testing mpesa environment
        // Load the package config file
        $configs = require __DIR__.'/../config/mpesa.php';

        foreach ($configs as $key => $value) {
            $app['config']->set('mpesa.'.$key, $value);
        }

    }

    protected function getBetween($string, $start, $end)
    {
        $string = ' '.$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    protected function loadMigrations($app)
    {
        // Get all files in the migrations directory
        $files = glob(__DIR__.'/../database/migrations/*.php.stub');

        foreach ($files as $file) {
            // Read file contents
            $content = file_get_contents($file);

            // Get table name
            $table = $this->getBetween($content, 'Schema::create(\'', '\'');

            // Check if table exists
            if (Schema::hasTable($table)) {
                continue;
            }

            $migration = include $file;
            $migration->up();
        }
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Load .env file
        $original_env = $_ENV;
        \Dotenv\Dotenv::createImmutable(__DIR__.'/../', '.env.example')->safeLoad();
        $GLOBALS['_ENV'] = array_merge($_ENV, $original_env);

        $this->loadConfig($app);
        $this->loadMigrations($app);

        // Fake the http client when generating access token
        Http::fake([
            'https://sandbox.safaricom.co.ke/oauth/v1/generate' => Http::response([
                'access_token' => 'test_token',
                'expires_in' => 3600,
            ], 200),
            'https://api.safaricom.co.ke/oauth/v1/generate' => Http::response([
                'access_token' => 'test_token',
                'expires_in' => 3600,
            ], 200),
        ]);

        // Create and register a named route
        Route::get('test', function () {
            return 'Test';
        })->name('named_route_test');
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
