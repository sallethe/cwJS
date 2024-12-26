<?php

class Field extends Component
{
    private string $name;
    private string $type;
    private string $placeholder;

    private string $pattern;
    private bool $required;

    public function __construct(string $name, string $type, string $placeholder, string $pattern = "", bool $required = true)
    {
        $this->name = $name;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->pattern = $pattern;
        $this->required = $required;
    }

    private function addPasswordButton(string $content)
    {
        return sprintf('
            <div>
                %s
                <a onclick="switchPwd(\'%s\')">
                    <img title="Monter/cacher le mot de passe" src="../_public/resources/icons/show.svg" alt="Montrer/cacher" id="%s">
                </a>
            </div>
            '
        , $content, $this->name, $this->name . 'b');
    }

    public function getCode(): string
    {
        $required = $this->required ? 'required' : '';
        $pattern = $this->pattern ? 'pattern="' . $this->pattern . '"' : '';
        $content = sprintf('
            <label for="%s">%s</label>
            <input %s type="%s" id="%s" name="%s" placeholder="%s" %s>',
            $this->name, $this->placeholder, $required, $this->type, $this->name, $this->name, $this->placeholder, $pattern);
        return $this->type == 'password' ? $this->addPasswordButton($content) : $content;
    }
}