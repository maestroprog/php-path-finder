<?php

namespace Path\Condition;

use Path\ConditionInterface;
use Path\ValueObject\Path;

/**
 * Условие "Маршрут с самым дорогим ценником за проезд".
 */
final class HighPriceCondition implements ConditionInterface
{
    /**
     * @inheritdoc
     */
    public function check(Path $path): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function compare(Path $path1, Path $path2): int
    {
        return $path1->getProperties()->getFarePrice() <=> $path2->getProperties()->getFarePrice();
    }
}
