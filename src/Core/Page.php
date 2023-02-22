<?php

namespace Thorazine\Pdf\Core;

class Page extends Document
{
    private $takenHeight = 0;
    const FULL_PAGE_HEIGHT = 100;

    private function getAvailableContentHeight()
    {
        // calculate the available height for content
    }
}