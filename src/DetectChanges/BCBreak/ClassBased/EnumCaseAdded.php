<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionEnum;
use Roave\BetterReflection\Reflection\ReflectionEnumCase;

class EnumCaseAdded implements ClassBased
{
    public function __invoke(ReflectionClass $fromEnum, ReflectionClass $toEnum): Changes
    {
        if (! $fromEnum instanceof ReflectionEnum) {
            return Changes::empty();
        }

        if (! $toEnum instanceof ReflectionEnum) {
            return Changes::empty();
        }

        $fromEnumName = $fromEnum->getName();

        $addedCases = array_filter(
            $toEnum->getCases(),
            static fn(ReflectionEnumCase $case): bool => (! $fromEnum->hasCase($case->getName()))
        );

        $changes = array_map(function (ReflectionEnumCase $case) use ($fromEnumName): Change {
            $caseName = $case->getName();

            return Change::added("Case {$fromEnumName}::{$caseName} was added");
            },
            $addedCases
        );

        return Changes::fromList(...$changes);
    }
};
