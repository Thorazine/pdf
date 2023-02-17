<?php

namespace Nh1816\PdfGenerator\ThirdParty;

use FPDF;

/**
 * @source https://github.com/AntonTyutin/fpdf/blob/master/examples/writetag/WriteTag.php
 */
class PDF extends FPDF
{
    public $wLine; // Maximum width of the line
    public $hLine; // Height of the line
    public $Text;  // Text to display
    public $border;
    public $align; // Justification of the text
    public $fill;
    public $Padding;
    public $lPadding;
    public $tPadding;
    public $bPadding;
    public $rPadding;
    public $TagStyle; // Style for each tag
    public $Indent;
    public $Space; // Minimum space between words
    public $PileStyle;
    public $Line2Print;    // Line to display
    public $NextLineBegin; // Buffer between lines
    public $TagName;
    public $Delta; // Maximum width minus width
    public $StringLength;
    public $LineLength;
    public $wTextLine; // Width minus paddings
    public $nbSpace;   // Number of spaces in the line
    public $Xini;      // Initial position
    public $href;      // Current URL
    public $TagHref;   // URL for a cell
    public $allowEmpty = true;
    public $defaultTag = 'p';

    protected $noParagraph = false;

    // Public Functions
    public function WriteTag($w, $h, $txt, $border = '', $align = 'J', $fill = false, $padding = '')
    {
        $this->wLine = $w;
        $this->hLine = $h;
        $this->Text = $txt;
        $this->Text = preg_replace('/\n|\r|\t/', '', $this->Text);
        $this->border = $border;
        $this->align = $align;
        $this->fill = $fill;
        $this->Padding = $padding;

        $this->Xini = $this->GetX();
        $this->href = '';
        $this->TagHref = [];
        $this->LastLine = false;

        if ($this->allowEmpty && '' == $this->Text) {
            $this->Text = ' ';
        }

        $this->Padding();
        $this->LineLength();

        $this->BorderTop();

        while ('' != $this->Text) {
            $this->MakeLine();
            $this->PrintLine();
        }

        $this->BorderBottom();

        $this->SetXY($this->Xini, $this->GetY());
    }

    public function SetStyle($tag, $family, $style, $size, $color, $indent = -1)
    {
        $tag = trim($tag);
        $this->TagStyle[$tag]['family'] = trim($family);
        $this->TagStyle[$tag]['style'] = trim($style);
        $this->TagStyle[$tag]['size'] = trim($size);
        $this->TagStyle[$tag]['color'] = trim($color);
        $this->TagStyle[$tag]['indent'] = $indent;
    }

    // Private Functions
    private function SetSpace() // Minimal space between words
    {
        $this->Space = $this->GetStringWidth(' ');
    }

    protected function Padding()
    {
        if (preg_match('/^.+,/', $this->Padding)) {
            $tab = explode(',', $this->Padding);
            $this->lPadding = $tab[0];
            $this->tPadding = $tab[1];
            if (isset($tab[2])) {
                $this->bPadding = $tab[2];
            } else {
                $this->bPadding = $this->tPadding;
            }
            if (isset($tab[3])) {
                $this->rPadding = $tab[3];
            } else {
                $this->rPadding = $this->lPadding;
            }
        } else {
            $this->lPadding = $this->Padding;
            $this->tPadding = $this->Padding;
            $this->bPadding = $this->Padding;
            $this->rPadding = $this->Padding;
        }
        if ($this->tPadding < $this->LineWidth) {
            $this->tPadding = $this->LineWidth;
        }
    }

    private function LineLength()
    {
        if (0 == $this->wLine) {
            $this->wLine = $this->w - $this->Xini - $this->rMargin;
        }

        $this->wTextLine = $this->wLine - $this->lPadding - $this->rPadding;
    }

