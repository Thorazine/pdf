<?php

namespace Thorazine\Pdf\Core;

use Thorazine\Pdf\Core\Document;
use Thorazine\Pdf\Interfaces\ComponentInterface;

class Section implements ComponentInterface
{
    private float $height = 0;
    private bool $break = false;
    private bool $startOnNewPage = false;
    public Document $document;
    public $content;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function render() : void
    {
        // render and calculate the height of the section
    }

    private function newPageOrNot()
    {
        // check if the section should start on a new page
    }


    public function break(bool $break = true)
    {
        $this->break = $break;
    }
}