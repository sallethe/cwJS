<?php

class TopBar extends Component
{
    private array $links;

    public function __construct(array $links)
    {
        $this->links = $links;
    }

    public function getCode(): string
    {
        $linksCode = '';
        foreach ($this->links as $link) {
            $linksCode = $linksCode . $link->getCode();
        }
        return sprintf('
            <div class="TopBar">
                <a href="/cwJS">
                    <img src="/cwJS/_public/resources/images/logo.svg" alt="Logo">
                </a>
                <div>
                    %s
                </div>
            </div>
    ', $linksCode);
    }
}
