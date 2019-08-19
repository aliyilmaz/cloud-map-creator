<?php

namespace App;

class CloudMapDownloader
{
    private $output;

    private $fileDirectory;

    private $satellite;
    private $date;

    public function __construct()
    {
        $this->output = new OutputInterface();
    }

    public function download(string $fileDirectory, string $satellite, string $date): ?string
    {
        $this->output->write('Download: ' . $satellite . ' | ' . $date . ' ... ');

        $this->fileDirectory = $fileDirectory;
        $this->satellite = $satellite;
        $this->date = $date;

        $fileName = $this->getFileName();

        if (!file_exists($fileName)) {
            $imageUrl = $this->getImageUrl();

            if (!$imageUrl) {
                $this->output->writeln('FAILED (No image url found / No data)', false);
                return null;
            }

            $this->downloadImage($imageUrl, $fileName);

            $this->output->writeln('OK', false);

            return $fileName;
        } else {
            $this->output->writeln('OK (File already exists)', false);
        }

        return $fileName;
    }

    private function getImageUrl(): ?string
    {
        $url = Utils::replace(
            'https://neo.sci.gsfc.nasa.gov/view.php?datasetId={satellite}&date={date}',
            [
                'satellite' => $this->satellite,
                'date' => $this->date,
            ]
        );

        $content = file_get_contents($url);

        if ($this->isValidData($content)) {
            $pattern = '/si=([0-9]+)&/s';
            if (preg_match($pattern, $content, $matches)) {
                return Utils::replace(
                    'https://neo.sci.gsfc.nasa.gov/servlet/RenderData?si={si}&cs=rgb&format=PNG&width=3600&height=1800',
                    ['si' => $matches[1]]
                );
            }
        }

        return null;
    }

    private function isValidData(string $content): bool
    {
        $pattern = '/calDate == \'' . $this->date . '\'/s';

        return preg_match($pattern, $content);
    }

    private function getFileName(): string
    {
        FileUtils::createDirectory($this->fileDirectory);

        return $this->fileDirectory . $this->satellite . '_' . $this->date . '.png';
    }

    private function downloadImage(string $imageUrl, string $fileName): bool
    {
        $image = file_get_contents($imageUrl);
        file_put_contents($fileName, $image);

        return true;
    }
}
