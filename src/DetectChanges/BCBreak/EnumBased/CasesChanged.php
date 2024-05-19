<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\EnumBased;

use Psl\Regex;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionEnum;
use Roave\BetterReflection\Reflection\ReflectionEnumCase;

class CasesChanged implements EnumBased
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
            static function (ReflectionEnumCase $case) use ($fromEnum): bool {
                if (self::isInternalDocComment($case->getDocComment())) {
                    return false;
                }

                if ($fromEnum->hasCase($case->getName())) {
                    return false;
                }

                return true;
            }
        );

        $removedCases = array_filter(
            $fromEnum->getCases(),
            static fn(ReflectionEnumCase $case): bool => !$toEnum->hasCase($case->getName())
        );

        $caseRemovedChanges = array_values(array_map(function (ReflectionEnumCase $case) use ($fromEnumName): Change {
            $caseName = $case->getName();

            return Change::removed("Case {$fromEnumName}::{$caseName} was removed");
        },
            $removedCases
        ));

        $caseAddedChanges = array_values(array_map(function (ReflectionEnumCase $case) use ($fromEnumName): Change {
            $caseName = $case->getName();

            return Change::added("Case {$fromEnumName}::{$caseName} was added");
            },
            $addedCases
        ));

        return Changes::fromList(...$caseRemovedChanges, ...$caseAddedChanges);
    }

    /**
     * Copied from DetectChanges\BCBreak\ClassBased\ExcludeInternalClass - for now I'm not sure
     * if there's a good place to put a shared function, and following the 3 strike then refactor rule.
     */
    private static function isInternalDocComment(string|null $comment): bool
    {
        return $comment !== null
            && Regex\matches($comment, '/\s+@internal\s+/');
    }
};
