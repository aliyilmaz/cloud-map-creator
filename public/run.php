<?php

require __DIR__ . '/../vendor/autoload.php';

//$acmc = new \App\AverageCloudMapCreator();
//$acmc->createDayCloudMaps();

$f = new \App\MeanCloudMapGenerator();
$f->meadDayCloudMaps();
