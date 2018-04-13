<?php

namespace Madewithlove\LaravelDebugConsole\Console;

use Illuminate\Console\Command;
use Madewithlove\LaravelDebugConsole\Renderers\Exception;
use Madewithlove\LaravelDebugConsole\Renderers\General;
use Madewithlove\LaravelDebugConsole\Renderers\Message;
use Madewithlove\LaravelDebugConsole\Renderers\Query;
use Madewithlove\LaravelDebugConsole\Renderers\Request;
use Madewithlove\LaravelDebugConsole\Renderers\Route;
use Madewithlove\LaravelDebugConsole\Renderers\Timeline;
use Madewithlove\LaravelDebugConsole\StorageRepository;

class Debug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug {section?}';

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
     * @var null|string
     */
    private $currentRequest = null;

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

            // Checks if its a new request
            if ($this->isNewRequest(array_get($data, '__meta.id'))) {
                $this->refresh();
            } else {
                $this->wait();

                continue;
            }

            (new General($this->input, $this->output))->render($data);

            switch ($section) {
                case 'messages':
                    (new Message($this->input, $this->output))->render($data);
                    break;
                case 'timeline':
                    (new Timeline($this->input, $this->output))->render($data);
                    break;
                case 'exceptions':
                    (new Exception($this->input, $this->output))->render($data);
                    break;
                case 'route':
                    (new Route($this->input, $this->output))->render($data);
                    break;
                case 'queries':
                    (new Query($this->input, $this->output))->render($data);
                    break;
                case 'request':
                    (new Request($this->input, $this->output))->render($data);
                    break;
            }
        }
    }

    private function wait()
    {
        sleep(1);
    }

    private function refresh()
    {
        $this->wait();
        system('clear');
    }

    /**
     * @param $id
     *
     * @return bool
     */
    private function isNewRequest($id)
    {
        if (empty($this->currentRequest) || $this->currentRequest !== $id) {
            $this->currentRequest = $id;

            return true;
        }

        return false;
    }
}
