<?php

require __DIR__ . '/../vendor/autoload.php';

//// Create average cloud maps for 1 day
//$dccm = new \App\Controller\DailyCloudCoverageMapController();
//$dccm->run();
//
//// Mean average cloud maps als take 5 days (-/+2 days) into account
//$mdcm = new \App\Controller\MeanDailyCloudMapsController();
//$mdcm->run();
//
//// Create colored cloud maps of the 5 days cloud maps
//$cccm = new \App\Controller\ColorCloudCoverageMapController();
//$cccm->run();

$mm = new \App\Controller\MercatorMapController();
$mm->run();