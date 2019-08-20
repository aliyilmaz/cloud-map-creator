<?php

namespace App\Projections;

class MercatorProjection extends AbstractProjection
{
    public function x2lon(int $x): float
    {
        $lon  = 360 / $this->width * $x;

        $lon -= 180;

        return $lon;
    }

    public function lon2x(float $lon): int
    {
        $lon += 180;
        $x = $this->width / 360 * $lon;

        $x = $x < 0 ? 0 : $x;

        return $x;
    }

    public function y2lat(int $y): float
    {
        $a = 1 / ($this->width / (2 * M_PI));
//        $a = exp(($y * 2) * $a);
//
//        $lat = (asin(($a - 1) / ($a + 1))) * (180.0 / M_PI);

//        $latRad = atan(exp(($a * -1 * $y))) - M_PI / 2;
//        $lat = rad2deg($latRad);

        $n = (($this->height / 2 - $y) * (2 * M_PI)) / $this->width;
        $lat = (rad2deg(atan(exp($n))) - M_PI / 4) * 2;

        return $lat; // TODO test it
    }

    public function lat2y(float $lat): int
    {
        $n = log(tan(M_PI / 4 + deg2rad($lat / 2)));
        $y = $this->height / 2 - $this->width * $n / (2 * M_PI);

        $y = $y < 0 ? 0 : $y;
        $y = $y > $this->height ? $this->height : $y;

        return round($y);
    }
}
