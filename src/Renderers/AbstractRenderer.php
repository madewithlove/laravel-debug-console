<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

use Madewithlove\LaravelDebugConsole\Renderers\Contracts\RendererInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractRenderer extends SymfonyStyle implements RendererInterface
{
    const TEXT_MAX_WITH = 80;
}
