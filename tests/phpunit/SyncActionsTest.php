<?php

declare(strict_types=1);

namespace Keboola\RunnerConfigTest\Tests;

use Keboola\RunnerStagingTest\Component;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class SyncActionsTest extends TestCase
{
    private function runAction(array $config): string
    {
        $temp = sys_get_temp_dir() . uniqid('config-test');
        mkdir($temp);
        $configFile = json_encode($config);
        file_put_contents($temp . '/config.json', $configFile);

        putenv('KBC_DATADIR=' . $temp);
        $component = new Component(new NullLogger());

        ob_start();
        $component->execute();
        return (string) ob_get_clean();
    }

    /** @dataProvider provideSyncActionWithOutput */
    public function testActionsOutput(string $actionName, string $expectedResult): void
    {
        $output = $this->runAction([
            'action' => $actionName,
            'parameters' => ['arbitrary' => ['a' => 'b']],
            'storage' => [
                'input' => [
                    'tables' => [],
                ],
            ],
        ]);

        self::assertSame($expectedResult, $output);
    }

    public function provideSyncActionWithOutput(): iterable
    {
        yield 'dump config' => [
            'action' => 'dumpConfig',
            'result' =>
                '{"action":"dumpConfig","parameters":{"arbitrary":{"a":"b"}},"storage":{"input":{"tables":[]}}}',
        ];

        yield 'empty json array' => [
            'action' => 'emptyJsonArray',
            'result' => '[]',
        ];

        yield 'empty json object' => [
            'action' => 'emptyJsonObject',
            'result' => '{}',
        ];

        yield 'invalid json' => [
            'action' => 'invalidJson',
            'result' => '{"tables": ["a", "b", "c"]',
        ];

        yield 'no response' => [
            'action' => 'noResponse',
            'result' => '',
        ];
    }

    public function testDumpEnvAction(): void
    {
        $output = $this->runAction([
            'action' => 'dumpEnv',
            'parameters' => ['arbitrary' => ['a' => 'b']],
            'storage' => [
                'input' => [
                    'tables' => [],
                ],
            ],
        ]);

        $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($data);
        self::assertGreaterThan(0, count($data));
    }

    public function testTimeoutAction(): void
    {
        $startTime = microtime(true);

        $output = $this->runAction([
            'action' => 'timeout',
            'parameters' => [],
            'storage' => [
                'input' => [
                    'tables' => [],
                ],
            ],
        ]);

        $endTime = microtime(true);

        self::assertSame('', $output);
        self::assertEqualsWithDelta(60, $endTime - $startTime, 1);
    }
}
