<?php

declare(strict_types=1);

namespace Keboola\RunnerConfigTest\Tests;

use Keboola\RunnerStagingTest\Component;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class SyncActionsTest extends TestCase
{
    public function testActions(): void
    {
        $temp = sys_get_temp_dir() . uniqid('config-test');
        mkdir($temp);
        $configFile = json_encode([
            'parameters' => ['arbitrary' => ['a' => 'b']],
            'storage' => [
                'input' => [
                    'tables' => [],
                ],
            ],
        ]);
        file_put_contents($temp . '/config.json', $configFile);

        putenv('KBC_DATADIR=' . $temp);
        $component = new Component(new NullLogger());

        self::assertSame(
            '{"parameters":{"arbitrary":{"a":"b"}},"storage":{"input":{"tables":[]}}}',
            $component->dumpConfigAction()
        );
        $response = json_decode($component->dumpEnvAction(), true);
        self::assertIsArray($response);
        self::assertGreaterThan(0, count($response));
        // I'm not going to wait for this
        //self::assertSame('', $component->timeoutAction());
        self::assertSame('[]', $component->emptyJsonArrayAction());
        self::assertSame('{}', $component->emptyJsonObjectAction());
        self::assertSame('{"tables": ["a", "b", "c"]', $component->invalidJsonAction());
        self::assertSame('', $component->noResponseAction());
    }
}
