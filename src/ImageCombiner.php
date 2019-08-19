<?php

namespace App;

class ImageCombiner
{
    private $output;

    public function __construct()
    {
        $this->output = new OutputInterface();
    }

    public function addTransparency(string $imageFile, string $resultImageFile): bool
    {
        $this->output->write('Add transparency to ' . FileUtils::getFileName($imageFile) . ' ... ');

        if (file_exists($resultImageFile)) {
            $this->output->writeln('OK (File already exists)', false);
            return true;
        }

        $command = Utils::replace(
            'convert "{source}" -transparent black "{target}"',
            [
                'source' => $imageFile,
                'target' => $resultImageFile,
            ]
        );

        exec($command, $output, $return);

        if ($return === 0) {
            $this->output->writeln('OK', false);
            return true;
        } else {
            $this->output->writeln('ERROR', false);
            return false;
        }
    }

    public function meanImages(array $imageFiles, string $resultImageFile): bool
    {
        $this->output->write('Mean ' . count($imageFiles) . ' images to ' . FileUtils::getFileName($resultImageFile) . ' ... ');

        if (file_exists($resultImageFile)) {
            $this->output->writeln('Ok (File already exists)', false);
            return true;
        }

        $images = '';
        foreach ($imageFiles as $imageFile) {
            $images .= '"' . $imageFile . '" ';
        }

        $command = Utils::replace(
            'convert {source} -evaluate-sequence mean \( -clone 0 -alpha off \) \( -clone 0 -alpha extract \) -delete 0 +swap -compose divide -composite "{target}"',
            [
                'source' => $images,
                'target' => $resultImageFile,
            ]
        );

        exec($command, $output, $result);

        if ($result === 0) {
            $this->output->writeln('OK', false);
            return true;
        } else {
            $this->output->writeln('ERROR', false);
            return false;
        }
    }
}
