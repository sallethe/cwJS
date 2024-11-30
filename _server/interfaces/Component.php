<?php

abstract class Component
{

    public abstract function getCode(): string;

    public function render()
    {
        echo $this->getCode();
    }
}
