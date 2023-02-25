<?php

namespace Thorazine\Pdf\Themes;

trait InvoiceTheme
{

    public function init() : void
    {
        $this->setLogoUrl();
        $this->setLogoWidth();
        $this->setLogoHeight();
    }
}