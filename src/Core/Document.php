<?php

namespace Thorazine\Pdf\Core;

use Hamcrest\Core\Set;
use Thorazine\Pdf\Core\Settings\Settings;
use Thorazine\Pdf\Core\Traits\PageTypes\A4;
use Thorazine\Pdf\Core\Settings\SectionSettings;
use Thorazine\Pdf\Core\Interfaces\DocumentInterface;

class Document implements DocumentInterface
{
    use A4; // default page type
    use Settings;
    

    protected array $sections = [];

    

    public function header()
    {

    }

    public function footer()
    {

    }

    public function addSection(callable $function, SectionSettings $setting = null)
    {
        $section = new Section($this);
        $section->content = $function;
        array_push($this->sections, $section);
        return $section;
    }


    private function build()
    {
        // $this->startDocument();
        // $this->AddPage(); 

        $leftOverPageHeight = $this->getFullPageHeight();

        foreach($this->sections as $section) {
            if($section['start_on_new_page'] && $leftOverPageHeight < $section->height()) {
                // $this->AddPage();
            }

            // deduct the height of the section from the left over page height
            $leftOverPageHeight = $this->calculateLeftOverPageHeight($leftOverPageHeight, $section->height);
        }
    }

    private function calculateLeftOverPageHeight($leftOverPageHeight, $sectionHeight)
    {
        $leftOverPageHeight = $leftOverPageHeight - $sectionHeight;
           
        // if the left over page height is less than 0, we need to add a new page
        if($leftOverPageHeight < 0) {
            $pages = floor($sectionHeight / $this->getFullPageHeight());
            $leftOverPageHeight = $sectionHeight - ($pages * $this->getFullPageHeight());
        }

        return $leftOverPageHeight;
    }


}