<?php

namespace App\Controller;

use App\FileUtils;

class ColorCloudCoverageMapController extends AbstractController
{
    const COLOR_MAP_COLOR_MAPPING_SOURCE = [
        0 => [8, 48, 107],
        100 => [247, 251, 255],
    ];

    const COLOR_MAP_COLOR_MAPPING_TARGET = [
        0 => [73, 126, 209],
        25 => [132, 189, 207],
        50 => [217, 204, 117],
        75 => [235, 159, 62],
        100 => [217, 71, 71],
    ];

    const IMAGE_SOURCE_DIRECTORY = __DIR__ . '/../../results/5d/';
    const IMAGE_TARGET_DIRECTORY = __DIR__ . '/../../results/5d_colored/';

    public function run(): void
    {
        $imageFiles = $this->getImageFiles();

        $this->output->writeln('Create colored cloud maps for ' . count($imageFiles) . ' files');

        FileUtils::createDirectory(self::IMAGE_TARGET_DIRECTORY);

        foreach ($imageFiles as $imageFile) {
            $targetImageFile = self::IMAGE_TARGET_DIRECTORY . FileUtils::getFileName($imageFile);

            $this->createColoredCloudMap($imageFile, $targetImageFile);
        }
    }

    private function getImageFiles(): array
    {
        $imageFiles = [];

        $handle = opendir(self::IMAGE_SOURCE_DIRECTORY);
        while ($fileName = readdir($handle)) {
            if (preg_match('/\.png$/', $fileName)) {
                $imageFiles[] = self::IMAGE_SOURCE_DIRECTORY . $fileName;
            }
        }

        return $imageFiles;
    }

    private function createColoredCloudMap(string $imageFile, string $targetImageFile): void
    {
        $this->output->write('Convert ' . FileUtils::getFileName($imageFile) . ' to colored cloud map ...');

        if (!file_exists($targetImageFile)) {
            $image = imagecreatefrompng($imageFile);

            $width = imagesx($image);
            $height = imagesy($image);

            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $color = imagecolorat($image, $x, $y);
                    $rgb = $this->color2rgb($color);
                    $rgbNew = $this->convertColor($rgb);

                    $color = imagecolorallocate($image, $rgbNew[0], $rgbNew[1], $rgbNew[2]);
                    imagesetpixel($image, $x, $y, $color);
                }
            }

            imagepng($image, $targetImageFile);

            $this->output->writeln('OK', false);
        } else {
            $this->output->writeln('OK (file already exists)', false);
        }
    }

    private function convertColor(array $rgb): array
    {
        if ($rgb[0] === 0 && $rgb[1] === 0 && $rgb[2] === 0) {
            return $rgb;
        }

        $percent = $this->getPercentageFromColor($rgb, self::COLOR_MAP_COLOR_MAPPING_SOURCE);
        $rbgNew = $this->getColorFromPercentage($percent, self::COLOR_MAP_COLOR_MAPPING_TARGET);

        return $rbgNew;
    }

    private function getPercentageFromColor(array $rgb, array $cloudMap): float
    {
        $percentage = 0.0;

        for ($i = 0; $i < 3; $i++) {
            $color1 = self::COLOR_MAP_COLOR_MAPPING_SOURCE[0][$i];
            $color2 = self::COLOR_MAP_COLOR_MAPPING_SOURCE[100][$i];

            $percentage += 100 / ($color2 - $color1) * ($rgb[$i] - $color1);
        }

        return $percentage / 3;
    }

    private function getColorFromPercentage(float $percentage, array $cloudMap): array
    {
        $rgb = [];

        // TODO Do this stuff in a dynamic... This code sucks!
        if ($percentage < 25) {
            $colorKey1 = 0;
            $colorKey2 = 25;
        } elseif ($percentage >= 25 && $percentage < 50){
            $colorKey1 = 25;
            $colorKey2 = 50;
        } elseif ($percentage >= 50 && $percentage < 75){
            $colorKey1 = 50;
            $colorKey2 = 75;
        } elseif ($percentage >= 75 && $percentage <= 100){
            $colorKey1 = 75;
            $colorKey2 = 100;
        }

        for ($i = 0; $i < 3; $i++) {
            $color1 = self::COLOR_MAP_COLOR_MAPPING_TARGET[$colorKey1][$i];
            $color2 = self::COLOR_MAP_COLOR_MAPPING_TARGET[$colorKey2][$i];

            $rgb[$i] = $color2 - ($colorKey2 - $percentage) / ($colorKey2 - $colorKey1) * ($color2 - $color1);
        }

        return $rgb;
    }

    private function color2rgb(int $color): array
    {
        $r = ($color >> 16) & 0xFF;
        $g = ($color >> 8) & 0xFF;
        $b = $color & 0xFF;

        return [$r, $g, $b];
    }
}
