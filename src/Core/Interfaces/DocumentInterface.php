<?php

namespace Thorazine\Pdf\Core\Interfaces;

interface DocumentInterface
{
    public function getFullPageHeight() : float;
    public function getFullPageWidth() : float;
}