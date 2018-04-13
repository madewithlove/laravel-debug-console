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
use React\EventLoop\Factory;

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
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * @param \Madewithlove\LaravelDebugConsole\StorageRepository $repository
     */
    public function __construct(StorageRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->loop = Factory::create();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $section = $this->argument('section');
        $this->loop->addPeriodicTimer(1, function () use ($section) {
            $data = $this->repository->latest();

            // Checks if its a new request
            if (!$this->isNewRequest($data)) {
                return;
            }

            $this->refresh();

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
        });

        $this->loop->run();
    }

    private function refresh()
    {
        system('clear');
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function isNewRequest(array $data)
    {
        $id = array_get($data, '__meta.id');
        if ($id && empty($this->currentRequest) || $this->currentRequest !== $id) {
            $this->currentRequest = $id;

            return true;
        }

        return false;
    }
}
