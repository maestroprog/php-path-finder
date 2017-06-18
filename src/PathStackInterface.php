<?php

namespace Path;

use Path\ValueObject\LinkedPoint;

interface PathStackInterface extends \Countable
{
    /**
     * Добавляет элемент пути в качестве последнего.
     *
     * @param LinkedPoint $point
     * @return mixed
     */
    public function push(LinkedPoint $point);

    /**
     * Достанет последний элемент пути, уберёт его из спика.
     * Вернёт этот элемент.
     *
     * @return LinkedPoint
     * @throws \OutOfBoundsException
     */
    public function pop(): LinkedPoint;

    /**
     * Вернёт текущий последний элемент пути.
     *
     * @return LinkedPoint
     */
    public function last(): LinkedPoint;
}
