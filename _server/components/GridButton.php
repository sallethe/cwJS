<?php

class GridButton extends Component
{
    private string $id;
    private int $width;
    private int $height;
    private string $name;
    private bool $editable;

    private int $count;

    public function __construct(string $id, string $name, int $width, int $height, int $count, bool $editable)
    {
        $this->id = $id;
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->editable = $editable;
        $this->count = $count;
    }

    public function getCode(): string
    {
        $buttons = [
            new Button("/cwJS/play?id=" . $this->id, "/cwJS/_public/resources/icons/play.svg", "Jouer", "Play")
        ];

        if ($this->editable) {
            $buttons[] = new Button("/cwJS/create?id=" . $this->id, "/cwJS/_public/resources/icons/edit.svg", "Modifier", "Edit");
            $buttons[] = new Button("/cwJS/_server/handlers/delgrid.php?id=" . $this->id, "/cwJS/_public/resources/icons/delete.svg", "Supprimer", "Delete");
        }

        $buttonsCode = '';
        foreach ($buttons as $button) {
            $buttonsCode .= $button->getCode();
        }

        return sprintf('
        <div class="GridButton">
            <img src="/cwJS/_public/resources/images/grid.svg" alt="Grid">
            <h1>%s</h1>
            <h2>%d x %d, %d mots</h2>
            <div>
                %s
            </div>
        </div>
        ', $this->name, $this->width, $this->height, $this->count, $buttonsCode);
    }
}