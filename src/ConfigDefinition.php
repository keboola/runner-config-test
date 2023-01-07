<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Keboola\Component\Config\BaseConfigDefinition;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $operations = [
            'application-error',
            'create-state',
            'child-jobs',
            'dump-config',
            'dump-env',
            'list',
            'sleep',
            'unsafe-dump-config',
            'user-error',
            'whoami',
        ];
        $parametersNode = parent::getParametersDefinition();
        // @formatter:off
        /** @noinspection NullPointerExceptionInspection */
        $parametersNode
            ->children()
                ->scalarNode('operation')
                    ->validate()
                        ->ifnotinarray($operations)
                        ->thenInvalid('Allowed operations are: ' . implode(', ', $operations))
                    ->end()
                ->end()
                ->scalarNode('timeout')->end()
                ->variableNode('arbitrary')->end()
                ->scalarNode('#token')->end()
                ->scalarNode('queueApiUrl')->end()
                ->scalarNode('childJobsCount')->end()
                ->scalarNode('logsTransport')->end()
                ->arrayNode('logs')
                    ->children()
                        ->enumNode('transport')
                            ->isRequired()
                            ->values(['tcp', 'udp', 'http'])
                        ->end()
                        ->arrayNode('records')
                            ->isRequired()
                            ->arrayPrototype()
                                ->children()
                                    ->enumNode('level')
                                        ->isRequired()
                                        ->values(array_keys(Logger::getLevels()))
                                        ->beforeNormalization()
                                            ->always(fn($v) => is_string($v) ? strtoupper($v) : $v)
                                        ->end()
                                    ->end()
                                    ->scalarNode('message')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->arrayNode('context')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
