<?php

namespace Madewithlove\LaravelDebugConsole\Console;

use Illuminate\Console\Command;
use Madewithlove\LaravelDebugConsole\Screen;
use Madewithlove\LaravelDebugConsole\Terminal;
use React\EventLoop\LoopInterface;

class Debug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug {screen?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays laravel debug bar stored information.';

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * @var \Madewithlove\LaravelDebugConsole\Terminal
     */
    private $terminal;

    /**
     * @var \Madewithlove\LaravelDebugConsole\Screen
     */
    private $screen;

    /**
     * @param \React\EventLoop\LoopInterface $loop
     * @param \Madewithlove\LaravelDebugConsole\Terminal $terminal
     * @param \Madewithlove\LaravelDebugConsole\Screen $screen
     */
    public function __construct(
        LoopInterface $loop,
        Terminal $terminal,
        Screen $screen
    )
    {
        parent::__construct();

        $this->loop = $loop;
        $this->terminal = $terminal;
        $this->screen = $screen;

        // Register events
        $this->registerEvents();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $screen = $this->argument('screen');

        $this->loop->addPeriodicTimer(1, function () {
            $this->terminal->refresh();
            $this->screen->display();
        });

        $this->loop->run();
    }

    private function registerEvents()
    {
        $this->terminal->registerKeyEvent(Terminal::KEY_LEFT, function () {
            $this->screen->next();
        });

        $this->terminal->registerKeyEvent(Terminal::KEY_RIGHT, function () {
            $this->screen->previous();
        });
    }
}
