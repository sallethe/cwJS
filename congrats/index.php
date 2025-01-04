<?php

session_start();
include_once "../_server/data/AccessHandler.php";
include_once "../_server/data/CookieHandler.php";

spl_autoload_register(function ($class) {
    include_once '../_server/components/' . $class . '.php';
});

$handler = new AccessHandler();

manageRedirect(
    !isset($_GET['id']) || $_GET['id'] == "",
    "/cwJS/"
);

$cookie = new CookieHandler();
$cookie->removeGrid($_GET['id']);

?>

<!DOCTYPE html>
<html lang="fr">
<?php

(new Header('Félicitations ! - cwJS', [
    new Script('../_public/code/theme.js', true),
]))->render();

?>
<body>
<?php

$buttons = [
    new Button('../', '../_public/resources/icons/error.svg', 'Abandonner', 'giveup'),
    null,
    new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
];

if ($handler->isConnected())
    $buttons[1] = new Button('../account', '../_public/resources/icons/account.svg', $_SESSION['username'], 'account');
else
    $buttons[1] = new Button('../login', '../_public/resources/icons/login.svg', 'Se connecter', 'Connexion');

(new TopBar($buttons))->render();

?>
<div class="IndexContainer">
    <h1>Félicitations !</h1>
    <p>Vous voulez rejouer ?</p>
    <div class="ButtonsSet">
        <a href="../play/?id=<?php echo $_GET['id']; ?>">
            <img src="../_public/resources/icons/play.svg" alt="Replay" />
            <p>Rejouer</p>
        </a>
        <a href="../">
            <img src="../_public/resources/icons/error.svg" alt="Back" />
            <p>Retour</p>
        </a>
        <?php

        if ($handler->isConnected())
            echo '
                <a href="../account">
                    <img src="../_public/resources/icons/account.svg" alt="Back"/>
                    <p>Compte</p>
                </a>
            ';

        ?>
    </div>

</div>
</body>