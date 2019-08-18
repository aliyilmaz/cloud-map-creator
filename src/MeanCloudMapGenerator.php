<?php

namespace App;

class MeanCloudMapGenerator
{
    private $imageCombiner;

    public function __construct()
    {
        $this->imageCombiner = new ImageCombiner();
    }

    public function meadDayCloudMaps(): void
    {
        $dateTime = new \DateTime('1999-01-01'); // We must get sure, the year has no leap year!

        // TODO Move files!!!!

        do {
            $this->meanCloudMapsForDay($dateTime);

            $dateTime->add(new \DateInterval('P1D'));
        } while ($dateTime->format('Y') === '1999');
    }

    private function meanCloudMapsForDay(\DateTime $dateTime): void
    {
        // TODO, if folder not found, create
        $resultImageFile = __DIR__ . '/../results/cloud-map-mean_' . $dateTime->format('m-d') . '.png';
        $imageFiles = $this->getImageFiles($dateTime);

        $this->imageCombiner->meanImages($imageFiles, $resultImageFile);
    }

    private function getImageFiles(\DateTime $dateTime): array
    {
        $imageFiles = [];

        for ($dayInterval = -2; $dayInterval <= 2; $dayInterval++) {
            $dateTimeCur = clone $dateTime;
            if ($dayInterval < 0) {
                $dateTimeCur->sub(new \DateInterval('P' . abs($dayInterval) . 'D'));
            } elseif ($dayInterval > 0) {
                $dateTimeCur->add(new \DateInterval('P' . $dayInterval . 'D'));
            }

            $imageFile = $this->getImageFile($dateTimeCur);
            if (file_exists($imageFile)) {
                $imageFiles[] = $imageFile;
            }
        }

        return $imageFiles;
    }

    private function getImageFile(\DateTime $dateTime): string
    {
        return __DIR__ . '/../temp/results/cloud-map_'  . $dateTime->format('m-d') . '.png';
    }
}
