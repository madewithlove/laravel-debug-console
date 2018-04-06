<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

class Exception extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->output->title('Exceptions');

        foreach (array_get($data, 'exceptions.exceptions', []) as $exception) {
            $this->output->block(array_get($exception, 'message'), array_get($exception, 'type'), 'fg=white;bg=red', ' ', true);
            $this->output->text(array_get($exception, 'file') . '#' . array_get($exception, 'line'));
            $this->output->newLine();
            $this->output->block(array_get($exception, 'surrounding_lines'), null, 'fg=yellow', ' ! ');
        }
    }
}
