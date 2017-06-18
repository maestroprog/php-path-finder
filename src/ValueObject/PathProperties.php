<?php

namespace Path\ValueObject;

/**
 * Свойства отрезка пути или всего пути.
 */
class PathProperties
{
    private $farePrice;

    /**
     * @param int $farePrice Стоимость проезда
     */
    public function __construct(int $farePrice)
    {
        $this->farePrice = $farePrice;
    }

    /**
     * Комбинирует текущие свойства с указанным в аргументе.
     * Вернёт новый объект свойств.
     *
     * @param PathProperties $properties
     * @return PathProperties
     */
    public function combineWith(PathProperties $properties): self
    {
        return new self($this->farePrice + $properties->getFarePrice());
    }

    public function getFarePrice(): int
    {
        return $this->farePrice;
    }
}
