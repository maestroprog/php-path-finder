<?php

use function Path\findLowCostPath;

require_once __DIR__ . '/../src/library.php';
$time = microtime(true) - $time;
$costs = require 'data.php';
$memory = memory_get_usage(true);
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    try {
        $from = 'Москва';
        $to = 'Владивосток';
        $result = findLowCostPath($from, $to, $costs);
    } catch (RuntimeException $e) {
        die($e->getMessage());
    }
}
return [
    microtime(true) - $start,
    0,
    memory_get_peak_usage(true) - $memory,
    memory_get_peak_usage(),
    microtime(true) - $time,
];
