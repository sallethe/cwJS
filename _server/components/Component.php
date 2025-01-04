<?php

abstract class Component
{
    public function render()
    {
        echo $this->getCode();
    }

    public abstract function getCode(): string;
}
