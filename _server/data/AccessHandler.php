<?php

class AccessHandler
{
    private string $redirect;

    private bool $mustBeConnected;

    public function __construct(string $redirect = '', bool $mustBeConnected = true)
    {
        $this->redirect = $redirect;
        $this->mustBeConnected = $mustBeConnected;
    }

    public function isSuperUser()
    {
        return isset($_SESSION['su']) && $_SESSION['su'] === true;
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

    private function xor(bool $a, bool $b): bool
    {
        return $a !== $b;
    }

    public function isConnected(): bool
    {
        return isset($_SESSION['username'])
            && isset($_SESSION['su'])
            && isset($_SESSION['id'])
            && isset($_SESSION['fn'])
            && isset($_SESSION['ln']);
    }
}

function manageRedirect(bool $condition, string $redirect): void
{
    if ($condition) {
        header('Location: ' . $redirect);
        die();
    }
}

function showFailure(int $statusCode): void
{
    http_response_code($statusCode);
    die();
}