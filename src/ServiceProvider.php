<?php

namespace Madewithlove\LaravelDebugConsole;

use Clue\React\Stdio\Stdio;
use Madewithlove\LaravelDebugConsole\Console\Debug;
use \Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Madewithlove\LaravelDebugConsole\Renderers\Exception;
use Madewithlove\LaravelDebugConsole\Renderers\General;
use Madewithlove\LaravelDebugConsole\Renderers\Message;
use Madewithlove\LaravelDebugConsole\Renderers\Query;
use Madewithlove\LaravelDebugConsole\Renderers\Request;
use Madewithlove\LaravelDebugConsole\Renderers\Route;
use Madewithlove\LaravelDebugConsole\Renderers\Timeline;
use React\EventLoop\Factory;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

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

        $this->app->bind(InputInterface::class, function () {
            return new ArrayInput(['command' => []]);
        });
        $this->app->bind(OutputInterface::class, ConsoleOutput::class);

        $this->app->bind(Screen::class, function () {
            $screen = new Screen($this->app->make(StorageRepository::class));
            $screen->registerRenderer('general', $this->app->make(General::class), 'header');
            $screen->registerRenderer('messages', $this->app->make(Message::class));
            $screen->registerRenderer('timeline', $this->app->make(Timeline::class));
            $screen->registerRenderer('exceptions', $this->app->make(Exception::class));
            $screen->registerRenderer('route', $this->app->make(Route::class));
            $screen->registerRenderer('queries', $this->app->make(Query::class));
            $screen->registerRenderer('request', $this->app->make(Request::class));

            return $screen;
        });

        $this->app->bind(Debug::class, function () {
            $loop = Factory::create();
            $terminal = new Terminal(new Stdio($loop));
            $screen = $this->app->make(Screen::class);

            return new Debug($loop, $terminal, $screen);
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
