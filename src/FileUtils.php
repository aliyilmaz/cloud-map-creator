<?php

namespace App;

class FileUtils
{
    public static function createDirectory(string $directory): void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    public static function deleteDirectory(string $directory): void
    {
        if (file_exists($directory)) {
            unlink($directory);
        }
    }

    public static function getFileName(string $file): string
    {
        $parts = explode('/', $file);

        return array_pop($parts);
    }
}
