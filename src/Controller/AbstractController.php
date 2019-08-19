<?php

namespace App\Controller;

use App\CloudMapDownloader;
use App\ImageCombiner;
use App\OutputInterface;

abstract class AbstractController
{
    public abstract function run(): void;

    protected $output;
    protected $cloudMapDownloader;
    protected $imageCombiner;

    public function __construct()
    {
        $this->output = new OutputInterface();
        $this->cloudMapDownloader = new CloudMapDownloader();
        $this->imageCombiner = new ImageCombiner();
    }
}
