<?php

namespace App\Controller;

use App\FileUtils;

class MeanDailyCloudMapsController extends AbstractController
{
    const IMAGE_SOURCE_DIRECTORY = __DIR__ . '/../../results/1d/';
    const IMAGE_TARGET_DIRECTORY = __DIR__ . '/../../results/5d/';

    public function run(): void
    {
        $dateTime = new \DateTime('1999-01-01'); // We must get sure, the year has no leap year!

        do {
            $this->meanCloudMapsForDay($dateTime);

            $dateTime->add(new \DateInterval('P1D'));
        } while ($dateTime->format('Y') === '1999');
    }

    private function meanCloudMapsForDay(\DateTime $dateTime): void
    {
        FileUtils::createDirectory(self::IMAGE_TARGET_DIRECTORY);
        $resultImageFile = self::IMAGE_TARGET_DIRECTORY . '/cloudmap_' . $dateTime->format('m-d') . '.png';

        $imageFiles = $this->getImageFiles($dateTime);

        $this->imageProcessor->meanImages($imageFiles, $resultImageFile);
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
        return self::IMAGE_SOURCE_DIRECTORY . '/cloudmap_'  . $dateTime->format('m-d') . '.png';
    }
}
