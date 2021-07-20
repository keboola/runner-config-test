<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Keboola\Component\BaseComponent;
use Keboola\Component\UserException;
use Keboola\JobQueueClient\Client;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class Component extends BaseComponent
{
    protected function run(): void
    {
        /** @var Config $config */
        $config = $this->getConfig();
        $operation = $config->getOperation();
        $inputTablesDir = $this->getDataDir() . '/in/tables';

        switch ($operation) {
            case 'list':
                // list files in input
                $finder = new Finder();
                foreach ($finder->in($inputTablesDir)->sortByName() as $fileInfo) {
                    $this->getLogger()->info($fileInfo->getFilename());
                }
                break;
            case 'dump-config':
                $string = (string) file_get_contents($this->getDataDir() . '/config.json');
                $this->getLogger()->info('Config: ' . strtr($string, "\n\t\r", ' '));
                break;
            case 'unsafe-dump-config':
                $data = (string) file_get_contents($this->getDataDir() . '/config.json');
                $string = '';
                for ($i = 0; $i < strlen($data); $i++) {
                    $string .= trim($data[$i]) ? trim($data[$i]) . ' ' : '';
                }
                $this->getLogger()->info('Config: ' . $string);
                break;
            case 'sleep':
                sleep($config->getTimeout());
                $this->getLogger()->info(sprintf('Slept for "%s" seconds.', $config->getTimeout()));
                break;
            case 'dump-env':
                foreach (getenv() as $key => $value) {
                    $this->getLogger()->info(sprintf('Environment "%s" has value "%s".', $key, $value));
                }
                break;
            case 'child-jobs':
                $timeout = $config->getTimeout();
                $queueClient = new Client($this->getLogger(), $config->getQueueApiUrl(), $config->getToken());
                for ($i = 0; $i < $config->getChildJobsCount(); $i++) {
                    $job = $queueClient->createJob($this->createChildJobData($timeout));
                    $this->getLogger()->info(sprintf(
                        'Created child job "%s" with timeout "%s".',
                        $job['id'],
                        $timeout
                    ));
                }
                $this->getLogger()->info('Parent job finished.');
                break;
            case 'whoami':
                $process = new Process(['whoami']);
                $process->mustRun();
                $this->getLogger()->info(sprintf('Running under "%s" user.', trim($process->getOutput())));
                break;
            default:
                throw new UserException('Invalid operation');
        }
    }

    protected function getConfigClass(): string
    {
        return Config::class;
    }

    protected function getConfigDefinitionClass(): string
    {
        return ConfigDefinition::class;
    }

    private function createChildJobData(int $sleepSeconds): array
    {
        return [
            'component' => 'keboola.runner-config-test',
            'mode' => 'run',
            'configData' => [
                'parameters' => [
                    'operation' => 'sleep',
                    'timeout' => $sleepSeconds,
                ],
            ],
        ];
    }
}
