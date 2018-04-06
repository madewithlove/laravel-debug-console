<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

class Route extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->output->title('Route');

        $route = array_get($data, 'route', []);

        $this->output->table([], array_map(function ($value, $index) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            return [$index, str_limit($value, 200)];
        }, array_values($route), array_keys($route)));
    }
}
