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
use Madewithlove\LaravelDebugConsole\Terminal;
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
     * @var null|string
     */
    private $currentRequest = null;

    /**
     * @var integer
     */
    private $currentSection = 0;

    /**
     * @var string
     */
    private $displayedSection;

    /**
     * @var \Madewithlove\LaravelDebugConsole\StorageRepository
     */
    private $repository;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * @var \Madewithlove\LaravelDebugConsole\Terminal
     */
    private $terminal;

    /**
     * @param \Madewithlove\LaravelDebugConsole\StorageRepository $repository
     * @param \React\EventLoop\LoopInterface $loop
     * @param \Madewithlove\LaravelDebugConsole\Terminal $terminal
     */
    public function __construct(StorageRepository $repository, LoopInterface $loop, Terminal $terminal)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->loop = $loop;
        $this->terminal = $terminal;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $section = $this->argument('section');

        $sections = [
            'messages',
            'timeline',
            'exceptions',
            'route',
            'queries',
            'request',
        ];

        $this->terminal->registerKeyEvent(Terminal::KEY_LEFT, function () {
            if ($this->currentSection > 0) {
                --$this->currentSection;
            }
        });

        $this->terminal->registerKeyEvent(Terminal::KEY_RIGHT, function () use ($sections) {
            if ($this->currentSection < count($sections) - 1) {
                ++$this->currentSection;
            }
        });

        $this->loop->addPeriodicTimer(1, function () use ($sections) {
            $data = $this->repository->latest();
            $section = array_get($sections, $this->currentSection);

            // Checks if its a new request
            if (!$this->isNewSection($section) && !$this->isNewRequest($data)) {
                return;
            }

            $this->terminal->refresh();

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

            $this->displayedSection = $section;
        });

        $this->loop->run();
    }

    /**
     * @param string $section
     *
     * @return bool
     */
    private function isNewSection($section)
    {
        return $section !== $this->displayedSection;
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
