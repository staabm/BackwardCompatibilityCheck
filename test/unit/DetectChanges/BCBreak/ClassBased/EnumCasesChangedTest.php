<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\CasesChanged;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use stdClass;

use function array_map;
use function iterator_to_array;

final class EnumCasesChangedTest extends TestCase
{
    /**
     * @param string[] $expectedMessages
     *
     * @dataProvider enumsToBeTested
     */
    public function testDiffs(
        ReflectionClass $fromEnum,
        ReflectionClass $toEnum,
        array $expectedMessages,
    ): void {
        $changes = (new CasesChanged())($fromEnum, $toEnum);

        self::assertSame(
            $expectedMessages,
            array_map(static function (Change $change): string {
                return $change->__toString();
            }, iterator_to_array($changes)),
        );
    }

    public function testReturnsNoChangesIfOldEnumIsNotEnum(): void
    {
        // EnumCasesChanged should not be called when the old symbol is not an Enum. If it does it will
        // just return an empty list.

        $changes = (new CasesChanged())(
            ReflectionClass::createFromName(stdClass::class),
            ReflectionClass::createFromName(DummyEnum::class),
        );

        $this->assertSame(0, $changes->count());
    }

    public function testReturnsNoChangesIfNewEnumIsNotEnum(): void
    {
        // EnumCasesChanged should not be called when the old symbol is not an Enum. If it does it will
        // just return an empty list.

        $changes = (new CasesChanged())(
            ReflectionClass::createFromName(DummyEnum::class),
            ReflectionClass::createFromName(stdClass::class),
        );

        $this->assertSame(0, $changes->count());
    }

    /**
     * @return array<string, array<int, ReflectionClass|array<int, string>>>
     * @psalm-return array<string, array{0: ReflectionClass, 1: ReflectionClass, 2: list<string>}>
     */
    public function enumsToBeTested(): array
    {
        $locator = (new BetterReflection())->astLocator();

        return [
            'RoaveTestAsset\\EnumWithCasesBeingChanged' => [
                (new DefaultReflector(new SingleFileSourceLocator(
                    __DIR__ . '/../../../../asset/api/old/EnumWithCasesBeingChanged.php',
                    $locator,
                )))->reflectClass('RoaveTestAsset\EnumWithCasesBeingChanged'),
                (new DefaultReflector(new SingleFileSourceLocator(
                    __DIR__ . '/../../../../asset/api/new/EnumWithCasesBeingChanged.php',
                    $locator,
                )))->reflectClass('RoaveTestAsset\EnumWithCasesBeingChanged'),
                [
                    '[BC] REMOVED: Case RoaveTestAsset\EnumWithCasesBeingChanged::August was removed',
                    '[BC] ADDED: Case RoaveTestAsset\EnumWithCasesBeingChanged::January was added',
                    '[BC] ADDED: Case RoaveTestAsset\EnumWithCasesBeingChanged::February was added',
                ],
            ],
        ];
    }
}
