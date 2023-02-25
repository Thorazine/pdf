<?php

namespace Thorazine\Pdf\Core\Interfaces;

interface PageTypeInterface
{
    public function getFullPageHeight() : float;
    public function getFullPageWidth() : float;
}