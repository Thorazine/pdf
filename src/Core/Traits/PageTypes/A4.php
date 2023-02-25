<?php

namespace Thorazine\Pdf\Core\Traits\PageTypes;

trait A4 
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