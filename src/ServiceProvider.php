<?php

namespace Madewithlove\LaravelDebugConsole;

use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Madewithlove\LaravelDebugConsole\Console\Debug;

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

        // Boots laravel debug bar
        $debugBar->boot();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(DebugbarServiceProvider::class);
    }
}
