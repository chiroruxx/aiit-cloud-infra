<?php

declare(strict_types=1);

namespace App;

use DomainException;

class ByteSize
{
    private static array $units = [
        'g' => 1024 ** 3,
        'm' => 1024 ** 2,
        'k' => 1024,
    ];

    public function __construct(private int $value)
    {
        if ($this->value < 0) {
            throw new DomainException('Invalid value.');
        }
    }

    public static function createWithUnit(string $withUnit): self
    {
        if (!self::validate($withUnit)) {
            throw new DomainException('Invalid size.');
        }

        [$givenValue, $givenUnit] = str_split($withUnit, strlen($withUnit) - 1);

        $value = $givenValue * self::$units[$givenUnit];

        return new self($value);
    }

    public static function validate(string $withUnit): bool
    {
        if (strlen($withUnit) < 2) {
            return false;
        }

        [$givenValue, $givenUnit] = str_split($withUnit, strlen($withUnit) - 1);

        if (!in_array($givenUnit, array_keys(self::$units), true)) {
            return false;
        }

        if (!filter_var($givenValue, FILTER_VALIDATE_INT)) {
            return false;
        }

        return true;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getWithUnit(): string
    {
        foreach (self::$units as $unit => $unitValue) {
            $divided = $this->value / $unitValue;
            if ($divided >= 1) {
                return floor($divided) . $unit;
            }
        }

        return '0' . array_key_last(self::$units);
    }

    public function lessThan(string $withUnit): bool
    {
        $another = self::createWithUnit($withUnit);

        return $this->value < $another->value;
    }
}
