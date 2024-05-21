<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Psl\Regex;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionEnum;
use Roave\BetterReflection\Reflection\ReflectionEnumCase;

use function array_filter;
use function array_map;

class CasesChanged implements ClassBased
{
    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toEnum): Changes
    {
        if (! $fromClass instanceof ReflectionEnum) {
            return Changes::empty();
        }

        if (! $toEnum instanceof ReflectionEnum) {
            return Changes::empty();
        }

        $fromEnumName = $fromClass->getName();

        $addedCases = array_filter(
            $toEnum->getCases(),
            static function (ReflectionEnumCase $case) use ($fromClass): bool {
                if (self::isInternalDocComment($case->getDocComment())) {
                    return false;
                }

                return ! $fromClass->hasCase($case->getName());
            },
        );


        $removedCases = array_filter(
            $fromClass->getCases(),
            static function (ReflectionEnumCase $case) use ($toEnum): bool {
                if (self::isInternalDocComment($case->getDocComment())) {
                    return false;
                }

                return ! $toEnum->hasCase($case->getName());
            },
        );

        $internalisedCases = array_filter(
            $toEnum->getCases(),
            static function (ReflectionEnumCase $case) use ($fromClass) {
                if (! self::isInternalDocComment($case->getDocComment())) {
                    return false;
                }

                if (! $fromClass->hasCase($case->getName())) {
                    return false;
                };

                return ! self::isInternalDocComment($fromClass->getCase($case->getName())->getDocComment());
            }
        );

        $caseRemovedChanges = array_map(
            static function (ReflectionEnumCase $case) use ($fromEnumName): Change {
                $caseName = $case->getName();

                return Change::removed('Case ' . $fromEnumName . '::' . $caseName . ' was removed');
            },
            $removedCases,
        );

        $caseAddedChanges = array_map(
            static function (ReflectionEnumCase $case) use ($fromEnumName): Change {
                $caseName = $case->getName();

                return Change::added('Case ' . $fromEnumName . '::' . $caseName . ' was added');
            },
            $addedCases,
        );

        $caseBecameInternalChanges = array_map(
            static function (ReflectionEnumCase $case) use ($fromEnumName): Change {
                $caseName = $case->getName();

                return Change::changed('Case ' . $fromEnumName . '::' . $caseName . ' was marked "@internal"');
            },
            $internalisedCases,
        );

        return Changes::fromList(...$caseRemovedChanges, ...$caseAddedChanges, ...$caseBecameInternalChanges);
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
}
