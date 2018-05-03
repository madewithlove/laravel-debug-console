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
                wordwrap(array_get($query, 'sql'), self::TEXT_MAX_WITH),
                array_get($query, 'duration_str'),
                basename(array_get($query, 'stmt_id')),
                basename(array_get($query, 'connection')),
            ];
        })->all());
    }
}
