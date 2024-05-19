<?php

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\EnumBased;

use Roave\BackwardCompatibility\Changes;
use Roave\BetterReflection\Reflection\ReflectionClass;

interface EnumBased
{
    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes;
}