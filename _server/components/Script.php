<?php

class Script extends Component
{
    private string $src;
    private bool $defer;

    public function __construct(string $src, bool $defer = false)
    {
        $this->src = $src;
        $this->defer = $defer;
    }

    public function getCode(): string
    {
        $defer = $this->defer ? 'defer' : '';
        return sprintf('<script %s src="%s"></script>', $defer, $this->src);
    }
}