    private function BorderTop()
    {
        $border = 0;
        if (1 == $this->border) {
            $border = 'TLR';
        }

        $this->Cell($this->wLine, $this->tPadding, '', $border, 0, 'C', $this->fill);
        $y = $this->GetY() + $this->tPadding;
        $this->SetXY($this->Xini, $y);
    }

    private function BorderBottom()
    {
        $border = 0;
        if (1 == $this->border) {
            $border = 'BLR';
        }

        $this->Cell($this->wLine, $this->bPadding, '', $border, 0, 'C', $this->fill);
    }

    private function DoStyle($tag)
    {
        $tag = trim($tag);
        if (! isset($this->TagStyle[$tag])) {
            $tag = $this->defaultTag;
        }

        $this->SetFont(
            $this->TagStyle[$tag]['family'],
            $this->TagStyle[$tag]['style'],
            $this->TagStyle[$tag]['size']
        );

        $tab = explode(',', $this->TagStyle[$tag]['color']);
        if (1 == count($tab)) {
            if (strlen($tab[0])) {
                if (! is_int($this->TagStyle[$tag]['color'])) {
                    $color = str_replace('#', '', $this->TagStyle[$tag]['color']);
                    if (3 == strlen($color)) {
                        $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
                    }

                    $this->SetTextColor(...sscanf($color, '%02x%02x%02x'));
                } else {
                    $this->SetTextColor($tab[0]);
                }
            }
        } else {
            $this->SetTextColor($tab[0], $tab[1], $tab[2]);
        }
    }

    private function FindStyle($tag, $ind)
    {
        $tag = trim($tag);

        if (! $this->TagStyle || ! array_key_exists($tag, $this->TagStyle)) {
            return;
        }

        // Family
        if ('' != $this->TagStyle[$tag]['family']) {
            $family = $this->TagStyle[$tag]['family'];
        } else {
            reset($this->PileStyle);
            foreach ($this->PileStyle as $k => $val) {
                $val = trim($val);
                if ('' != $this->TagStyle[$val]['family']) {
                    $family = $this->TagStyle[$val]['family'];

                    break;
                }
            }
        }

        // Style
        $style = '';
        $style1 = strtoupper($this->TagStyle[$tag]['style']);
        if ('N' != $style1) {
            $bold = false;
            $italic = false;
            $underline = false;
            reset($this->PileStyle);
            foreach ($this->PileStyle as $k => $val) {
                $val = trim($val);
                $style1 = strtoupper($this->TagStyle[$val]['style']);
                if ('N' == $style1) {
                    break;
                }
                if (str_contains($style1, 'B')) {
                    $bold = true;
                }
                if (str_contains($style1, 'I')) {
                    $italic = true;
                }
                if (str_contains($style1, 'U')) {
                    $underline = true;
                }
            }
            if ($bold) {
                $style .= 'B';
            }
            if ($italic) {
                $style .= 'I';
            }
            if ($underline) {
                $style .= 'U';
            }
        }

        // Size
        if (0 != $this->TagStyle[$tag]['size']) {
            $size = $this->TagStyle[$tag]['size'];
        } else {
            reset($this->PileStyle);
            foreach ($this->PileStyle as $k => $val) {
                $val = trim($val);
                if (0 != $this->TagStyle[$val]['size']) {
                    $size = $this->TagStyle[$val]['size'];
                    break;
                }
            }
        }

        // Color
        if ('' != $this->TagStyle[$tag]['color']) {
            $color = $this->TagStyle[$tag]['color'];
        } else {
            reset($this->PileStyle);
            foreach ($this->PileStyle as $k => $val) {
                $val = trim($val);
                if ('' != $this->TagStyle[$val]['color']) {
                    $color = $this->TagStyle[$val]['color'];
                    break;
                }
            }
        }

        // Result
        @$this->TagStyle[$ind]['family'] = $family;
        @$this->TagStyle[$ind]['style'] = $style;
        @$this->TagStyle[$ind]['size'] = $size;
        @$this->TagStyle[$ind]['color'] = $color;
        @$this->TagStyle[$ind]['indent'] = $this->TagStyle[$tag]['indent'];
    }

