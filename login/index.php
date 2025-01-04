<?php

session_start();

include_once "../_server/data/patterns.php";
include_once "../_server/data/AccessHandler.php";

(new AccessHandler('/cwJS/account', false))->check();

spl_autoload_register(function ($class) {
    if (file_exists('../_server/components/' . $class . '.php'))
        include_once '../_server/components/' . $class . '.php';
});

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

if (isset($_GET['error']))
    (new ErrorMessage($_GET['error']))->render();
?>

<div class="FormsContainer">
    <form method="post" action="../_server/handlers/signup.php">
        <div>
            <img alt="Register" src="../_public/resources/images/signup.svg">
            <h1>Créer un compte</h1>
        </div>
        <div>
            <div>
                <?php (new Field('fn', 'text', 'Prénom', HtmlPatterns::$namePattern))->render() ?>
                <?php (new Field('ln', 'text', 'Nom', HtmlPatterns::$namePattern))->render() ?>
            </div>
            <div class="separator"></div>
            <?php (new Field('username', 'text', 'Nom d\'utilisateur', HtmlPatterns::$idPattern))->render() ?>
            <div class="separator"></div>
            <?php (new Field('passwd1', 'password', 'Mot de passe', HtmlPatterns::$pwdPattern))->render() ?>
            <?php (new Field('passwd2', 'password', 'Confirmer le mot de passe', HtmlPatterns::$pwdPattern))->render() ?>
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
            <?php (new Field('username', 'text', 'Nom d\'utilisateur', HtmlPatterns::$idPattern))->render() ?>
            <div class="separator"></div>
            <?php (new Field('passwd', 'password', 'Mot de passe', HtmlPatterns::$pwdPattern))->render() ?>
        </div>
        <input type="submit" value="Se connecter">
    </form>
</div>
</body>
</html>
