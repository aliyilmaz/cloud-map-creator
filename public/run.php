<?php

require __DIR__ . '/../vendor/autoload.php';

$dccm = new \App\Controller\DailyCloudCoverageMapController();
$dccm->run();

$mdcm = new \App\Controller\MeanDailyCloudMapsController();
$mdcm->run();
