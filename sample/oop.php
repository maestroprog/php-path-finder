<?php

ini_set('display_errors', true);
error_reporting(E_ALL);
require_once __DIR__ . '/../src/bootstrap.php';

$dataProvider = new \Path\PathFinderDataProvider();
$dataProvider->loadCosts(require 'data.php');
$pathFinder = new \Path\Service\PathFinderService($dataProvider);

$from = $dataProvider->findPointByName('Москва');
$to = $dataProvider->findPointByName('Владивосток');

try {
    $result = $pathFinder->getPathWithConditions($from, $to, [new \Path\Condition\LowPriceCondition(),]);
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
