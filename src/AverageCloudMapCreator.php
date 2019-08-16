<?php

namespace App;

class AverageCloudMapCreator
{
    const SATELLITES = [
        'aqua' => 'MYDAL2_D_CLD_FR',
        'terra' => 'MODAL2_D_CLD_FR',
    ];

    private $output;
    private $cloudMapDownloader;
    private $imageCombiner;

    public function __construct()
    {
        $this->output = new OutputInterface();
        $this->cloudMapDownloader = new CloudMapDownloader();
        $this->imageCombiner = new ImageCombiner();
    }

    public function run()
    {
        $this->output->writeln('START');

        $dateTime = new \DateTime('2000-01-02'); // We must get sure, this one is a leap year!

        do {
            $this->generateCloudmapForDay($dateTime);

            $dateTime->add(new \DateInterval('P1D'));
        } while ($dateTime->format('Y') === '2000');


        $this->output->writeln('FINISHED \\o/');
    }

    private function generateCloudmapForDay(\DateTime $dateTime):void
    {
        $month = $dateTime->format('m');
        $day = $dateTime->format('d');

        $this->output->writeln('Create cloud map for ' . $month . '-' . $day);

        $imageFiles = $this->getImageFiles($dateTime);
        $resultImageFile = $this->getResultImageFile($dateTime);

        $this->imageCombiner->combine($imageFiles, $resultImageFile);
    }

    private function getImageFiles(\DateTime $dateTime): array
    {
        $imageFiles = [];

        $month = $dateTime->format('m');
        $day = $dateTime->format('d');

        $yearMin = 2000;
        $currentYear = intval(date('Y'));

        for ($year = $yearMin; $year <= $currentYear; $year++) {
            $date = $year . '-' . $month . '-' . $day;

            foreach (self::SATELLITES as $satellite) {
                $imageFile = $this->cloudMapDownloader->download($satellite, $date);

                if ($imageFile) {
                    $imageFiles[] = $imageFile;
                }
            }
        }

        return $imageFiles;
    }

    private function getResultImageFile(\DateTime $dateTime): string
    {
        $resultImageFile = __DIR__ . '/../results/';
        FileUtils::createDirectory($resultImageFile);

        return $resultImageFile . 'cloud-map_' . $dateTime->format('m-d') . '.png';
    }
}
