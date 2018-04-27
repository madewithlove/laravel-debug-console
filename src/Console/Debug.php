<?php

namespace Madewithlove\LaravelDebugConsole\Console;

use Clue\React\Stdio\Stdio;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Madewithlove\LaravelDebugConsole\Renderers\Exception;
use Madewithlove\LaravelDebugConsole\Renderers\General;
use Madewithlove\LaravelDebugConsole\Renderers\Message;
use Madewithlove\LaravelDebugConsole\Renderers\Query;
use Madewithlove\LaravelDebugConsole\Renderers\Request;
use Madewithlove\LaravelDebugConsole\Renderers\Route;
use Madewithlove\LaravelDebugConsole\Renderers\Timeline;
use Madewithlove\LaravelDebugConsole\StorageRepository;
use React\EventLoop\LoopInterface;

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
     * @var \Clue\React\Stdio\Stdio
     */
    private $stdio;

    /**
     * @var string
     */
    private $section;

    /**
     * @var string
     */
    private $currentSection;

    /**
     * @param \Madewithlove\LaravelDebugConsole\StorageRepository $repository
     * @param \React\EventLoop\LoopInterface $loop
     * @param \Clue\React\Stdio\Stdio $stdio
     */
    public function __construct(
        StorageRepository $repository,
        LoopInterface $loop,
        Stdio $stdio
    )
    {
        parent::__construct();

        $this->repository = $repository;
        $this->loop = $loop;
        $this->stdio = $stdio;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->section = $this->argument('section');
        $this->registerEvents();

        $this->loop->addPeriodicTimer(1, function () {
            try {
                $data = $this->repository->latest();
            } catch (FileNotFoundException $e) {
                $this->refresh();
                $this->error('No laravel debugbar storage files found.');

                return;
            }

            // Checks if its a new request
            if (!$this->isNewRequest($data) && $this->section === $this->currentSection) {
                return;
            }

            $this->currentSection = $this->section;
            $this->refresh();

            (new General($this->input, $this->output))->render($data);

            switch ($this->section) {
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
        $this->output->newLine(2);
        $this->output->write(sprintf("\033\143"));
    }

    private function registerEvents()
    {
        $readline = $this->stdio->getReadline();
        $readline->setAutocomplete(function () {
            return ['messages', 'timeline', 'exceptions', 'route', 'queries', 'request', 'quit'];
        });
        $readline->setPrompt('> ');

        $this->stdio->on('data', function ($line) use ($readline) {
            $line = trim($line, "\r\n");
            $all = $readline->listHistory();
            if ($line !== '' && $line !== end($all)) {
                $readline->addHistory($line);
                $this->section = $line;
            }

            if (in_array($line, ['quit'])) {
                $this->stdio->end();
                $this->loop->stop();
            }
        });
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
