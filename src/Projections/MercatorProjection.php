<?php

namespace App\Projections;

class MercatorProjection
{
    public function x2lon(int $x, int $width): float
    {
        $lon  = 360 / $width * $x;

        $lon -= 180;

        return $lon;
    }

    public function lon2x(float $lon, int $width): int
    {
        $lon += 180;
        $x = $width / 360 * $lon;

        $x = $x < 0 ? 0 : $x;

        return $x;
    }

    public function y2lat(int $y, int $width, int $height): float
    {
        $a = 1 / ($width / (2 * M_PI));
        $a = exp(($y * 2) * $a);

        $lat = (asin(($a - 1) / ($a + 1))) * (180.0 / M_PI);

        return $lat; // TODO test it
    }

    public function lat2y(float $lat, int $width, int $height): int
    {
        $n = log(tan(M_PI / 4 + deg2rad($lat / 2)));
        $y = $height / 2 - $width * $n / (2 * M_PI);

        $y = $y < 0 ? 0 : $y;
        $y = $y > $height ? $height : $y;

        return round($y);
    }
}
