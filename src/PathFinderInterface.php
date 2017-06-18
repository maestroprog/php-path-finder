<?php

namespace Path;

use Path\ValueObject\PathResult;
use Path\ValueObject\Point;

interface PathFinderInterface
{
    /**
     * Ищет все возможные пути без условий, возвращает результат.
     *
     * @param Point $from
     * @param Point $to
     * @return PathResult
     * @throws \OutOfBoundsException Если не удалось найти путь
     */
    public function getPath(Point $from, Point $to): PathResult;

    /**
     * Ищет все возможные пути, с учетом указанных условий, возвращает результат.
     *
     * @param Point $from
     * @param Point $to
     * @param $conditions ConditionInterface[]
     * @return PathResult
     * @throws \OutOfBoundsException Если не удалось найти путь
     */
    public function getPathWithConditions(Point $from, Point $to, array $conditions): PathResult;
}