    public const LT = '{';
    public const RT = '}';

    private function Parser($text)
    {
        $tab = [];

        // Closing tag
        if (preg_match('|^(' . preg_quote(self::LT) . '/([^' . preg_quote(self::RT) . ']+)' . preg_quote(self::RT) . ')|s', $text, $regs)) {
            $tab[1] = 'c';
            $tab[2] = trim($regs[2]);

            // kijk of de tag bestaat!
            if ($this->TagStyle && ! array_key_exists($tab[2], $this->TagStyle)) {
                $this->TagStyle[$tab[2]] = $this->TagStyle[$this->defaultTag];
            }
        } // Opening tag
        else {
            if (preg_match('|^(' . preg_quote(self::LT) . '([^' . preg_quote(self::RT) . ']+)' . preg_quote(self::RT) . ')|s', $text, $regs)) {
                // Spaties worden uit de tags gehaald, dus links ( {ahref=''} ) moeten worden gefixed ( {a href=''} )
                $styles = array_keys($this->TagStyle);
                arsort($styles);

                $regs[2] = preg_replace('/^(' . implode('|', $styles) . ')(.*)$/', '$1 $2', $regs[2]);
                $tab[1] = 'o';
                $tab[2] = trim($regs[2]);

                // Presence of attributes
                if (preg_match("/(.+) (.+)=(\"|')(.+)(\"|')/", $regs[2])) {
                    // Split op spaties
                    $tab1 = preg_split('/ +/', $regs[2]);
                    // De tag is het element voor de eerste spatie, het eerste element dus:
                    $tab[2] = trim(array_shift($tab1));
                    // Loop door de attributes heen, $couple is hier [key="value"]
                    foreach ($tab1 as $i => $couple) {
                        // Split op key en value
                        $tab2 = explode('=', $couple);
                        // Key:
                        $tab2[0] = trim($tab2[0]);
                        // Value:
                        $tab2[1] = trim($tab2[1], '\'" ');
                        // Onthoud de attribute:
                        $tab[$tab2[0]] = $tab2[1];
                    }
                }

                // kijk of de tag bestaat!
                if ($this->TagStyle && ! array_key_exists($tab[2], $this->TagStyle)) {
                    $this->TagStyle[$tab[2]] = $this->TagStyle[$this->defaultTag];
                }
            } // Space
            else {
                if (preg_match('/^( )/', $text, $regs)) {
                    $tab[1] = 's';
                    $tab[2] = ' ';
                } // Text
                else {
                    if (preg_match('/^([^' . preg_quote(self::LT) . ' ]+)/s', $text, $regs)) {
                        $tab[1] = 't';
                        $tab[2] = $regs[1];
                    }
                }
            }
        }

        if (isset($regs[1])) {
            $begin = strlen($regs[1]);
            $end = strlen($text);
            $text = substr($text, $begin, $end);
        }

        $tab[0] = $text;

        return $tab;
    }

    protected $curTags = [];
    protected $widthRemaining = 0;

