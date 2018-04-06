<?php

namespace Madewithlove\LaravelDebugConsole\Console;

use Illuminate\Console\Command;
use Madewithlove\LaravelDebugConsole\Renderers\Query;
use Madewithlove\LaravelDebugConsole\Renderers\Request;
use Madewithlove\LaravelDebugConsole\StorageRepository;

class Debug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug {section}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays laravel debug bar stored information.';

    /**
     * @var \Madewithlove\LaravelDebugConsole\StorageRepository
     */
    private $repository;

    /**
     * @param \Madewithlove\LaravelDebugConsole\StorageRepository $repository
     */
    public function __construct(StorageRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $section = $this->argument('section');

        // Watch files every second
        while (true) {
            $data = $this->repository->latest();

            // Make sure the screen is clean
            $this->refresh();

            (new Request($this->output))->render($data);
            (new Query($this->output))->render($data);
        }
    }

    public function wait()
    {
        sleep(1);
    }

    public function refresh()
    {
        $this->wait();
        system('clear');
    }
}
