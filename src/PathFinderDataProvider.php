<?php

namespace Path;

use Path\ValueObject\PathProperties;
use Path\ValueObject\Point;

/**
 */
class PathFinderDataProvider implements \IteratorAggregate
{
    private $list;

    public function __construct()
    {
        $this->list = new \ArrayObject();
    }

    /**
     * Метод загружает данные в формате:
     * [
     *      ['Москва', 'Самара', 1000],
     *      ['Самара', 'Красноярск', 2000],
     *      ['Красноярск', 'Владивосток', 3000],
     *      ['Самара', 'Новосибирск', 1000],
     *      ['Новосибирск', 'Владивосток', 2000],
     * ]
     *
     * @param array $data
     */
    public function loadCosts(array $data)
    {
        foreach ($data as $row) {
            // добавление точек в список, если их там ещё нет
            if ($this->list->offsetExists($row[0])) {
                $pointFrom = $this->list->offsetGet($row[0]);
            } else {
                $pointFrom = new Point($row[0]);
                $this->list->offsetSet($pointFrom->getName(), $pointFrom);
            }
            if ($this->list->offsetExists($row[1])) {
                $pointTo = $this->list->offsetGet($row[1]);
            } else {
                $pointTo = new Point($row[1]);
                $this->list->offsetSet($pointTo->getName(), $pointTo);
            }
            // связывание двух точек, если они ещё не связаны
            $pointFrom->linkTo($pointTo, new PathProperties($row[2]));
        }
    }

    /**
     * Находит точку по названию,
     * если она была импортирована в провайдер данных.
     *
     * @param string $name
     * @return Point
     * @throws \OutOfBoundsException
     */
    public function findPointByName(string $name): Point
    {
        return $this->list->offsetGet($name);
    }

    public function getIterator()
    {
        return $this->list->getIterator();
    }
}
