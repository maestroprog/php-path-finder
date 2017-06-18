<?php

namespace Path\ValueObject;

use Path\PathFinderInterface;
use Path\PathStackInterface;

/**
 * Путь состоит из отрезков, через котрые нужно "пройти",
 * и свойств данного пути.
 */
class Path implements PathStackInterface
{
    private $properties;
    private $points = [];

    /**
     * Создаёт новый объект пути с указанными отрезками пути,
     * отсортированные в порядке следования по маршруту.
     *
     * @param LinkedPoint[] $points
     * @return self
     */
    public static function create(array $points = []): self
    {
        return (new self())->loadPoints($points);
    }

    /**
     * Конструктор публичный, для возможности создания объекта с указанием списка отрезков.
     *
     * @param LinkedPoint[] ...$points
     */
    public function __construct(LinkedPoint ...$points)
    {
        $this->points = new \SplObjectStorage();
        $this->loadPoints($points);
        $this->calculateProperties();
    }

    /**
     * Вернёт обобщённые свойства пути.
     *
     * @return PathProperties
     */
    public function getProperties(): PathProperties
    {
        return $this->properties;
    }

    /**
     * Вернёт в виде массива все точки @see Point,
     * отсортированныев порядке продвижения по маршруту.
     *
     * @return Point[]
     */
    public function getPoints(): array
    {
        $result = [];
        foreach ($this->points as $i => $linkedPoint) {
            /**
             * @var $linkedPoint LinkedPoint
             */
            $result[] = $linkedPoint->getFrom();
        }
        if (isset($linkedPoint)) {
            $result[] = $linkedPoint->getTo();
        }
        return $result;
    }

    /**
     * При клонировании поля-объекты `points` и `properties`
     * не должны ссылаться на изначально созданные объекты,
     * для исключения влияния со стороны @see PathFinderInterface
     * когда клонируется новый новый подходящий маршрут
     * для отдельного хранения в отдельной переменной.
     */
    public function __clone()
    {
        $this->points = clone $this->points;
        $this->properties = clone $this->properties;
    }

    /**
     * @inheritdoc
     */
    public function push(LinkedPoint $point)
    {
        $this->points->attach($point);
        $this->calculateProperties();
    }

    /**
     * @inheritdoc
     */
    public function pop(): LinkedPoint
    {
        $point = $this->last();
        $this->points->detach($point);
        $this->calculateProperties();
        return $point;
    }

    /**
     * @inheritdoc
     */
    public function last(): LinkedPoint
    {
        if (!$this->points->valid()) {
            $this->points->rewind();
        }
        $count = $this->points->count() - 1;
        for ($i = $this->points->key(); $i < $count; $i++) {
            $this->points->next();
        }
        $point = $this->points->current();
        return $point;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->points->count();
    }

    /**
     * Включает ли в себя данный путь прохождение через указанную точку.
     *
     * @param Point $point
     * @return bool
     */
    public function hasPoint(Point $point): bool
    {
        foreach ($this->points as $linkedPoint) {
            if ($linkedPoint->getFrom() === $point || $linkedPoint->getTo() === $point) {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверяет, входит ли указанная дистанция в общий путь.
     *
     * @param LinkedPoint $point
     * @return bool
     */
    public function hasLinkedPoint(LinkedPoint $point): bool
    {
        return $this->points->contains($point);
    }

    /**
     * Загружает новые отрезки пути, заменяя старые.
     *
     * @param LinkedPoint[] $points
     * @return Path
     */
    private function loadPoints(array $points): self
    {
        $this->points->removeAllExcept(new \SplObjectStorage());
        foreach ($points as $linkedPoint) {
            $this->points->attach($linkedPoint);
        }
        $this->calculateProperties();
        return $this;
    }

    /**
     * Пересчитывает свойства пути, такие как стоимость, и прочее.
     * Метод должен вызываться каждый раз, когда происходит изменение пути:
     * добавление нового отрезка, удаление старого, и т.д.
     *
     * @return void
     */
    protected function calculateProperties()
    {
        /**
         * @var $calculatedProps PathProperties
         */
        $calculatedProps = null;
        foreach ($this->points as $linkedPoint) {
            /**
             * @var $linkedPoint LinkedPoint
             */
            if (is_null($calculatedProps)) {
                $calculatedProps = $linkedPoint->getProperties();
            } else {
                $calculatedProps = $calculatedProps->combineWith($linkedPoint->getProperties());
            }
        }
        $this->properties = $calculatedProps;
    }
}
