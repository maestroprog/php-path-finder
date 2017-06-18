<?php

namespace Path;

use Path\ValueObject\LinkedPoint;

class PathHeap extends \SplHeap
{
    protected function compare($value1, $value2)
    {
        if (!$value1 instanceof LinkedPoint || !$value2 instanceof LinkedPoint) {
            throw new \LogicException('Некорректное использование кучи.');
        }
        return $value1->getFrom() === $value2->getTo()
            ? 0
            : (
            $value2->getTo() === $value2->getFrom() ? -1
                : 1
            );
    }
}
