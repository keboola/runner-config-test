<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Keboola\Component\BaseComponent;
use Keboola\Component\UserException;
use Symfony\Component\Finder\Finder;

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
}
