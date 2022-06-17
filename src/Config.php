<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Keboola\Component\Config\BaseConfig;

class Config extends BaseConfig
{
    public function getOperation(): string
    {
        return $this->getStringValue(['parameters', 'operation']);
    }

    public function getTimeout(): int
    {
        return $this->getIntValue(['parameters', 'timeout'], null);
    }

    public function getToken(): string
    {
        return $this->getStringValue(['parameters', '#token']);
    }

    public function getQueueApiUrl(): string
    {
        return $this->getStringValue(['parameters', 'queueApiUrl']);
    }

    public function getChildJobsCount(): int
    {
        return $this->getIntValue(['parameters', 'childJobsCount']);
    }
}
