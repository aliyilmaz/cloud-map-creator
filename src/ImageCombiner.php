<?php

namespace App;

class ImageCombiner
{
    private $output;

    private $imageFiles = [];
    private $resultImageFile;

    public function __construct()
    {
        $this->output = new OutputInterface();
    }

    public function combine(array $imageFiles, string $resultImageFile): void
    {
        $this->imageFiles = $imageFiles;
        $this->resultImageFile = $resultImageFile;

        $this->output->writeln('Combine ' . count($imageFiles) . ' image files');

        $imageFilesTransparent = $this->getTransparencyImages();
        $this->meanImages($imageFilesTransparent);
    }

    private function getTransparencyImages(): array
    {
        $transparentImageFiles = [];

        foreach ($this->imageFiles as $imageFile) {
            $transparentImageFile = $this->getFileNameForTransparentImage($imageFile);
            $ok = $this->addTransparency($imageFile, $transparentImageFile);

            if ($ok) {
                $transparentImageFiles[] = $transparentImageFile;
            }
        }

        return $transparentImageFiles;
    }

    private function getFileNameForTransparentImage(string $imageFile): string
    {
        $transparentImageFile = __DIR__ . '/../temp/transparent/';
        FileUtils::createDirectory($transparentImageFile);

        $transparentImageFile .= FileUtils::getFileName($imageFile);

        return $transparentImageFile;
    }

    private function addTransparency(string $image, string $transparentImageFile): bool
    {
        $this->output->write('Add transparency to ' . $image . ' ... ');

        if (file_exists($transparentImageFile)) {
            $this->output->writeln('OK (File already exists)', false);
            return true;
        }

        $command = Utils::replace(
            'convert "{source}" -transparent black "{target}"',
            [
                'source' => $image,
                'target' => $transparentImageFile,
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

    private function meanImages(array $imageFiles): bool
    {
        $this->output->write('Mean images to final image ' . $this->resultImageImage . '... ');

        if (file_exists($this->resultImageImage)) {
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
                'target' => $this->resultImageImage,
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
