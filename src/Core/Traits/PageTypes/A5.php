<?php

namespace Thorazine\Pdf\Core\Traits\PageTypes;

class A5
{
    private float $fullPageHeight = 0;
    private float $fullPageWidth = 0;

    public function getFullPageHeight() : float
    {
        return $this->fullPageHeight;
    }

    public function getFullPageWidth() : float
    {
        return $this->fullPageWidth;
    }
}