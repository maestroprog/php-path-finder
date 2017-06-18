<?php

namespace Path\Service;

use Path\ConditionInterface;
use Path\PathFinderDataProvider;
use Path\PathFinderInterface;
use Path\ValueObject\LinkedPoint;
use Path\ValueObject\Path;
use Path\ValueObject\PathResult;
use Path\ValueObject\Point;

/**
 * Сервис, который ищет самые разнообразные не повторяющиеся пути от точки старта до точки назначения.
 */
final class PathFinderService implements PathFinderInterface
{
    private $dataProvider;

    public function __construct(PathFinderDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    private function log($msg)
    {
//        echo $msg, PHP_EOL;
    }

    public function getPath(Point $from, Point $to): PathResult
    {
        return $this->algorithm($from, $to, []);
    }

    /**
     * @inheritdoc
     * @param $conditions ConditionInterface[]
     */
    public function getPathWithConditions(Point $from, Point $to, array $conditions): PathResult
    {
        return $this->algorithm($from, $to, $conditions);
    }

    /**
     * Реализация алгоритма поиска пути с использованием условий для поиска.
     *
     * @param $from Point
     * @param $to Point
     * @param $conditions ConditionInterface[]
     * @return PathResult
     */
    private function algorithm(Point $from, Point $to, array $conditions): PathResult
    {
        $result = new PathResult($from, $to);
        $nicePath = null;
        $currentPoint = $from;
        $currentPath = Path::create();
        $see = new \SplObjectStorage();

        $this->log('Начинаем с ' . $currentPoint->getName());

        for (; ;) {
            $citySelected = false;
            if ($currentPoint->getName() !== $to->getName()) {
                foreach ($currentPoint->getLinkedPoints() as $linkedPoint) {
                    /**
                     * @var LinkedPoint $linkedPoint
                     */
                    if (
                        !$see->contains($linkedPoint)
                        && !$currentPath->hasPoint($linkedPoint->getTo())
                    ) {

                        $this->log('Переходим в ' . $linkedPoint->getTo()->getName());

                        $this->log('Ставим метку пути ' . $linkedPoint->getFrom()->getName() . ' > ' . $linkedPoint->getTo()->getName());
                        $see->attach($linkedPoint);
                        if ($linkedPoint->getTo()->isExistsLinkTo($linkedPoint->getFrom())) {
                            // метка, чтобы не идти по обратному пути, если он есть
                            $revLink = $linkedPoint->getTo()->getLinkByFromPoint($linkedPoint->getFrom());
                            $this->log('Ставим метку обратного пути (ед) ' . $revLink->getFrom()->getName() . ' > ' . $revLink->getTo()->getName());
                            $see->attach($linkedPoint->getTo()->getLinkByFromPoint($linkedPoint->getFrom()));
                        }
                        $currentPath->push($linkedPoint);
                        $currentPoint = $linkedPoint->getTo();
                        $citySelected = true;
                        break;
                    }
                    unset($data);
                }
            } else {
                // прибыли в пункт назначения!

                $this->log('Достигли пункта назначения!');

                $checkOk = true;
                $compareResults = [];
                foreach ($conditions as $condition) {
                    if (is_null($nicePath)) {
                        if (!$condition->check($currentPath)) {
                            $checkOk = false;
                            break;
                        }
                    } elseif (($compare = $condition->compare($currentPath, $nicePath)) === -1) {
                        $checkOk = false;
                        break;
                    } else {
                        $compareResults[] = $compare;
                    }
                }
                if ($checkOk) {
                    if (in_array(1, $compareResults)) {
                        $result->clear();
                    }
                    $this->log('Клёвые параметры!');
                    $nicePath = clone $currentPath;
                    // добавляем в результат лучший путь
                    $result->addPath($nicePath);
                } else {
                    $this->log('Плохие параметры!');
                }
            }
            if (!$citySelected) {
                $this->log('Выходим из текущего города ' . $currentPoint->getName());
                // если следующий город для пути не был выбран
                if (count($currentPath) === 0) {
                    // если предыдущих городов текущего пути нет - значит все возможные маршруты просмотрены, выходим
                    break;
                }
                // восстанавливаем предыдущий город из текущего пути
                foreach ($currentPoint->getLinkedPoints() as $linkedPoint) {
                    if ($see->offsetExists($linkedPoint)) {

                        $this->log('Снимаем метку обратного пути (мас) ' . $linkedPoint->getFrom()->getName() . ' > ' . $linkedPoint->getTo()->getName());
                        // снимаем метки обратного пути
                        $see->detach($linkedPoint);
                    }
                }

                $prevLinkedPoint = $currentPath->pop();

//                $this->log('Предыдущий город был ' . $prevLinkedPoint->getFrom()->getName());

                if (count($currentPath) > 0) {
                    $currentPoint = $prevLinkedPoint->getFrom();
                } else {
                    $currentPoint = $from;
                }

                $this->log('Вернулись к городу ' . $currentPoint->getName());

                if ($prevLinkedPoint->getTo()->isExistsLinkTo($currentPoint)) {
                    // метка, чтобы не идти по обратному пути, если он есть

                    $revLink = $prevLinkedPoint->getTo()->getLinkByFromPoint($currentPoint);

                    $this->log('Снимаем метку обратного пути (ед) ' . $revLink->getFrom()->getName() . ' > ' . $revLink->getTo()->getName());

                    $see->detach($prevLinkedPoint->getTo()->getLinkByFromPoint($currentPoint));
                }
            }
        }
        if (!$result->count()) {
            throw new \OutOfBoundsException('Путь не найден.');
        }

        return $result;
    }
}
