<?php

include_once '../_server/interfaces/Component.php';
include_once '../_server/components/Button.php';
include_once '../_server/components/TopBar.php';
include_once '../_server/components/Header.php';
include_once '../_server/components/Script.php';
include_once '../_server/components/Field.php';
include_once '../_server/components/ErrorMessage.php';


if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php
(new Header('Connexion - cwJS', [
    new Script('../_public/code/theme.js', true),
    new Script('../_public/code/login.js')
]))->render()

?>
<body>
<?php

(new TopBar([
    new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
]))->render();

?>

<?php

if (isset($_GET['error'])) {
    (new ErrorMessage($_GET['error']))->render();
}
?>

<div class="FormsContainer">
    <form>
        <div>
            <img alt="Register" src="../_public/resources/images/signup.svg">
            <h1>Créer un compte</h1>
        </div>
        <div>
            <div>
                <!-- TODO : pattern -->
                <?php (new Field('fn', 'text', 'Prénom'))->render() ?>
                <?php (new Field('ln', 'text', 'Nom'))->render() ?>
            </div>
            <div class="separator"></div>
            <!-- TODO : pattern -->
            <?php (new Field('username', 'text', 'Nom d\'utilisateur'))->render() ?>
            <div class="separator"></div>
            <!-- TODO : pattern -->
            <?php (new Field('passwd', 'password', 'Mot de passe'))->render() ?>
            <?php (new Field('passwd2', 'password', 'Confirmer le mot de passe'))->render() ?>
        </div>
        <input type="submit" value="Créer un compte">
    </form>
    <div class="separator"></div>
    <form action="../_server/handlers/login.php" method="post">
        <div>
            <img alt="Log in" src="../_public/resources/images/login.svg">
            <h1>Se connecter</h1>
        </div>
        <div>
            <!-- TODO : pattern -->
            <?php (new Field('username', 'text', 'Nom d\'utilisateur'))->render() ?>
            <div class="separator"></div>
            <!-- TODO : pattern -->
            <?php (new Field('passwd', 'password', 'Mot de passe'))->render() ?>
        </div>
        <input type="submit" value="Se connecter">
    </form>
</div>
</body>
</html>
