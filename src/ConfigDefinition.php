<?php

declare(strict_types=1);

namespace Keboola\RunnerStagingTest;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $operations = ['list', 'dump-config', 'unsafe-dump-config', 'sleep', 'dump-env'];
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
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
