<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

class General extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->title('General');

        $this->table([
            'Request Time',
            'Route',
            'Memory Usage',
            'Request Duration',
            'PHP Version',
        ], [
            [
                array_get($data, '__meta.datetime'),
                array_get($data, '__meta.method') . ' ' . array_get($data, '__meta.uri'),
                array_get($data, 'memory.peak_usage_str'),
                array_get($data, 'time.duration_str'),
                array_get($data, 'php.version')
            ],
        ]);
    }
}
