<?php

namespace Madewithlove\LaravelDebugConsole;

use Clue\React\Stdio\Stdio;

class Terminal
{
    const KEY_UP = "\033[A";
    const KEY_DOWN = "\033[B";
    const KEY_LEFT = "\033[D";
    const KEY_RIGHT = "\033[C";

    /**
     * @var \Clue\React\Stdio\Stdio
     */
    private $stdio;

    /**
     * @var string
     */
    private $keyBeingPressed = false;

    /**
     * @param \Clue\React\Stdio\Stdio $stdio
     */
    public function __construct(Stdio $stdio)
    {
        $this->stdio = $stdio;
    }

    /**
     * @param string $key
     * @param callable $event
     */
    public function registerKeyEvent($key, callable $event)
    {
        $this->stdio->getReadline()->on($key, $event);
    }

    /**
     * Refreshes the screen
     */
    public function refresh()
    {
        // @todo: find a better way to do this
        system('clear');
    }
}