<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Gelf\Transport\HttpTransport;
use Gelf\Transport\TcpTransport;
use Gelf\Transport\UdpTransport;
use Keboola\Component\BaseComponent;
use Keboola\Component\Exception\BaseComponentException;
use Keboola\Component\UserException;
use Keboola\JobQueueClient\Client;
use Keboola\JobQueueClient\JobData;
use Monolog\Handler\GelfHandler;
use Monolog\Logger as MonologLogger;
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
                $queueClient = new Client(
                    $config->getQueueApiUrl(),
                    $config->getToken(),
                    ['logger' => $this->getLogger()]
                );
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

    private function createChildJobData(int $sleepSeconds): JobData
    {
        return new JobData(
            'keboola.runner-config-test',
            null,
            [
                'parameters' => [
                    'operation' => 'sleep',
                    'timeout' => $sleepSeconds,
                ],
            ],
            'run',
        );
    }

    public function getSyncActions(): array
    {
        return [
            'dumpConfig' => 'dumpConfigAction',
            'dumpEnv' => 'dumpEnvAction',
            'timeout' => 'timeoutAction',
            'emptyJsonArray' => 'timeoutAction',
            'emptyJsonObject' => 'emptyJsonObjectAction',
            'invalidJson' => 'invalidJsonAction',
            'noResponse' => 'noResponseAction',
            'userError' => 'userErrorAction',
            'applicationError' => 'applicationErrorAction',
            'printLogs' => 'printLogsAction',
        ];
    }

    public function dumpConfigAction(): string
    {
        return (string) file_get_contents($this->getDataDir() . '/config.json');
    }

    public function dumpEnvAction(): string
    {
        return (string) json_encode(getenv());
    }

    public function timeoutAction(): string
    {
        sleep(60);
        return '';
    }

    public function emptyJsonArrayAction(): string
    {
        return '[]';
    }

    public function emptyJsonObjectAction(): string
    {
        return '{}';
    }

    public function invalidJsonAction(): string
    {
        return '{"tables": ["a", "b", "c"]';
    }

    public function noResponseAction(): string
    {
        return '';
    }

    public function userErrorAction(): string
    {
        print('User Error');
        exit(1);
    }

    public function applicationErrorAction(): string
    {
        print('Application Error');
        exit(2);
    }

    public function printLogsAction(): string
    {
        /** @var Config $config */
        $config = $this->getConfig();
        $logs = $config->getLogs();

        $gelfPublisher = match ($logs['transport']) {
            'udp' => new UdpTransport((string) getenv('KBC_LOGGER_ADDR'), (int) getenv('KBC_LOGGER_PORT')),
            'tcp' => new TcpTransport((string) getenv('KBC_LOGGER_ADDR'), (int) getenv('KBC_LOGGER_PORT')),
            'http' => new HttpTransport((string) getenv('KBC_LOGGER_ADDR'), (int) getenv('KBC_LOGGER_PORT')),
        };

        $gelfLogsHandler = new GelfHandler($gelfPublisher);
        $gelfLogger = new MonologLogger('app', [$gelfLogsHandler]);

        $logLevels = MonologLogger::getLevels();
        foreach ($logs['records'] as $log) {
            $gelfLogger->addRecord($logLevels[$log['level']], $log['message'], $log['context'] ?? []);
        }

        return '{}';
    }

    // method is overridden so that we can produce raw output
    public function execute(): void
    {
        if (!$this->isSyncAction()) {
            $this->run();
            return;
        }

        $action = $this->getConfig()->getAction();
        $syncActions = $this->getSyncActions();
        if (array_key_exists($action, $syncActions)) {
            $method = $syncActions[$action];
            echo $this->$method();
        } else {
            throw BaseComponentException::invalidSyncAction($action);
        }
    }
}
