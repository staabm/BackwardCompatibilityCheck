<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility;

use Psl\Str;
use Throwable;

/** @psalm-immutable */
final class Change
{
    private const ADDED   = 'added';
    private const CHANGED = 'changed';
    private const REMOVED = 'removed';
    private const SKIPPED = 'skipped';

    /** @psalm-param self::* $modificationType */
    private function __construct(
        private string $modificationType,
        public string $description,
        private bool $isBcBreak,
        public string|null $file = null,
        public int|null $line = null,
        public int|null $column = null,
    ) {
    }

    /** @psalm-pure */
    public static function added(string $description, bool $isBcBreak = true): self
    {
        return new self(self::ADDED, $description, $isBcBreak);
    }

    /** @psalm-pure */
    public static function changed(string $description, bool $isBcBreak = true): self
    {
        return new self(self::CHANGED, $description, $isBcBreak);
    }

    /** @psalm-pure */
    public static function removed(string $description, bool $isBcBreak = true): self
    {
        return new self(self::REMOVED, $description, $isBcBreak);
    }

    /** @psalm-pure */
    public static function skippedDueToFailure(Throwable $failure): self
    {
        // @TODO Note: we may consider importing the full exception for better printing later on
        return new self(self::SKIPPED, $failure->getMessage(), true);
    }

    public function isAdded(): bool
    {
        return $this->modificationType === self::ADDED;
    }

    public function isRemoved(): bool
    {
        return $this->modificationType === self::REMOVED;
    }

    public function isChanged(): bool
    {
        return $this->modificationType === self::CHANGED;
    }

    public function isSkipped(): bool
    {
        return $this->modificationType === self::SKIPPED;
    }

    public function getModificationType(): string
    {
        return $this->modificationType;
    }

    /** @internal */
    public function withFilePositionsIfNotAlreadySet(
        string|null $file,
        int|null $line,
        int|null $column,
    ): self {
        $instance = clone $this;

        $instance->file   ??= $file;
        $instance->line   ??= $line;
        $instance->column ??= $column;

        return $instance;
    }

    public function onFile(string|null $file): self
    {
        $instance = clone $this;

        $instance->file = $file;

        return $instance;
    }

    public function onLine(int $line): self
    {
        $instance = clone $this;

        $instance->line = $line;

        return $instance;
    }

    public function onColumn(int|null $column): self
    {
        $instance = clone $this;

        $instance->column = $column;

        return $instance;
    }

    public function __toString(): string
    {
        return Str\format(
            '%s%s: %s',
            $this->isBcBreak ? '[BC] ' : '     ',
            Str\uppercase($this->modificationType),
            $this->description,
        );
    }
}
