<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Keboola\Component\Config\BaseConfig;

class Config extends BaseConfig
{
    public function getOperation(): string
    {
        return $this->getValue(['parameters', 'operation']);
    }

    public function getTimeout(): int
    {
        return (int) $this->getValue(['parameters', 'timeout'], null);
    }
}
