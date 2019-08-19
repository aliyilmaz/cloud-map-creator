<?php

namespace App\Controller;

use App\FileUtils;

class DailyCloudCoverageMapController extends AbstractController
{
    const SATELLITES = [
        'aqua' => 'MYDAL2_D_CLD_FR',
        'terra' => 'MODAL2_D_CLD_FR',
    ];

    const DIRECTORY_TEMP_RAW = __DIR__ . '/../../temp/raw/';
    const DIRECTORY_TEMP_TRANSPARENT = __DIR__ . '/../../temp/transparent/';
    const DIRECTORY_RESULTS = __DIR__ . '/../../results/1d/';

    public function run(): void
    {
        $this->output->writeln('START');

        $dateTime = new \DateTime('1999-01-01'); // We must get sure, the year has no leap year!

        do {
            $this->generateCloudmapForDay($dateTime);

            $dateTime->add(new \DateInterval('P1D'));
        } while ($dateTime->format('Y') === '1999');

        $this->output->writeln('FINISHED \\o/');
    }

    private function generateCloudmapForDay(\DateTime $dateTime): void
    {
        $this->output->writeln('Create cloud map for ' . $dateTime->format('m-d'));

        $resultImageFile = $this->getResultImageFile($dateTime);

        if (!file_exists($resultImageFile)) {
            $imageFiles = $this->downloadImageFiles($dateTime);
            $imageFiles = $this->addTransparencyToImages($imageFiles);
            $ok = $this->imageCombiner->meanImages($imageFiles, $resultImageFile);

            if ($ok) {
                FileUtils::deleteDirectory(self::DIRECTORY_TEMP_TRANSPARENT);
            }
        } else {
            $this->output->writeln('SKIP: Cloud map already exists');
        }
    }

    private function getResultImageFile(\DateTime $dateTime): string
    {
        FileUtils::createDirectory(self::DIRECTORY_RESULTS);

        return self::DIRECTORY_RESULTS . 'cloudmap_' . $dateTime->format('m-d') . '.png';
    }

    private function downloadImageFiles(\DateTime $dateTime): array
    {
        $imageFiles = [];

        $month = $dateTime->format('m');
        $day = $dateTime->format('d');

        $yearMin = 2000;
        $currentYear = intval(date('Y'));

        for ($year = $yearMin; $year <= $currentYear; $year++) {
            $date = $year . '-' . $month . '-' . $day;

            foreach (self::SATELLITES as $satellite) {
                $imageFile = $this->cloudMapDownloader->download(self::DIRECTORY_TEMP_RAW, $satellite, $date);

                if ($imageFile) {
                    $imageFiles[] = $imageFile;
                }
            }
        }

        return $imageFiles;
    }

    private function addTransparencyToImages(array $imageFiles): array
    {
        $transparentImageFiles = [];

        foreach ($imageFiles as $imageFile) {
            $transparentImageFile = $this->getFileNameForTransparentImage($imageFile);
            $ok = $this->imageCombiner->addTransparency($imageFile, $transparentImageFile);

            if ($ok) {
                $transparentImageFiles[] = $transparentImageFile;
            }
        }

        return $transparentImageFiles;

    }

    private function getFileNameForTransparentImage(string $imageFile): string
    {
        FileUtils::createDirectory(self::DIRECTORY_TEMP_TRANSPARENT);

        return self::DIRECTORY_TEMP_TRANSPARENT . FileUtils::getFileName($imageFile);
    }
}
