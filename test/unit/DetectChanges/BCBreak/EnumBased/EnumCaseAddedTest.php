<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\EnumBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\EnumBased\CaseAdded;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;

/** @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\EnumBased\CaseAdded */
final class EnumCaseAddedTest extends TestCase
{
    /**
     * @param string[] $expectedMessages
     *
     * @dataProvider enumsToBeTested
     */
    public function testDiffs(
        ReflectionClass $fromEnum,
        ReflectionClass $toEnum,
        array           $expectedMessages,
    ): void
    {
        $changes = (new CaseAdded())($fromEnum, $toEnum);

        self::assertSame(
            $expectedMessages,
            array_map(static function (Change $change): string {
                return $change->__toString();
            }, iterator_to_array($changes)),
        );
    }

    /**
     * @return array<string, array<int, ReflectionClass|array<int, string>>>
     * @psalm-return array<string, array{0: ReflectionClass, 1: ReflectionClass, 2: list<string>}>
     */
    public function enumsToBeTested()
    {
        $locator = (new BetterReflection())->astLocator();

        return [
            'RoaveTestAsset\\EnumWithCasesBeingAdded' => [
                (new DefaultReflector(new SingleFileSourceLocator(
                    __DIR__ . '/../../../../asset/api/old/EnumWithCasesBeingAdded.php',
                    $locator,
                )))->reflectClass('RoaveTestAsset\EnumWithCasesBeingAdded'),
                (new DefaultReflector(new SingleFileSourceLocator(
                    __DIR__ . '/../../../../asset/api/new/EnumWithCasesBeingAdded.php',
                    $locator,
                )))->reflectClass('RoaveTestAsset\EnumWithCasesBeingAdded'),
                [
                    '[BC] ADDED: Case RoaveTestAsset\EnumWithCasesBeingAdded::January was added',
                    '[BC] ADDED: Case RoaveTestAsset\EnumWithCasesBeingAdded::February was added',
                ],
            ],
        ];
    }
}
