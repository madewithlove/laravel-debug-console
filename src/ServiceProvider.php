<?php

namespace Madewithlove\LaravelDebugConsole;

use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Clue\React\Stdio\Stdio;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Madewithlove\LaravelDebugConsole\Console\Debug;
use React\EventLoop\Factory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // Obey the same rules of laravel debugbar so we do not enable profiling where is not expected too.
        if (!$this->isEnabled()) {
            return;
        }

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
        $this->app->register(DebugbarServiceProvider::class);
    }

    /**
     * @return bool
     */
    protected function isEnabled()
    {
        $enabled = $this->app['config']->get('debugbar.enabled');

        if (is_null($enabled)) {
            $enabled = $this->app['config']->get('app.debug');
        }

        return (bool) $enabled;
    }
}
