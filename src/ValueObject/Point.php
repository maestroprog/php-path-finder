<?php

namespace Path\ValueObject;

/**
 * Точка, может быть как пунктом отправления,
 * так и пунктом назначения.
 */
class Point
{
    private $name;
    private $links;
    private $multiLinks;

    /**
     * @param string $name Название пункта
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->links = new \ArrayObject();
        $this->multiLinks = new \ArrayObject();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Связывает начальную точку с конечной.
     * Если точки уже связаны - ничего не делает.
     *
     * @param Point $point
     * @param PathProperties $properties
     */
    public function linkTo(Point $point, PathProperties $properties)
    {
        if (!$this->links->offsetExists($point->getName())) {
            $this->links->offsetSet($point->getName(), new LinkedPoint($this, $point, $properties));
        }
    }

    /**
     * Добавляет внешнюю связь с другой точкой.
     *
     * @param MultiLinkedPoint $linkedPoint
     * @throws \LogicException
     */
    public function linkMulti(MultiLinkedPoint $linkedPoint)
    {
        if ($linkedPoint->getFrom() !== $this) {
            throw new \LogicException('Предъявлена не настоящая точка "From".');
        }
        if (!$this->links->offsetExists($linkedPoint->getTo()->getName())) {
            $this->links->offsetSet($linkedPoint->getTo()->getName(), $linkedPoint);
        }
    }

    /**
     * Проверяет, привязана ли указанная точка к текущему объекту.
     *
     * @param Point $point
     * @return bool
     */
    public function isExistsLinkTo(Point $point): bool
    {
        return $this->links->offsetExists($point->getName());
    }

    /**
     * Возвращает связь текущей точки с указанной.
     *
     * @param Point $point
     * @return LinkedPoint
     * @throws \OutOfBoundsException
     */
    public function getLinkByFromPoint(Point $point): LinkedPoint
    {
        return $this->links->offsetGet($point->getName());
    }

    /**
     * Возвращает генератор для перебора всех связанных точек.
     *
     * @return \Generator
     */
    public function getLinkedPoints(): \Generator
    {
        foreach ($this->links as $linkedPoint) {
            yield $linkedPoint;
        }
    }
}
