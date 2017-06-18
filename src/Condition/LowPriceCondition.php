<?php

namespace Path\Condition;

use Path\ConditionInterface;
use Path\ValueObject\Path;

/**
 * Условие "Маршрут с самым дешёвым ценником за проезд".
 */
final class LowPriceCondition implements ConditionInterface
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
        return $path2->getProperties()->getFarePrice() <=> $path1->getProperties()->getFarePrice();
    }
}
