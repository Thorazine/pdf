<?php

namespace Thorazine\Pdf\Components;

use Thorazine\Pdf\Core\Component;
use Thorazine\Pdf\Interfaces\ComponentInterface;

class TestComponent extends Component implements ComponentInterface
{
    protected float $top = 0;
    protected float $bottom = 0;
    protected float $left = 0;
    protected float $right = 0;
    protected float $width = 0;
    protected float $height = 0;
    protected float $lineHeight = 0;
    protected array $background = []; // transparent
    protected array $color = [0,0,0];
    protected bool $bold = false;
    protected bool $italic = false;
    protected bool $underline = false;
    protected string $font = 'Helvetica';


    public function render() : void
    {
        
    }
}