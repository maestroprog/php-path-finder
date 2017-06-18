<?php

use function Path\findLowCostPath;

ini_set('display_errors', true);
error_reporting(E_ALL);
require_once __DIR__ . '/../src/library.php';

$costs = require 'data.php';
try {
    $from = 'Москва';
    $to = 'Владивосток';
    $result = findLowCostPath($from, $to, $costs);
} catch (RuntimeException $e) {
    die($e->getMessage());
}

?>
<h4>Найден самый дешёвый маршрут за <?= $result[0]; ?></h4>
<h5>Маршрут в порядке следования от г. <?= $from; ?> до г. <?= $to; ?>.</h5>
<table>
    <?php foreach ($result[1] as $i => $item): ?>
        <tr>
            <td style="<?= $i === 0
                ? 'background-color:lightgreen'
                : ($i === count($result[1]) - 1 ? 'background-color:pink' : ''); ?>"><?= $item; ?></td>
        </tr>
    <?php endforeach; ?>
</table>
