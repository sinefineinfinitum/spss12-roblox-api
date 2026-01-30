<?php

declare(strict_types=1);

namespace SineFine\RobloxApi\Domain\Roblox\Id;

use InvalidArgumentException;

abstract class RobloxId
{
    private int $value;

    final public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('RobloxId must be positive');
        }
        $this->value = $value;
    }

    final public function value(): int
    {
        return $this->value;
    }

    final public function __toString(): string
    {
        return (string)$this->value;
    }
}