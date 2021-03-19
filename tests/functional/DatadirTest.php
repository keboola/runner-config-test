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
        if ('/code/tests/functional/child-jobs/source/data' === $specification->getSourceDatadirDirectory()) {
            $this->markTestSkipped('child-jobs skipped - queue api is not public');
        }

        parent::testDatadir($specification);
    }
}
