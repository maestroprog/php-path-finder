<?php

namespace Path;

use Path\ValueObject\Path;

/**
 * Интерфейс проверки маршртута неким требованиям.
 */
interface ConditionInterface
{
    /**
     * Метод, проверяющий на соответствие свойств маршрута заявленным требованиям.
     * Должен вернуть true, если маршрут подходит, иначе false.
     *
     * @param Path $path
     * @return bool
     */
    public function check(Path $path): bool;

    /**
     * Если несколько маршрутов соответствуют определённым требованиям,
     * эта функция поможет определить, какой маршрут является более подходящим.
     * Если `$path1` лучше, чем `$path2`, то вернёт `1`;
     * Если `$path2` лучше, чем `$path1`, то вернёт `-1`;
     * Вернёт `0`, если одинаково подходят и `$path1` и `$path2`.
     *
     * @param Path $path1
     * @param Path $path2
     * @return int
     */
    public function compare(Path $path1, Path $path2): int;
}
