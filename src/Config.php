<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Keboola\Component\Config\BaseConfig;

/**
 * @phpstan-import-type LevelName from \Monolog\Logger
 */
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

    /**
     * @return array{
     *     transport: 'udp'|'tcp'|'http',
     *     records: array{
     *         level: LevelName,
     *         message: non-empty-string,
     *         context?: array<mixed>
     *     }[]
     * }
     */
    public function getLogs(): array
    {
        // @phpstan-ignore-next-line because getArrayValue returns generic array
        return $this->getArrayValue(['parameters', 'logs']);
    }
}
