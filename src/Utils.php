<?php

namespace App;

class Utils
{
    public static function replace(string $string, array $replaceArray): string
    {
        foreach ($replaceArray as $key => $value) {
            $string = str_replace('{' . $key . '}', $value, $string);
        }

        return $string;
    }
}
