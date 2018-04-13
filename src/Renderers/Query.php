<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

class Query extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $queries = collect(array_get($data, 'queries.statements', []));
        $message = sprintf(
            '%s statements were executed in %s',
            array_get($data, 'queries.nb_statements'),
            array_get($data, 'queries.accumulated_duration_str')
        );

        $this->title('Queries');
        $this->writeln($message);
        $this->newLine();

        $this->table([
            'SQL',
            'Duration',
            'Statement ID',
            'connection',
        ], $queries->map(function ($query, $index) {
            return [
                array_get($query, 'sql'),
                array_get($query, 'duration_str'),
                array_get($query, 'stmt_id'),
                str_limit(array_get($query, 'connection'), 20),
            ];
        })->all());
    }
}
