<?php

declare(strict_types=1);

namespace Keboola\RunnerConfigTest\FunctionalTests;

use Keboola\DatadirTests\DatadirTestCase;
use Keboola\DatadirTests\DatadirTestSpecificationInterface;

class DatadirTest extends DatadirTestCase
{
    /**
     * @dataProvider provideDatadirSpecifications
     */
    public function testDatadir(DatadirTestSpecificationInterface $specification): void
    {
        if ($specification->getSourceDatadirDirectory() === '/code/tests/functional/child-jobs/source/data') {
            $this->markTestSkipped('child-jobs skipped - queue api is not public');
        }

        parent::testDatadir($specification);
    }
}