    private function MakeLine()
    {
        $this->Text .= ' ';
        $startText = $this->Text;
        $this->LineLength = [];
        $this->TagHref = [];
        $Length = 0;
        $this->nbSpace = 0;

        $i = $this->BeginLine();
        $this->TagName = [];

        if (0 == $i) {
            $Length = $this->StringLength[0];
            $this->TagName[0] = 1;
            $this->TagHref[0] = $this->href;
        }

        while ($Length < $this->wTextLine) {
            $tab = $this->Parser($this->Text);

            $this->Text = $tab[0];
            if ('' == $this->Text) {
                $this->LastLine = true;
                break;
            }

            if ('o' == $tab[1]) {
                array_unshift($this->PileStyle, $tab[2]);
                $this->FindStyle($this->PileStyle[0], $i + 1);

                $this->DoStyle($i + 1);
                $this->TagName[$i + 1] = 1;
                if (-1 != $this->TagStyle[$tab[2]]['indent']) {
                    $Length += $this->TagStyle[$tab[2]]['indent'];
                    $this->Indent = $this->TagStyle[$tab[2]]['indent'];
                }
                if ('a' == $tab[2]) {
                    $this->href = $tab['href'];
                }

                $this->curTags[] = $tab;
                if (isset($tab['width'])) {
                    $this->widthRemaining = $tab['width'];
                }

                $this->SetSpace();
            }

            if ('c' == $tab[1]) {
                array_shift($this->PileStyle);
                array_pop($this->curTags);

                if (isset($this->PileStyle[0])) {
                    $this->FindStyle($this->PileStyle[0], $i + 1);
                    $this->DoStyle($i + 1);
                }
                $this->TagName[$i + 1] = 1;
                if (-1 != $this->TagStyle[$tab[2]]['indent']) {
                    $this->LastLine = true;
                    $this->Text = $this->Text;
                    break;
                }
                if ('a' == $tab[2]) {
                    $this->href = '';
                }

                if ($this->widthRemaining > 0) {
                    ++$i;
                    $this->StringLength[$i] = $this->widthRemaining;
                    $Length += $this->StringLength[$i];
                    $this->LineLength[$i] = $Length;
                    $this->Line2Print[$i] = ' ';
                }

                $this->widthRemaining = 0;
            }

            if ('s' == $tab[1]) {
                ++$i;
                $curTag = end($this->curTags);

                if (isset($curTag['width'])) {
                    $this->widthRemaining -= $this->Space;
                }
                $Length += $this->Space;
                $this->Line2Print[$i] = '';
                if ('' != $this->href) {
                    $this->TagHref[$i] = $this->href;
                }
            }

            if ('t' == $tab[1]) {
                ++$i;
                $curTag = end($this->curTags);

                $this->StringLength[$i] = $this->GetStringWidth($tab[2]);
                if (isset($curTag['width'])) {
                    $this->widthRemaining -= $this->StringLength[$i];
                }
                $Length += $this->StringLength[$i];
                $this->LineLength[$i] = $Length;
                $this->Line2Print[$i] = $tab[2];
                if ('' != $this->href) {
                    $this->TagHref[$i] = $this->href;
                }
            }
        }

        if ($startText == $this->Text) {
            $this->LastLine = true;
            $this->noParagraph = true;
        }

        if ($Length > $this->wTextLine || true == $this->LastLine) {
            $this->EndLine();
        }
    }

    private function BeginLine()
    {
        $this->Line2Print = [];
        $this->StringLength = [];

        if (isset($this->PileStyle[0])) {
            $this->FindStyle($this->PileStyle[0], 0);
            $this->DoStyle(0);
        }

        if (is_array($this->NextLineBegin) && count($this->NextLineBegin) > 0) {
            $this->Line2Print[0] = $this->NextLineBegin['text'];
            $this->StringLength[0] = $this->NextLineBegin['length'];
            $this->NextLineBegin = [];
            $i = 0;
        } else {
            preg_match('/^(( *(' . preg_quote(self::LT) . '([^' . preg_quote(self::RT) . ']+)' . preg_quote(self::RT) . ')* *)*)(.*)/', $this->Text, $regs);
            $regs[1] = str_replace(' ', '', $regs[1]);
            $this->Text = $regs[1] . $regs[5];
            $i = -1;
        }

        return $i;
    }

