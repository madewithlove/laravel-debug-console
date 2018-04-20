<?php

namespace Madewithlove\LaravelDebugConsole;

use Clue\React\Stdio\Stdio;
use Madewithlove\LaravelDebugConsole\Console\Debug;
use \Illuminate\Support\ServiceProvider as BaseServiceProvider;
use React\EventLoop\Factory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Debug::class,
            ]);
        }

        /** @var \DebugBar\DebugBar $debugBar */
        $debugBar = $this->app->make('debugbar');

        $this->app->bind(StorageRepository::class, function () use ($debugBar) {
            return new StorageRepository($debugBar->getStorage());
        });

        $this->app->bind(Debug::class, function () {
            $storageRepository = $this->app->make(StorageRepository::class);
            $loop = Factory::create();
            $terminal = new Terminal(new Stdio($loop));

            return new Debug($storageRepository, $loop, $terminal);
        });

        // Boots laravel debug bar
        $debugBar->boot();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
    }
}
