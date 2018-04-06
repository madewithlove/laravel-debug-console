<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

use Symfony\Component\Console\Helper\Table;

class General extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->output->title('General');

        $table = new Table($this->output);
        $table->setHeaders([
            'Request Time',
            'Route',
            'Memory Usage',
            'Request Duration',
            'PHP Version',
        ])
            ->setRows([
                [
                    array_get($data, '__meta.datetime'),
                    array_get($data, '__meta.method') . ' ' . array_get($data, '__meta.uri'),
                    array_get($data, 'memory.peak_usage_str'),
                    array_get($data, 'time.duration_str'),
                    array_get($data, 'php.version')
                ],
            ])
            ->render();
    }
}
