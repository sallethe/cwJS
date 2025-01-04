<?php


class ErrorMessage extends Component
{

    public static array $errorMessages = array(
        'nspwd' => 'Les mots de passe ne correspondent pas.', // Not the Same PassWorD
        'loger' => 'Ce compte n\'existe pas.', // LOGin ERror
        'invdt' => 'Les données envoyées sont invalides.', // INValid DaTa
        'pwder' => 'Le mot de passe est incorrect.', // PassWorD ERror
        'exalr' => 'Ce compte existe déjà.', // EXists ALReady
        'accdn' => 'Accès interdit.', // ACCes DeNied
        'inter' => 'Veuillez réessayer.' // INTernal ERror
    );
    private string $code;

    public function __construct(string $message)
    {
        $this->code = $message;
    }

    public function getCode(): string
    {
        $content = array_key_exists($this->code, ErrorMessage::$errorMessages) ?
            ErrorMessage::$errorMessages[$this->code] :
            ErrorMessage::$errorMessages['inter'];
        return sprintf('
        <div class="ErrorMessage">
            <img src="/cwJS/_public/resources/icons/error.svg" alt="Error">
            <div>
                <h3>Une erreur est survenue</h3>
                <p>%s</p>
            </div>
        </div>
    ', $content);
    }
}