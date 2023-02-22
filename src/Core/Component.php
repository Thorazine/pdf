<?php

namespace Thorazine\Pdf\Core;

class Component
{
    public function __call($name, $arguments)
    {
        $property = lcfirst(substr($name, 3));
        if (substr($name, 0, 3) === 'get') {
            return $this->$property;
        } elseif (substr($name, 0, 3) === 'set') {
            $this->$property = $arguments[0];
            return $this;
        }
    }
}