    private function EndLine()
    {
        if ('' != end($this->Line2Print) && false == $this->LastLine) {
            $this->NextLineBegin['text'] = array_pop($this->Line2Print);
            $this->NextLineBegin['length'] = end($this->StringLength);
            array_pop($this->LineLength);
        }

        while ('' === end($this->Line2Print)) {
            array_pop($this->Line2Print);
        }

        $lineLength = end($this->LineLength);
        if (false === $lineLength) {
            $lineLength = end($this->StringLength);
        }

        $this->Delta = $this->wTextLine - $lineLength;

        $this->nbSpace = 0;
        for ($i = 0; $i < (is_countable($this->Line2Print) ? count($this->Line2Print) : 0); ++$i) {
            if ('' == $this->Line2Print[$i]) {
                ++$this->nbSpace;
            }
        }
    }

    private function PrintLine()
    {
        $border = 0;
        if (1 == $this->border) {
            $border = 'LR';
        }
        $this->Cell($this->wLine, $this->hLine, '', $border, 0, 'C', $this->fill);
        $y = $this->GetY();
        $this->SetXY($this->Xini + $this->lPadding, $y);

        if (-1 != $this->Indent) {
            if (0 != $this->Indent) {
                $this->Cell($this->Indent, $this->hLine);
            }
            $this->Indent = -1;
        }

        $space = $this->LineAlign();
        $this->DoStyle(0);

        for ($i = 0; $i < (is_countable($this->Line2Print) ? count($this->Line2Print) : 0); ++$i) {
            if (isset($this->TagName[$i])) {
                $this->DoStyle($i);
            }
            if (isset($this->TagHref[$i])) {
                $href = $this->TagHref[$i];
            } else {
                $href = '';
            }
            if ('' == $this->Line2Print[$i]) {
                $this->Cell($space, $this->hLine, '         ', 0, 0, 'L', false, $href);
            } else {
                $this->Cell($this->StringLength[$i], $this->hLine, $this->Line2Print[$i], 0, 0, 'L', false, $href);
            }
        }

        $this->LineBreak();
        if ($this->LastLine && '' != $this->Text) {
            $this->EndParagraph();
        }
        $this->LastLine = false;
    }

    private function LineAlign()
    {
        $space = $this->Space;
        if ('J' == $this->align) {
            if (0 != $this->nbSpace) {
                $space = $this->Space + ($this->Delta / $this->nbSpace);
            }
            if ($this->LastLine) {
                $space = $this->Space;
            }
        }

        if ('R' == $this->align) {
            $this->Cell($this->Delta, $this->hLine);
        }

        if ('C' == $this->align) {
            $this->Cell($this->Delta / 2, $this->hLine);
        }

        return $space;
    }

    private function LineBreak()
    {
        $x = $this->Xini;
        $y = $this->GetY() + $this->hLine;
        $this->SetXY($x, $y);
    }

    private function EndParagraph()
    {
        if ($this->noParagraph) {
            $this->noParagraph = false;

            return;
        }

        $border = 0;
        if (1 == $this->border) {
            $border = 'LR';
        }
        $this->Cell($this->wLine, $this->hLine / 2, '', $border, 0, 'C', $this->fill);
        $x = $this->Xini;
        $y = $this->GetY() + $this->hLine / 2;
        $this->SetXY($x, $y);
    }

    /**
     * @source https://stackoverflow.com/questions/11126354/fpdf-letter-spacing
     */
    protected $LetterSpacingPt;      // current letter spacing in points
    protected $LetterSpacing;        // current letter spacing in user units

    public function SetLetterSpacing($size)
    {
        if ($this->LetterSpacingPt == $size) {
            return;
        }
        $this->LetterSpacingPt = $size;
        $this->LetterSpacing = $size / $this->k;
        if ($this->page > 0) {
            $this->_out(sprintf('BT %.3f Tc ET', $size));
        }
    }

