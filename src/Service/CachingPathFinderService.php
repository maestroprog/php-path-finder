<?php

namespace Path\Service;

use Path\CacheInterface;
use Path\ConditionInterface;
use Path\PathFinderInterface;
use Path\ValueObject\PathResult;
use Path\ValueObject\Point;

/**
 * Кеширующий сервис-прослойка поиска маршрутов.
 * Если не находит данные в кеше - обращается к поиску внедрённого сервиса.
 *
 * В качестве зависимости требует сервис по-настоящему реализующий поиск маршрутов,
 * и компонент кеширующий данные.
 */
final class CachingPathFinderService implements PathFinderInterface
{
    private $pathFinder;
    private $cache;

    public function __construct(PathFinderInterface $pathFinder, CacheInterface $cache)
    {
        $this->pathFinder = $pathFinder;
        $this->cache = $cache;
    }

    public function getPath(Point $from, Point $to): PathResult
    {
        return $this->getPathWithConditions($from, $to, []);
    }

    public function getPathWithConditions(Point $from, Point $to, array $conditions): PathResult
    {
        $conditionsClasses = array_map(function (ConditionInterface $condition) {
            return get_class($condition);
        }, $conditions);
        $names = [$from->getName(), $to->getName()];
        $key = implode(':', array_merge($names, $conditionsClasses));

        $result = null;
        if ($this->cache->has($key)) {
            try {
                // перехватим исключение для бОльшей стабильности
                $result = $this->cache->get($key);
            } catch (\OutOfRangeException $e) {
                ;
            }
        }
        if (null === $result) {
            $result = $this->pathFinder->getPathWithConditions($from, $to, $conditions);
        }
        $this->cache->set($key, $result);

        return $result;
    }
}
