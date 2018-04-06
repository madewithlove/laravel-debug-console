<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

class Message extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->output->title('Messages');

        array_map(function ($message) {
            $label = array_get($message, 'label');
            $message = array_get($message, 'message', '');

            switch ($label) {
                case 'error':
                    $this->output->error($message);
                    break;
                case 'warning':
                    $this->output->warning($message);
                    break;
                case 'info':
                default;
                    $this->output->block($message, $label, 'fg=black;bg=blue', ' ', true);
            }

        }, array_get($data, 'messages.messages', []));
    }
}
