<?php

class Header extends Component
{
    private string $title = 'Header';
    private array $scripts = [];

    public function __construct(string $title, array $scripts = [])
    {
        $this->scripts = $scripts;
        $this->title = $title;
    }

    public function getCode(): string
    {
        $scriptsCode = '';
        foreach ($this->scripts as $script) $scriptsCode = $scriptsCode . $script->getCode();
        return sprintf('
            <head>
                <meta charset="UTF-8">
                <title>%s</title>
                %s
                <link rel="icon" href="/cwJS/_public/resources/icons/favicon.svg">
                <link rel="stylesheet" href="/cwJS/_public/style/main.css">
            </head>'
            , $this->title, $scriptsCode);
    }
}