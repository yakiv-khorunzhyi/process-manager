<?php

declare(strict_types=1);

namespace Core;

class ProcessStatistics
{
    protected $info;

    public function setInfo(array $info): self
    {
        $this->info = [
            'processes' => $info['processes'],
            'finished'  => $info['finished'],
            'timeout'   => $info['timeout'],
            'failed'    => $info['failed'],
        ];

        return $this;
    }

    public function getQuantityStatisticsAsString(): string
    {
        return 'works: ' . count($this->info['processes'])
            . ', finished: ' . count($this->info['finished'])
            . ', timeout: ' . count($this->info['timeout'])
            . ', failed: ' . count($this->info['failed']);
    }

    //public function getFailedStatisticsAsString(): string
    //{
    //    $errorOutput = [];
    //
    //    foreach ($this->failed as &$process) {
    //        $errorOutput[] = $process->getErrorOutput();
    //    }
    //
    //    return implode(', ', $errorOutput);
    //}
}