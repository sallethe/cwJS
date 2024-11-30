<?php

class Button extends Component
{
    private string $href;
    private string $title;
    private string $alt;
    private string $img;
    private string $id;

    public function __construct(string $href, string $img, string $title = '', string $alt = '', string $id = '')
    {
        $this->href = $href;
        $this->title = $title;
        $this->alt = $alt;
        $this->img = $img;
        $this->id = $id;
    }

    public function getCode(): string
    {
        $id = $this->id == '' ? '' : 'id="' . $this->id . '"';
        $href = $this->href == '' ? '' : 'href="' . $this->href . '"';
        $title = $this->title == '' ? '' : 'title="' . $this->title . '"';
        $alt = 'alt="' . ($this->alt ?? $this->img) . '"';

        return sprintf('
            <a class="Button" %s %s %s>
                <img src="%s" %s>
            </a>',
            $id, $href, $title, $this->img, $alt);
    }
}