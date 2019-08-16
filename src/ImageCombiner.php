<?php

namespace App;

class ImageCombiner
{
    private $output;

    private $imageFiles = [];

    public function __construct()
    {
        $this->output = new OutputInterface();
    }

    public function combine(array $imageFiles)
    {
        $this->imageFiles = $imageFiles;

        $this->output->writeln('Combine ' . count($imageFiles) . ' image files');

        // TODO Write some nice functionality
    }
}