<?php

namespace Path\ValueObject;

/**
 * Результат поиска маршрута.
 * Может содержать как один, так и несколько маршрутов.
 */
class PathResult implements \Countable
{
    private $from;
    private $to;
    private $pathList = [];

    /**
     * @param Point $from Пункт отправления
     * @param Point $to Пункт назначения
     * @param Path[] $paths Найденные пути
     */
    public function __construct(Point $from, Point $to, array $paths = [])
    {
        $this->from = $from;
        $this->to = $to;
        foreach ($paths as $path) {
            if (!$path instanceof Path) {
                throw new \InvalidArgumentException('Invalid path type.');
            }
        }
        $this->pathList = $paths;
    }

    /**
     * Добавляет новый найденный путь в результат.
     *
     * @param Path $path
     * @return self
     */
    public function addPath(Path $path): self
    {
        $this->pathList[] = $path;
        return $this;
    }

    /**
     * Удаляет указанный путь из результатов.
     *
     * @param Path $path
     */
    public function removePath(Path $path)
    {
        foreach ($this->pathList as $key => $value) {
            if ($value === $path) {
                unset($this->pathList[$key]);
                break;
            }
        }
    }

    /**
     * Обнуляет список найденных путей.
     *
     * @return void
     */
    public function clear()
    {
        $this->pathList = [];
    }

    /**
     * Вернёт точку отправления.
     *
     * @return Point
     */
    public function getFrom(): Point
    {
        return $this->from;
    }

    /**
     * Вернёт точку назначения.
     *
     * @return Point
     */
    public function getTo(): Point
    {
        return $this->to;
    }

    /**
     * Вернёт список путей.
     *
     * @return Path[]
     */
    public function getPathList(): array
    {
        return $this->pathList;
    }

    /**
     * Вернёт количество найденных путей.
     *
     * @return int
     */
    public function count()
    {
        return count($this->pathList);
    }
}
