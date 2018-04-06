<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

use Madewithlove\LaravelDebugConsole\Renderers\Contracts\RendererInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }
}
