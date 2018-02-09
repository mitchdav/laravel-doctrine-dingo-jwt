<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    //protected $baseUrl = 'http://house-tempest.app/';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function __get(string $name) {
        if ($name === 'baseUrl' ) {
            return $_SERVER['APP_URL'];
        }
        throw new RuntimeException('Error: Property not found');
    }

    public function __set(string $name, $value) {
        throw new RuntimeException('Error: Property not found');
    }

    public function __isset(string $name) {
        throw new RuntimeException('Error: Property not found');
    }
}

