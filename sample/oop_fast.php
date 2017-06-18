<?php

ini_set('display_errors', true);
error_reporting(E_ALL);
require_once __DIR__ . '/../src/bootstrap.php';

$dataProvider = new \Path\PathFinderDataProvider();
$dataProvider->loadCosts(require 'data.php');
$pathFinder = new \Path\Service\PathFinderService($dataProvider);
$pathFinder = new \Path\Service\PreCalculatedPathFinderService(
    $pathFinder,
    $dataProvider,
    [new \Path\Condition\LowPriceCondition(),]
);

// todo нахождение нескольких маршрутов хреново работает
$from = $dataProvider->findPointByName('Москва');
$to = $dataProvider->findPointByName('Владивосток');

try {
    $result = $pathFinder->getPath($from, $to);
} catch (OutOfBoundsException $e) {
    die($e->getMessage());
}

?>
<h3>Маршруты в порядке следования от г. <?= $from->getName(); ?> до г. <?= $to->getName(); ?>.</h3>
<p>Всего найдено <?= $result->count(); ?> путей.</p>
<?php foreach ($result->getPathList() as $path): ?>
    <h4>Найден самый дешёвый маршрут за <?= $path->getProperties()->getFarePrice(); ?></h4>
    <table>
        <?php foreach ($path->getPoints() as $i => $point): ?>
            <tr>
                <td style="<?= $i === 0
                    ? 'background-color:lightgreen'
                    : ($i === $path->count() ? 'background-color:pink' : ''); ?>"><?= $point->getName(); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endforeach; ?>
