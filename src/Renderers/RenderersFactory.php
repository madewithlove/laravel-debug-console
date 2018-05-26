<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenderersFactory
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return array
     */
    public static function create(InputInterface $input, OutputInterface $output)
    {
        return [
            'general' => new General($input, $output),
            'messages' => new Message($input, $output),
            'timeline' => new Timeline($input, $output),
            'exceptions' => new Exception($input, $output),
            'route' => new Route($input, $output),
            'queries' => new Query($input, $output),
            'request' => new Request($input, $output),
        ];
    }
}
