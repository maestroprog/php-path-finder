<?php

$results = [
    'Функция findLowCostPath' => require 'bench_function.php',
    'ООП' => require 'bench_oop.php',
    'ООП с предрасчетами' => require 'bench_oop_fast.php',
];

?>
<h1>Тест с 10 тысячами итераций</h1>
<table cellpadding="5" cellspacing="0" border="1">
    <tr>
        <th>Тест</th>
        <th>RPS</th>
        <th>Потребление памяти</th>
        <th>Загрузка данных и предрасчеты</th>
        <th>Общее время работы</th>
    </tr>
    <?php foreach ($results as $name => $result): ?>
        <tr>
            <td><?= $name; ?></td>
            <td><?= (int)(10000 / $result[0]); ?></td>
            <td><?= number_format($result[3] / 1024, 4, '.', ' '); ?> K</td>
            <td><?= number_format($result[1], 4); ?> s</td>
            <td><?= number_format($result[4], 4); ?> s</td>
        </tr>
    <?php endforeach; ?>
</table>
