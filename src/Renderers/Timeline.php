<?php

namespace Madewithlove\LaravelDebugConsole\Renderers;

class Timeline extends AbstractRenderer
{
    /**
     * @param array $data
     */
    public function render(array $data)
    {
        $this->output->title('Timeline');

        $duration = array_get($data, 'time.duration', 0) * 1000;

        foreach (array_get($data, 'time.measures', []) as $measure) {
            $this->output->comment(sprintf('%s (%s)', array_get($measure, 'label'), array_get($measure, 'duration_str')));

            $this->output->progressStart($duration);
            $this->output->progressAdvance(array_get($measure, 'duration') * 1000);

            $this->output->newLine(2);
        }
    }
}
