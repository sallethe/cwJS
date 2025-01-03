<?php

class AccessHandler
{
    private string $redirect;

    private bool $mustBeConnected;

    public function __construct(string $redirect, bool $mustBeConnected)
    {
        $this->redirect = $redirect;
        $this->mustBeConnected = $mustBeConnected;
    }

    private function isConnected(): bool
    {
        return isset($_SESSION['username'])
            && isset($_SESSION['logged'])
            && isset($_SESSION['id'])
            && isset($_SESSION['fn'])
            && isset($_SESSION['ln']);
    }

    private function xor(bool $a, bool $b): bool
    {
        return $a !== $b;
    }

    public function check(): void
    {
        if (
            $this->xor(
                $this->isConnected(),
                $this->mustBeConnected)
        ) {
            header('Location: ' . $this->redirect);
            die();
        }
    }
}
