<?php

namespace App\Controller;

use App\CloudMapDownloader;
use App\ImageProcessor;
use App\OutputInterface;

abstract class AbstractController
{
    protected $output;
    protected $cloudMapDownloader;
    protected $imageProcessor;

    public function __construct()
    {
        $this->output = new OutputInterface();
        $this->cloudMapDownloader = new CloudMapDownloader();
        $this->imageProcessor = new ImageProcessor();
    }

    public abstract function run(): void;
}
