<?php

namespace Thorazine\Pdf\Core;

class Render
{
    private $height = 0;

    public function render()
    {
        // render and calculate the height of the section
    }

    $temp_doc = new TempDocument(function (WriterAdapter $backup_writer) use (&$height) {
        $backup_writer->addPage();
        $start_y = $backup_writer->getY();
        // Call contents and check height
        call_user_func($this->callable, $backup_writer);
        $height = $backup_writer->getY() - $start_y;
    });
    $temp_doc->make();
}