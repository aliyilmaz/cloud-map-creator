<?php

namespace App\Projections;

abstract class AbstractProjection
{
    protected $width;
    protected $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
}
