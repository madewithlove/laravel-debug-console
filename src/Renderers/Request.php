<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

class Request extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->title('Route');

        $request = array_get($data, 'request', []);

        $this->table([], array_map(function ($value, $index) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            return [$index, str_limit($value, 200)];
        }, array_values($request), array_keys($request)));
    }
}
