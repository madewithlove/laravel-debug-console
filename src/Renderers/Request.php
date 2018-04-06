<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

use Symfony\Component\Console\Helper\Table;

class Request extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->output->writeln('Request information:');

        $table = new Table($this->output);
        $table->setHeaders([
            'Date and Time',
            'Total Memory',
            'Total Time',
        ])
        ->setRows([
            [
                array_get($data, '__meta.datetime'),
                array_get($data, 'memory.peak_usage_str'),
                array_get($data, 'time.duration_str')
            ],
        ])
        ->render();
    }
}
