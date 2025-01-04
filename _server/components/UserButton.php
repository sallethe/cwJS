<?php

class UserButton extends Component
{
    private string $id;
    private string $user;
    private string $fn;
    private string $ln;

    public function __construct(string $id, string $user, string $fn, string $ln)
    {
        $this->id = $id;
        $this->user = $user;
        $this->fn = $fn;
        $this->ln = $ln;
    }

    public function getCode(): string
    {
        $buttons = [
            new Button("/cwJS/_server/handlers/logas.php?id=" . $this->id, "/cwJS/_public/resources/icons/login.svg", "Se connecter en tant que " . $this->user, "Log as"),
            new Button("/cwJS/_server/handlers/deluser.php?id=" . $this->id, "/cwJS/_public/resources/icons/error.svg", "Supprimer " . $this->user, "Delete")
        ];

        $buttonsCode = '';
        foreach ($buttons as $button) $buttonsCode .= $button->getCode();

        return sprintf('
        <div class="GridButton">
            <img src="/cwJS/_public/resources/images/account.svg" alt="User">
            <h1>%s</h1>
            <h2>%s %s &bull; ID %s</h2>
            <div>
                %s
            </div>
        </div>
        ', $this->user, $this->fn, $this->ln, $this->id, $buttonsCode);
    }
}