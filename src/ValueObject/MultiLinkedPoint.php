<?php

namespace Path\ValueObject;

/**
 * Связь двух дальних точек (пунктов).
 * Логически является отрезком пути между двумя дальними точками,
 * включающим в себя возможные пути между ними, и имеющим определённые свойства.
 */
class MultiLinkedPoint extends LinkedPoint
{
    /**
     * @var Path[]
     */
    private $paths;

    /**
     * @param Point $from Точка отправления
     * @param Point $to Точка назначения
     * @param Path[] $paths Рассчитанные возможные полные пути между двумя дальними точками
     */
    public function __construct(Point $from, Point $to, array $paths)
    {
        foreach ($paths as $path) {
            if (!$path instanceof Path) {
                throw new \InvalidArgumentException('Invalid path type.');
            }
            $this->paths[] = $path;
        }
        parent::__construct($from, $to, $this->paths[0]->getProperties());
    }

    /**
     * @return Path[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }
}
