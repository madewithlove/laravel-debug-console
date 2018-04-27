<?php

namespace Madewithlove\LaravelDebugConsole\Console;

use Illuminate\Console\Command;
use Madewithlove\LaravelDebugConsole\Renderers\RenderersFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
     * @var string
     */
    private $section;

    /**
     * @var array
     */
    private $sections;

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
        $this->section = $this->argument('section');
        $this->sections = RenderersFactory::create($this->input, $this->output);

        $this->loop->addPeriodicTimer(1, function () {
            try {
                $data = $this->repository->latest();
            } catch (FileNotFoundException $e) {
                $this->clear();
                $this->error('No laravel debugbar storage files found.');

                return;
            }

            // Checks if its a new request
            if (!$this->isNewRequest($data)) {
                return;
            }

            $this->clear();
            $this->renderScreen($data);
        });

        $this->loop->run();
    }

    /**
     * @param array $data
     */
    public function renderScreen(array $data)
    {
        array_get($this->sections, 'general')->render($data);
        if (array_has($this->sections, $this->section)) {
            array_get($this->sections, $this->section)->render($data);
        }
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

    private function clear()
    {
        passthru("echo '\033\143'");
    }
}
