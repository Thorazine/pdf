<?php

namespace Thorazine\Pdf\Core\Interfaces;

use Thorazine\Pdf\Core\Document;

interface ComponentInterface
{
    public function __construct(Document $document);
    public function render() : void;
}