<?php

namespace Madewithlove\LaravelDebugConsole\Renderers\Contracts;

interface RendererInterface
{
    /**
     * @param array $data
     */
    public function render(array $data);
}
