<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

use Symfony\Component\Console\Helper\Table;

class Query extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $queries = collect(array_get($data, 'queries.statements', []));

        // Retrieve grouped sql statements
        $rows = $queries
            ->map(function ($query, $index) {
                return [
                    $index,
                    array_get($query, 'sql'),
                    array_get($query, 'duration_str'),
                ];
            })
            ->all();

        $table = new Table($this->output);

        $this->output->writeln('Queries executed:');
        $table
            ->setHeaders([
                'N.',
                'SQL',
                'Duration',
            ])
            ->setRows($rows)
            ->render();

        $this->output->writeln('Queries summary:');
        $table
            ->setHeaders([
                'Total Number of Queries',
                'Total Execution Time',
            ])
            ->setRows([
                [
                    array_get($data, 'queries.nb_statements'),
                    array_get($data, 'queries.accumulated_duration_str')
                ]
            ])
            ->render();
    }
}
