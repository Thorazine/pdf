<?php

namespace Thorazine\Pdf\Core\Settings;

trait CellSettings
{
    public function cellBorderWidth(float $all, float|null $leftAndRight = null, float|null $bottom = null, float|null $left = null) : self
    {
        $this->setCellValue('BorderWidth', $all, $leftAndRight, $bottom, $left);
        return $this;
    }

    public function cellBorderType(string $all, string|null $leftAndRight = null, string|null $bottom = null, string|null $left = null) : self
    {
        $this->setCellValue('BorderType', $all, $leftAndRight, $bottom, $left);
        return $this;
    }

    public function cellBorderColor(array $all, array|null $leftAndRight = null, array|null $bottom = null, array|null $left = null) : self
    {
        $this->setCellValue('BorderColor', $all, $leftAndRight, $bottom, $left);
        return $this;
    }

    public function cellPadding(float $all, float|null $leftAndRight = null, float|null $bottom = null, float|null $left = null) : self
    {
        $this->setCellValue('Padding', $all, $leftAndRight, $bottom, $left);
        return $this;
    }

    private function setCellValue(string $fieldName, $value1, $value2 = null, $value3 = null, $value4 = null)
    {
        if($value2 === null && $value3 === null && $value4 === null) {
            $this->{'cellTop'.$fieldName} = $value1;
            $this->{'cellRight'.$fieldName} = $value1;
            $this->{'cellBottom'.$fieldName} = $value1;
            $this->{'cellLeft'.$fieldName} = $value1;
            return $this;
        } elseif($value3 === null && $value4 === null) {
            $this->{'cellTop'.$fieldName} = $value1;
            $this->{'cellRight'.$fieldName} = $value2;
            $this->{'cellBottom'.$fieldName} = $value1;
            $this->{'cellLeft'.$fieldName} = $value2;
            return $this;
        }
        elseif($value1 && $value2 && $value3 && $value4) {
            $this->{'cellTop'.$fieldName} = $value1;
            $this->{'cellRight'.$fieldName} = $value2;
            $this->{'cellBottom'.$fieldName} = $value3;
            $this->{'cellLeft'.$fieldName} = $value4;
            return $this;
        }
        throw new \Exception('Invalid border settings. Either 1, 2 or 4 values are allowed.');
    }
}