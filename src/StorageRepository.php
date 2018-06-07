<?php

namespace Madewithlove\LaravelDebugConsole;

use DebugBar\Storage\StorageInterface;

class StorageRepository
{
    /**
     * @var \DebugBar\Storage\StorageInterface
     */
    private $storage;

    /**
     * Repository constructor.
     *
     * @param \DebugBar\Storage\StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Returns an array with the latest stored laravel debug bar data.
     *
     * @return array
     */
    public function latest()
    {
        $file = $this->storage->find([], 1);
        $id = array_get($file, '0.id');

        return $this->storage->get($id);
    }
}
