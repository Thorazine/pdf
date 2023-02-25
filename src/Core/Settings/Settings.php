<?php

namespace Thorazine\Pdf\Core\Settings;

class Settings 
{
    use CellSettings;
    use RowSettings;
    use TableSettings;
    use SectionSettings;
    use PageSettings;
    use DocumentSettings;

    // document settings
    protected bool $passwordProtected = false;
    protected string $password;

    // page settings
    protected $pageTopMargin = 0;
    protected $pageBottomMargin = 0;
    protected $pageLeftMargin = 0;
    protected $pageRightMargin = 0;

    // section settings

    // table settings

    // row settings
    protected $rowSpacing = 0;

    // cell settings
    protected string $cellAlign = 'left';
    protected string $cellVerticalAlign = 'top';
    protected float $cellWidth = 0;
    protected float $cellHeight = 0;
    // cell top settings
    protected float $cellTopBorderWidth = 0;
    protected array $cellTopBorderColor = [0, 0, 0];
    protected string $cellTopBorderType = 'solid';
    protected float $cellTopPadding = 0;
    // cell right settings
    protected float $cellRightBorderWidth = 0;
    protected array $cellRightBorderColor = [0, 0, 0];
    protected string $cellRightBorderType = 'solid';
    protected float $cellRightPadding = 0;
    // cell bottom settings
    protected float $cellBottomBorderWidth = 0;
    protected array $cellBottomBorderColor = [0, 0, 0];
    protected string $cellBottomBorderType = 'solid';
    protected float $cellBottomPadding = 0;
    // cell left settings
    protected float $cellLeftBorderWidth = 0;
    protected array $cellLeftBorderColor = [0, 0, 0];
    protected string $cellLeftBorderType = 'solid';
    protected float $cellLeftPadding = 0;


    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $property = lcfirst(substr($name, 3));
            $this->$property = $arguments[0];
        }

        if (substr($name, 0, 3) == 'get') {
            $property = lcfirst(substr($name, 3));
            return $this->$property;
        }
    }
}