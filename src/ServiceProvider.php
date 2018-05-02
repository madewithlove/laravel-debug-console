<?php

namespace Madewithlove\LaravelDebugConsole;

use Clue\React\Stdio\Stdio;
use Madewithlove\LaravelDebugConsole\Console\Debug;
use \Illuminate\Support\ServiceProvider as BaseServiceProvider;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Terminal;

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

        $this->app->bind(Debug::class, function () use ($debugBar) {
            $storage = new StorageRepository($debugBar->getStorage());
            $loop = Factory::create();
            $stdio = new Stdio($loop);

            return new Debug($storage, $loop, $stdio);
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
