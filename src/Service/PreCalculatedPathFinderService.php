<?php

namespace Path\Service;

use Path\ConditionInterface;
use Path\PathFinderDataProvider;
use Path\PathFinderInterface;
use Path\ValueObject\MultiLinkedPoint;
use Path\ValueObject\PathResult;
use Path\ValueObject\Point;


/**
 * Сервис поиска путей, который заранее загружает все возможные варианты маршрутов.
 */
final class PreCalculatedPathFinderService implements PathFinderInterface
{
    private $pathFinderService;
    private $dataProvider;

    /**
     * @param PathFinderInterface $pathFinderService Сервис с алгоритмом нахождения путей
     * @param PathFinderDataProvider $dataProvider Провайдер данных точек
     * @param ConditionInterface[] $conditions Позволяет предустановить условия для ускорения поиска маршрутов
     */
    public function __construct(
        PathFinderInterface $pathFinderService,
        PathFinderDataProvider $dataProvider,
        array $conditions
    )
    {
        $this->pathFinderService = $pathFinderService;
        $this->dataProvider = $dataProvider;

        foreach ($conditions as $condition) {
            if (!$condition instanceof ConditionInterface) {
                throw new \InvalidArgumentException('Invalid condition type.');
            }
        }

        $this->init($conditions);
    }

    public function getPath(Point $from, Point $to): PathResult
    {
        if (!$from->isExistsLinkTo($to)) {
            throw new \OutOfBoundsException('Путь не найден.');
        }
        $link = $from->getLinkByFromPoint($to);
        if (!$link instanceof MultiLinkedPoint) {
            throw new \OutOfBoundsException('Путь не найден.');
        }
        return new PathResult($from, $to, $link->getPaths());
    }

    /**
     * @inheritdoc
     * @param Point $from
     * @param Point $to
     * @param ConditionInterface[] $conditions
     * @return PathResult
     */
    public function getPathWithConditions(Point $from, Point $to, array $conditions): PathResult
    {
        if (!$from->isExistsLinkTo($to)) {
            throw new \OutOfBoundsException('Путь не найден.');
        }
        $link = $from->getLinkByFromPoint($to);
        if (!$link instanceof MultiLinkedPoint) {
            throw new \OutOfBoundsException('Путь не найден.');
        }
        $result = new PathResult($from, $to);
        $nicePath = null;

        foreach ($link->getPaths() as $path) {
            if (is_null($nicePath)) {
                $nicePath = $path;
                $result->addPath($nicePath);
            } else {
                $checkOk = true;
                $nice = false;
                foreach ($conditions as $condition) {
                    if (is_null($nicePath)) {
                        if (!$condition->check($path)) {
                            $checkOk = false;
                            break;
                        }
                    } elseif (($compare = $condition->compare($path, $nicePath)) === -1) {
                        $checkOk = false;
                        break;
                    } elseif (!$nice && $compare === 1) {
                        $nice = true;
                    }
                }
                if ($checkOk) {
                    if ($nice) {
                        $result->clear();
                    }
                    $nicePath = $path;
                    // добавляем в результат лучший путь
                    $result->addPath($nicePath);
                }
            }
        }
        return $result;
    }

    /**
     * Функция инициирующая полные предварительные расчеты.
     *
     * @param $conditions ConditionInterface[]
     */
    private function init(array $conditions = [])
    {
        $allPoints = [];
        // загрузим список всех доступных точек
        foreach ($this->dataProvider as $point) {
            /**
             * @var $point Point
             */
            $allPoints[] = $point;
        }

        // перебирая все точки в качестве пунктов отправления и назначения
        // будем вычислять все возможные маршруты между любыми двумя точками
        foreach ($allPoints as $pointFrom) {
            foreach ($allPoints as $pointTo) {
                /**
                 * @var $pointFrom Point
                 * @var $pointTo Point
                 */
                if ($pointFrom === $pointTo) {
                    continue;
                }
                try {
                    $result = $this->pathFinderService->getPathWithConditions($pointFrom, $pointTo, $conditions);
                    if ($pointFrom->getName() === 'Москва' && $pointTo->getName() === 'Владивосток') {
                        foreach ($result->getPathList() as $path) {
                            $p = [];
                            foreach ($path->getPoints() as $po) {
                                $p[] = $po->getName();
                            }
                        }
                    }
                    $pointFrom->linkMulti(new MultiLinkedPoint($pointFrom, $pointTo, $result->getPathList()));
                } catch (\OutOfBoundsException $e) {
                    ; // nothing
                }
            }
        }
    }
}
