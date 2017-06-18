<?php

namespace Path\ValueObject;

/**
 * Связь двух ближних точек (пунктов).
 * Логически является отрезком пути, имеющим определённые свойства.
 */
class LinkedPoint
{
    private $from;
    private $to;
    private $properties;

    /**
     * @param Point $from Точка отправления
     * @param Point $to Точка назначения
     * @param PathProperties $properties Свойства отрезка пути между двумя точками
     */
    public function __construct(Point $from, Point $to, PathProperties $properties)
    {
        $this->from = $from;
        $this->to = $to;
        $this->properties = $properties;
    }

    public function getFrom(): Point
    {
        return $this->from;
    }

    public function getTo(): Point
    {
        return $this->to;
    }

    public function getProperties(): PathProperties
    {
        return $this->properties;
    }
}
