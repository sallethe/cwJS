<?php

const DEFAULT_COOKIE_NAME = "grids";
const DEFAULT_SEPARATOR = "-";

define("DEFAULT_TTL", time() + 3153600);

class CookieHandler
{
    public function addGrid(string $grid): void
    {
        if ($this->hasGrid($grid)) return;
        $grids = $this->getGrids();
        $grids[] = $grid;
        $this->saveGrid($grids);
    }

    private function hasGrid(string $grid): bool
    {
        $grids = $this->getGrids();
        return in_array($grid, $grids);
    }

    public function getGrids(): array
    {
        if (!isset($_COOKIE[DEFAULT_COOKIE_NAME])) return [];
        return explode(DEFAULT_SEPARATOR, $_COOKIE[DEFAULT_COOKIE_NAME]);
    }

    private function saveGrid(array $data): void
    {
        setcookie(
            DEFAULT_COOKIE_NAME,
            join(DEFAULT_SEPARATOR, $data),
            DEFAULT_TTL,
            '/'
        );
    }

    public function removeGrid(string $grid): void
    {
        if (!$this->hasGrid($grid)) return;
        $grids = $this->getGrids();
        for ($i = 0; $i < count($grids); $i++)
            if ($grids[$i] === $grid)
                unset($grids[$i]);
        $this->saveGrid($grids);
    }
}
