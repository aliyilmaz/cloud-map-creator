<?php

namespace App\Projections;

class MercatorProjection extends AbstractProjection
{
    public function x2lon(int $x): float
    {
        $lon  = 360 / $this->width * $x;

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
        $n = ($this->height / 2 - $y) * (2 * M_PI) / $this->width;
        $lat = rad2deg(atan(exp($n)) - M_PI / 4) * 2;

        return $lat;
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
