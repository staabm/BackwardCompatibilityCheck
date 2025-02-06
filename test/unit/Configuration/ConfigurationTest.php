<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\Configuration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Baseline;
use Roave\BackwardCompatibility\Configuration\Configuration;

#[CoversClass(Configuration::class)]
final class ConfigurationTest extends TestCase
{
    public function testBaselineShouldBeEmptyForDefaultConfiguration(): void
    {
        $config = Configuration::default();

        self::assertEquals(Baseline::empty(), $config->baseline);
        self::assertNull($config->filename);
    }
}
