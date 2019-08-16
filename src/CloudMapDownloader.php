<?php

namespace App;

class CloudMapDownloader
{
    private $output;

    private $satellite;
    private $date;

    public function __construct()
    {
        $this->output = new OutputInterface();
    }

    public function download(string $satellite, string $date): ?string
    {
        $this->output->write('Download: ' . $satellite . ' | ' . $date . ' ... ');

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
        $url = 'https://neo.sci.gsfc.nasa.gov/view.php?datasetId={satellite}&date={date}';

        $replaceArray = [
            'satellite' => $this->satellite,
            'date' => $this->date,
        ];

        foreach ($replaceArray as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

//        var_dump($url);die();

        $content = file_get_contents($url);

        if ($this->isValidData($content)) {
            $pattern = '/si=([0-9]+)&/s';
            if (preg_match($pattern, $content, $matches)) {
                $url = 'https://neo.sci.gsfc.nasa.gov/servlet/RenderData?si={si}&cs=rgb&format=PNG&width=3600&height=1800';
                return str_replace('{si}', $matches[1], $url);
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
        $fileName = __DIR__ . '/../temp/cloud_maps/';
        FileUtils::createDirectory($fileName);

        $fileName .= $this->satellite . '_' . $this->date . '.png';

        return $fileName;
    }

    private function downloadImage(string $imageUrl, string $fileName): bool
    {
        $image = file_get_contents($imageUrl);
        file_put_contents($fileName, $image);

        return true;
    }
}
