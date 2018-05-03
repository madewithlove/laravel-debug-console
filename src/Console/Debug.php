<?php

namespace Madewithlove\LaravelDebugConsole\Console;

use Clue\React\Stdio\Stdio;
use Illuminate\Console\Command;
use Madewithlove\LaravelDebugConsole\Renderers\RenderersFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
     * @var \Clue\React\Stdio\Stdio
     */
    private $stdio;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * @var null|string
     */
    private $currentRequest = null;

    /**
     * @var string
     */
    private $section;

    /**
     * @var string
     */
    private $renderedSection;

    /**
     * @var array
     */
    private $sections;

    /**
     * @param \Madewithlove\LaravelDebugConsole\StorageRepository $repository
     * @param \React\EventLoop\LoopInterface $loop
     * @param \Clue\React\Stdio\Stdio $stdio
     */
    public function __construct(StorageRepository $repository, LoopInterface $loop, Stdio $stdio)
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
        $this->sections = RenderersFactory::create($this->input, $this->output);
        $this->registerReadLineEvent();

        $this->loop->addPeriodicTimer(1, function () {
            try {
                $data = $this->repository->latest();
            } catch (FileNotFoundException $e) {
                $this->clear();
                $this->error('No laravel debugbar storage files found.');

                return;
            }

            // Checks if its a new request
            if (!$this->isNewData($data) && !$this->isNewSection()) {
                return;
            }

            $this->clear();
            $this->renderScreen($data);
            $this->clearPrompt();
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

        $this->renderedSection = $this->section;
    }

    /**
     * @return bool
     */
    public function isNewSection()
    {
        return $this->renderedSection !== $this->section;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function isNewData(array $data)
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
        $this->output->write(sprintf("\033\143"));
    }

    private function clearPrompt()
    {
        $this->output->newLine();

        $this->stdio->getReadline()->setPrompt(' > ');
    }

    private function registerReadLineEvent()
    {
        $readLine = $this->stdio->getReadline();
        $readLine->setAutocomplete(function () {
            return $this->getSectionOptions();
        });

        $this->stdio->on('data', function ($line) {
            $line = trim($line);
            if (!in_array($line, $this->getSectionOptions())) {
                $this->output->newLine();
                $this->error('Invalid option.');

                return;
            }

            $this->section = $line;
            $this->renderedSection = null;
        });
    }

    /**
     * @return array
     */
    private function getSectionOptions()
    {
        return array_filter(array_keys($this->sections), function ($section) {
            return !in_array($section, ['general']);
        });
    }
}
