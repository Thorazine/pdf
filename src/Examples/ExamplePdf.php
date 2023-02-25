<?php

namespace Thorazine\Pdf\Examples;

use Thorazine\Pdf\Tests;

class ExamplePdf extends Document implements PdfInterface
{
    // adding a theme
    use TestInvoiceTheme;

    private function content()
    {
        $this->setTitle();

        $this->addSection(function($section) {
            $section->break(false)->startOnNewPage(true);

            dd($section);
        });
 
    }


    public function header()
    {
        
    }

    public function footer()
    {
        
    }

    public function inline()
    {
        $this->content();
        $this->inline();
    }
}