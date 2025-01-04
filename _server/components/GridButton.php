<?php

class GridButton extends Component
{
    private string $id;
    private int $dims;
    private string $name;
    private bool $editable;
    private string $diff;

    private int $count;

    public function __construct(string $id, string $name, int $dims, int $diff, int $count, bool $editable)
    {
        $this->id = $id;
        $this->name = $name;
        $this->dims = $dims;
        $this->diff = $diff;
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
        foreach ($buttons as $button) $buttonsCode .= $button->getCode();

        $diff = match ($this->diff) {
            3 => "Difficile",
            2 => "Moyen",
            default => "Facile",
        };

        return sprintf('
        <div class="GridButton">
            <img src="/cwJS/_public/resources/images/grid.svg" alt="Grid">
            <h1>%s</h1>
            <h2>%d x %d  &bull;  %d mots  &bull;  %s</h2>
            <div>
                %s
            </div>
        </div>
        ', $this->name, $this->dims, $this->dims, $this->count, $diff, $buttonsCode);
    }
}