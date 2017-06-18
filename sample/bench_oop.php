<?php

require_once __DIR__ . '/../src/bootstrap.php';
$time = microtime(true);
$dataProvider = new \Path\PathFinderDataProvider();
$dataProvider->loadCosts(require 'data.php');
$pathFinder = new \Path\Service\PathFinderService($dataProvider);
//$pathFinder = new \Path\Service\CachingPathFinderService($pathFinder, new \Path\Cache\InMemoryCache());

$from = $dataProvider->findPointByName('Москва');
$to = $dataProvider->findPointByName('Владивосток');

try {
} catch (OutOfBoundsException $e) {
    die($e->getMessage());
}
$loaded = microtime(true) - $time;
$memory = memory_get_usage();
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    try {
        $result = $pathFinder->getPathWithConditions($from, $to, [new \Path\Condition\LowPriceCondition(),]);
    } catch (OutOfBoundsException $e) {
        die($e->getMessage());
    }
}
return [
    microtime(true) - $start,
    $loaded,
    memory_get_peak_usage(true) - $memory,
    memory_get_peak_usage(),
    microtime(true) - $time,
];
