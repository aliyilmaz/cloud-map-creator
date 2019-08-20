<?php

namespace App\Controller;

use App\FileUtils;
use App\Projections\MercatorProjection;

class MercatorMapController extends AbstractController
{
    const IMAGE_SOURCE_DIRECTORY = __DIR__ . '/../../results/5d_colored/';
    const IMAGE_TARGET_DIRECTORY = __DIR__ . '/../../results/5d_colored_mercator/';

    const IMAGE_WIDTH = 3600;
    const IMAGE_HEIGHT = 3600;

    /** @var MercatorProjection */
    private $projection;

    public function __construct()
    {
        parent::__construct();

        $this->projection = new MercatorProjection(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
    }

    public function run(): void
    {
        $imageFiles = FileUtils::getFiles(self::IMAGE_SOURCE_DIRECTORY);

        $this->output->writeln('Create mercator maps for ' . count($imageFiles) . ' files');

        FileUtils::createDirectory(self::IMAGE_TARGET_DIRECTORY);

        foreach ($imageFiles as $imageFile) {
            $targetImageFile = self::IMAGE_TARGET_DIRECTORY . FileUtils::getFileName($imageFile);

            $this->createMercatorCloudMap($imageFile, $targetImageFile);
        }
    }

    private function createMercatorCloudMap($imageFile, $targetImageFile): void
    {
        $this->output->write('Convert ' . FileUtils::getFileName($imageFile) . ' to colored cloud map ...');

        if (!file_exists($targetImageFile)) {
            $image = imagecreatefrompng($imageFile);

            $width = imagesx($image);
            $height = imagesy($image);

            for ($y = 0; $y < self::IMAGE_HEIGHT; $y++) {
                var_dump($y);
                for ($x = 0; $x < self::IMAGE_WIDTH; $x++) {
                    $y = 3600;

                    $lon = $this->projection->x2lon($x);
                    $lat = $this->projection->y2lat($y);

                    var_dump($lon, $lat);die();

                    if ($x > 10) {
                        die();
                    }

//                    $color = imagecolorallocate($image, $rgbNew[0], $rgbNew[1], $rgbNew[2]);
//                    imagesetpixel($image, $x, $y, $color);
                }

                die();
            }

            imagepng($image, $targetImageFile);

            $this->output->writeln('OK', false);
        } else {
            $this->output->writeln('OK (file already exists)', false);
        }
    }
}
