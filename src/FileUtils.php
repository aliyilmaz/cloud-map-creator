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
            if (!is_dir($directory)) {
                throw new \InvalidArgumentException("$directory must be a directory");
            }

            if (substr($directory, strlen($directory) - 1, 1) != '/') {
                $directory .= '/';
            }

            $files = glob($directory . '*', GLOB_MARK);

            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::deleteDirectory($file);
                } else {
                    unlink($file);
                }
            }

            rmdir($directory);
        }
    }

    public static function getFileName(string $file): string
    {
        $parts = explode('/', $file);

        return array_pop($parts);
    }

    public static function getFiles(string $directory): array
    {
        $files = [];

        $handle = opendir($directory);
        while ($fileName = readdir($handle)) {
            if ($fileName !== '.' && $fileName !== '..') {
                $files[] = $directory . '/' . $fileName;
            }
        }

        return $files;
    }
}