    protected function _dounderline($x, $y, $txt)
    {
        // Underline text
        $up = $this->CurrentFont['up'];
        $ut = $this->CurrentFont['ut'];
        $w = $this->GetStringWidth($txt) + $this->ws * substr_count($txt, ' ') + (strlen($txt) - 1) * $this->LetterSpacing;

        return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, ($this->h - ($y - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function GetStringWidthOfLastLineInMultiCell(string $string, int $width)
    {
        //Get width of a string in the current font
        $words = explode(' ', $string);

        $string_width = 0;
        $spaceWidth = $this->GetStringWidth(' ');

        for ($i = 0, $words_count = count($words); $i < $words_count; $i++) {
            // sum up all the words' width, and add space withs between the words
            $string_width += $this->GetStringWidth($words[$i]) + $spaceWidth;
            if ($string_width > $width) {
                //if current width is more than the line width, then the current word
                //will be moved to next line, we need to count it again
                $i--;
            }

            if ($string_width >= $width) {
                //if the current width is equal or grater than the line width,
                //we need to reset current width, and count the width of remaining text
                $string_width = 0;
            }
        }
        //at last, we have only the width of the text that remain on the last line!
        return $string_width;
    }

    //Private properties
    var $tmpFiles = [];

    /*******************************************************************************
     *                                                                              *
     *                               Public methods                                 *
     *                                                                              *
     *******************************************************************************/
    function Image($file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '', $isMask = false, $maskImg = 0)
    {
        //Put an image on the page
        if (! isset($this->images[$file])) {
            //First use of this image, get info
            if ($type == '') {
                $pos = strrpos($file, '.');
                if (! $pos)
                    $this->Error('Image file has no extension and no type was specified: ' . $file);
                $type = substr($file, $pos + 1);
            }
            $type = strtolower($type);
            if ($type == 'png') {
                $info = $this->_parsepng($file);
                if ($info == 'alpha')
                    return $this->ImagePngWithAlpha($file, $x, $y, $w, $h, $link);
            } else {
                if ($type == 'jpeg')
                    $type = 'jpg';
                $mtd = '_parse' . $type;
                if (! method_exists($this, $mtd))
                    $this->Error('Unsupported image type: ' . $type);
                $info = $this->$mtd($file);
            }
            if ($isMask) {
                if (in_array($file, $this->tmpFiles))
                    $info['cs'] = 'DeviceGray'; //hack necessary as GD can't produce gray scale images
                if ($info['cs'] != 'DeviceGray')
                    $this->Error('Mask must be a gray scale image');
                if ($this->PDFVersion < '1.4')
                    $this->PDFVersion = '1.4';
            }
            $info['i'] = count($this->images) + 1;
            if ($maskImg > 0)
                $info['masked'] = $maskImg;
            $this->images[$file] = $info;
        } else
            $info = $this->images[$file];
        //Automatic width and height calculation if needed
        if ($w == 0 && $h == 0) {
            //Put image at 72 dpi
            $w = $info['w'] / $this->k;
            $h = $info['h'] / $this->k;
        } elseif ($w == 0)
            $w = $h * $info['w'] / $info['h'];
        elseif ($h == 0)
            $h = $w * $info['h'] / $info['w'];
        //Flowing mode
        if ($y === null) {
            if ($this->y + $h > $this->PageBreakTrigger && ! $this->InHeader && ! $this->InFooter && $this->AcceptPageBreak()) {
                //Automatic page break
                $x2 = $this->x;
                $this->AddPage($this->CurOrientation, $this->CurPageFormat);
                $this->x = $x2;
            }
            $y = $this->y;
            $this->y += $h;
        }
        if ($x === null)
            $x = $this->x;
        if (! $isMask)
            $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k, $info['i']));
        if ($link)
            $this->Link($x, $y, $w, $h, $link);
        return $info['i'];
    }

// needs GD 2.x extension
// pixel-wise operation, not very fast
    function ImagePngWithAlpha($file, $x, $y, $w = 0, $h = 0, $link = '')
    {
        $tmp_alpha = tempnam('.', 'mska');
        $this->tmpFiles[] = $tmp_alpha;
        $tmp_plain = tempnam('.', 'mskp');
        $this->tmpFiles[] = $tmp_plain;

        [$wpx, $hpx] = getimagesize($file);
        $img = imagecreatefrompng($file);
        $alpha_img = imagecreate($wpx, $hpx);

        // generate gray scale pallete
        for ($c = 0; $c < 256; $c++)
            ImageColorAllocate($alpha_img, $c, $c, $c);

        // extract alpha channel
        $xpx = 0;
        while ($xpx < $wpx) {
            $ypx = 0;
            while ($ypx < $hpx) {
                $color_index = imagecolorat($img, $xpx, $ypx);
                $col = imagecolorsforindex($img, $color_index);
                imagesetpixel($alpha_img, $xpx, $ypx, $this->_gamma((127 - $col['alpha']) * 255 / 127));
                ++$ypx;
            }
            ++$xpx;
        }

        imagepng($alpha_img, $tmp_alpha);
        imagedestroy($alpha_img);

        // extract image without alpha channel
        $plain_img = imagecreatetruecolor($wpx, $hpx);
        imagecopy($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx);
        imagepng($plain_img, $tmp_plain);
        imagedestroy($plain_img);

        //first embed mask image (w, h, x, will be ignored)
        $maskImg = $this->Image($tmp_alpha, 0, 0, 0, 0, 'PNG', '', true);

        //embed image, masked with previously embedded mask
        $this->Image($tmp_plain, $x, $y, $w, $h, 'PNG', $link, false, $maskImg);
    }

    function Close()
    {
        parent::Close();
        // clean up tmp files
        foreach ($this->tmpFiles as $tmp)
            @unlink($tmp);
    }

    /*******************************************************************************
     *                                                                              *
     *                               Private methods                                *
     *                                                                              *
     *******************************************************************************/
    function _putimages()
    {
        $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
        reset($this->images);
        foreach ($this->images as $file => $info) {
            $this->_newobj();
            $this->images[$file]['n'] = $this->n;
            $this->_out('<</Type /XObject');
            $this->_out('/Subtype /Image');
            $this->_out('/Width ' . $info['w']);
            $this->_out('/Height ' . $info['h']);

            if (isset($info['masked']))
                $this->_out('/SMask ' . ($this->n - 1) . ' 0 R');

            if ($info['cs'] == 'Indexed')
                $this->_out('/ColorSpace [/Indexed /DeviceRGB ' . (strlen($info['pal']) / 3 - 1) . ' ' . ($this->n + 1) . ' 0 R]');
            else {
                $this->_out('/ColorSpace /' . $info['cs']);
                if ($info['cs'] == 'DeviceCMYK')
                    $this->_out('/Decode [1 0 1 0 1 0 1 0]');
            }
            $this->_out('/BitsPerComponent ' . $info['bpc']);
            if (isset($info['f']))
                $this->_out('/Filter /' . $info['f']);
            if (isset($info['parms']))
                $this->_out($info['parms']);
            if (isset($info['trns']) && is_array($info['trns'])) {
                $trns = '';
                for ($i = 0; $i < count($info['trns']); $i++)
                    $trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
                $this->_out('/Mask [' . $trns . ']');
            }
            $this->_out('/Length ' . strlen($info['data']) . '>>');
            $this->_putstream($info['data']);
            unset($this->images[$file]['data']);
            $this->_out('endobj');
            //Palette
            if ($info['cs'] == 'Indexed') {
                $this->_newobj();
                $pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
                $this->_out('<<' . $filter . '/Length ' . strlen($pal) . '>>');
                $this->_putstream($pal);
                $this->_out('endobj');
            }
        }
    }

// GD seems to use a different gamma, this method is used to correct it again
    function _gamma($v)
    {
        return pow($v / 255, 2.2) * 255;
    }

// this method overriding the original version is only needed to make the Image method support PNGs with alpha channels.
// if you only use the ImagePngWithAlpha method for such PNGs, you can remove it from this script.
    function _parsepng($file)
    {
        //Extract info from a PNG file
        $f = fopen($file, 'rb');
        if (! $f)
            $this->Error('Can\'t open image file: ' . $file);
        //Check signature
        if ($this->_readstream($f, 8) != chr(137) . 'PNG' . chr(13) . chr(10) . chr(26) . chr(10))
            $this->Error('Not a PNG file: ' . $file);
        //Read header chunk
        $this->_readstream($f, 4);
        if ($this->_readstream($f, 4) != 'IHDR')
            $this->Error('Incorrect PNG file: ' . $file);
        $w = $this->_readint($f);
        $h = $this->_readint($f);
        $bpc = ord($this->_readstream($f, 1));
        if ($bpc > 8)
            $this->Error('16-bit depth not supported: ' . $file);
        $ct = ord($this->_readstream($f, 1));
        if ($ct == 0)
            $colspace = 'DeviceGray';
        elseif ($ct == 2)
            $colspace = 'DeviceRGB';
        elseif ($ct == 3)
            $colspace = 'Indexed';
        else {
            fclose($f);      // the only changes are
            return 'alpha';  // made in those 2 lines
        }
        if (ord($this->_readstream($f, 1)) != 0)
            $this->Error('Unknown compression method: ' . $file);
        if (ord($this->_readstream($f, 1)) != 0)
            $this->Error('Unknown filter method: ' . $file);
        if (ord($this->_readstream($f, 1)) != 0)
            $this->Error('Interlacing not supported: ' . $file);
        $this->_readstream($f, 4);
        $parms = '/DecodeParms <</Predictor 15 /Colors ' . ($ct == 2 ? 3 : 1) . ' /BitsPerComponent ' . $bpc . ' /Columns ' . $w . '>>';
        //Scan chunks looking for palette, transparency and image data
        $pal = '';
        $trns = '';
        $data = '';
        do {
            $n = $this->_readint($f);
            $type = $this->_readstream($f, 4);
            if ($type == 'PLTE') {
                //Read palette
                $pal = $this->_readstream($f, $n);
                $this->_readstream($f, 4);
            } elseif ($type == 'tRNS') {
                //Read transparency info
                $t = $this->_readstream($f, $n);
                if ($ct == 0)
                    $trns = [ord(substr($t, 1, 1))];
                elseif ($ct == 2)
                    $trns = [ord(substr($t, 1, 1)), ord(substr($t, 3, 1)), ord(substr($t, 5, 1))];
                else {
                    $pos = strpos($t, chr(0));
                    if ($pos !== false)
                        $trns = [$pos];
                }
                $this->_readstream($f, 4);
            } elseif ($type == 'IDAT') {
                //Read image data block
                $data .= $this->_readstream($f, $n);
                $this->_readstream($f, 4);
            } elseif ($type == 'IEND')
                break;
            else
                $this->_readstream($f, $n + 4);
        } while ($n);
        if ($colspace == 'Indexed' && empty($pal))
            $this->Error('Missing palette in ' . $file);
        fclose($f);
        return ['w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'FlateDecode', 'parms' => $parms, 'pal' => $pal, 'trns' => $trns, 'data' => $data];
    }

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        if (strpos($corners, '2') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $y) * $k));
        else
            $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '3') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        if (strpos($corners, '4') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);

        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '1') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $y) * $k));
            $this->_out(sprintf('%.2F %.2F l', ($x + $r) * $k, ($hp - $y) * $k));
        } else
            $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

    function checkbox(bool $checked, int $checkbox_size = 5, string $zapfdingbats_character = '4'): void
    {
        $check = '';

        if ($checked) {
            $check = $zapfdingbats_character;
        }

        $font = $this->FontFamily;
        $font_style = $this->FontStyle;
        $font_size = $this->FontSize;

        $this->SetFont('ZapfDingbats', '', $font_size * 3);
        $this->Cell($checkbox_size, $checkbox_size, $check, 1);
        $this->SetFont($font, $font_style, $font_size);
    }
}
