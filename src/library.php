<?php

namespace Path;

use RuntimeException;

/**
 * Функция, выполняющая поиск наиболее дешёвого маршрута.
 * На вход принимает три параметра: название города отправления, название города назначения,
 * и массив со стоимостью передвижения между городами,
 * с указанием названий двух городов: отправления и назначения, и цены за проезд между этими двумя городами.
 * В качестве результата, функция вернёт массив со списком населенных пунктов в качестве маршрута,
 * и общей стоимостью за проезд.
 *
 * Пример использования:
 * $result = findLowCostPath(
 *  'Москва',
 *  'Владивосток',
 *  [
 *      ['Москва', 'Самара', 1000],
 *      ['Самара', 'Красноярск', 2000],
 *      ['Красноярск', 'Владивосток', 3000],
 *      ['Самара', 'Новосибирск', 1000],
 *      ['Новосибирск', 'Владивосток', 2000],
 * ]);
 *
 * Результат выполнения функции:
 * $result = [4000, ['Москва', 'Самара', 'Новосибирск', 'Владивосток']];
 *
 * Если маршрут для проезда с одного города в другой не был найден, функция бросит исключение.
 *
 * @param string $fromCity
 * @param string $toCity
 * @param array $costList
 * @return array|bool
 * @throws RuntimeException
 */
function findLowCostPath(string $fromCity, string $toCity, array $costList): array
{
    if ($fromCity === $toCity) {
        // если город отправления является городом назначения, такие входные данные могут быть
        // но не будем бросать исключение
        return [0, [$fromCity]];
    }

    $allLinks = [];
    // приводим список цен в удобное внутреннее представление функции
    foreach ($costList as $item) {
        if (isset($allLinks[$item[0]][$item[1]])) {
            throw new RuntimeException(sprintf(
                'Обнаружено дублирование цен на поездки между городами: %s и %s',
                $item[0],
                $item[1]
            ));
        }
        $allLinks[$item[0]][$item[1]] = ['price' => $item[2], 'see' => false];
    }

    $veryLowPrice = null; // самая дешёвая стоимость, найденная на данный момент
    $path = []; // самый дешёвый найденный путь

    $currentCity = $fromCity;  // текущий город, от которого ведутся поиски к пункту назначения
    $currentPath = []; // текущий путь, по которому ведутся расчеты
    $currentPrice = 0; // стоимость текущего пути.
    // переменная нужна для того, чтобы предупредить излишние вычисления

    // начинаем поиск самого дешёвого пути
    for (; ;) {
        $citySelected = false;
        if ($currentCity !== $toCity) {
            if (isset($allLinks[$currentCity])) {
                foreach ($allLinks[$currentCity] as $nextCity => &$data) {
                    $nextPrice = $currentPrice + $data['price'];
                    if (
                        !$data['see']
                        && (null === $veryLowPrice || $nextPrice < $veryLowPrice)
                        && !in_array($nextCity, $currentPath)
                    ) {
                        $data['see'] = true;
                        if (isset($allLinks[$nextCity][$currentCity])) {
                            // метка, чтобы не идти по обратному пути, если он есть
                            $allLinks[$nextCity][$currentCity]['see'] = true;
                        }
                        $currentPath[] = $nextCity;
                        $currentCity = $nextCity;
                        $currentPrice = $nextPrice;
                        $citySelected = true;
                        break;
                    }
                    unset($data);
                }
            }
        } else {
            // прибыли в пункт назначения!
            if (null === $veryLowPrice || $currentPrice < $veryLowPrice) {
                // если этот путь первый найденный или дешевле предыдущих, то запомним данные этого пути
                // запомним новую наименьшую общую стоимость пути
                $veryLowPrice = $currentPrice;
                $path = $currentPath;
            }
        }
        if (!$citySelected) {
            // если следующий город для пути не был выбран
            if (count($currentPath) === 0) {
                // если предыдущих городов текущего пути нет - значит все возможные маршруты просмотрены, выходим
                break;
            }
            // восстанавливаем предыдущий город из текущего пути
            if (isset($allLinks[$currentCity])) {
                // снимаем метки обратного пути
                // todo check
                array_walk($allLinks[$currentCity], function (&$item, $key) {
                    $item['see'] = false;
                });
            }
            $prevCity = array_pop($currentPath);
            if (count($currentPath) > 0) {
                $currentCity = end($currentPath);
            } else {
                $currentCity = $fromCity;
            }
            $currentPrice -= $allLinks[$currentCity][$prevCity]['price'];
            if (isset($allLinks[$prevCity][$currentCity])) {
                $allLinks[$prevCity][$currentCity]['see'] = false;
            }
        }
    }

    if (is_null($veryLowPrice)) {
        throw new RuntimeException('Маршрут не найден!');
    }

    return [$veryLowPrice, array_merge([$fromCity], $path)];
}
