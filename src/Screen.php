<?php

namespace Madewithlove\LaravelDebugConsole;

use Illuminate\Support\Collection;
use Madewithlove\LaravelDebugConsole\Renderers\Contracts\RendererInterface;

class Screen
{
    /**
     * @var \Madewithlove\LaravelDebugConsole\StorageRepository
     */
    private $repository;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $renderers;

    /***
     * @param \Madewithlove\LaravelDebugConsole\StorageRepository $repository
     */
    public function __construct(StorageRepository $repository)
    {
        $this->repository = $repository;
        $this->renderers = new Collection();
    }

    public function display()
    {
        $data = $this->repository->latest();

        // Render screen
        $this->renderers->get('header')->each(function (RendererInterface $renderer) use ($data) {
            $renderer->render($data);
        });
        $this->renderers->get('body')->get('messages')->render($data);
    }

    /**
     * @param string $name
     * @param \Madewithlove\LaravelDebugConsole\Renderers\Contracts\RendererInterface $renderer
     * @param string $group
     */
    public function registerRenderer($name, RendererInterface $renderer, $group = 'body')
    {
        if (!$this->renderers->has($group)) {
            $this->renderers->put($group, new Collection());
        }

        $this->renderers->put($group, $this->renderers->get($group)->put($name, $renderer));
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