<?php

include_once '../_credentials.php';
include_once '../_server/data/patterns.php';

session_start();

if (!isset($_SESSION['logged']) || !$_SESSION['logged']) {
    header('location: /cwJS/login/');
}

spl_autoload_register(function ($class) {
    if (file_exists('../_server/components/' . $class . '.php'))
        include_once '../_server/components/' . $class . '.php';
});

try {
    $pdo = new PDO(
        sprintf("mysql:host=%s;dbname=%s",
            Credentials::$url,
            Credentials::$database),
        Credentials::$username,
        Credentials::$password);
} catch (PDOException $e) {
    http_response_code(500);
    die();
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php

(new Header(sprintf('%s - cwJS', $_SESSION['username']), [
    new Script('../_public/code/theme.js', true),
    new Script('../_public/code/login.js', true),
]))->render();

?>
<body>
<?php

(new TopBar([
    new Button('../_server/handlers/logout.php', "../_public/resources/icons/logout.svg", "Se déconnecter", 'Logout'),
    new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
]))->render();

?>

<div class="AccountContainer">
    <div>
        <img src="/cwJS/_public/resources/images/account.svg" alt="Hello">
        <h1>Bonjour, <?php echo $_SESSION['fn'] ?></h1>
    </div>
    <h2>Vos grilles</h2>
    <div class="GridButtonContainer">
        <?php

        try {
            $req = $pdo->prepare("SELECT * FROM GRIDS WHERE creator = :creator");
            $req->bindParam(':creator', $_SESSION['id']);
            $req->execute();
        } catch (PDOException $e) {
            http_response_code(500);
            die();
        }

        if ($req->rowCount() == 0) {
            echo '<p>Noting...</p>';
        }

        while ($data = $req->fetch()) {
            (new GridButton($data['id'], $data['title'], $data['width'], $data['height'], $data['word_count'], true))->render();
        }
        ?>
    </div>
    <h2>Votre compte</h2>
    <div class="FormsContainer">
        <form method="post" action="../_server/handlers/account_edit.php">
            <div>
                <h3>Changer de nom et prénom</h3>
                <h2><span><?php echo $_SESSION['fn'] ?><?php echo $_SESSION['ln'] ?></span></h2>
            </div>
            <div>
                <?php

                (new Field('value', 'text', 'Nouveau prénom', HtmlPatterns::$namePattern))->render();
                (new Field('value2', 'text', 'Nouveau nom', HtmlPatterns::$namePattern))->render();

                ?>
            </div>
            <input type="hidden" name="change" value="names">
            <input type="submit" value="Mettre à jour">
        </form>
        <form method="post" action="../_server/handlers/account_edit.php">
            <div>
                <h3>Changer d'identifiant</h3>
                <h2><span><?php echo $_SESSION['username'] ?></span></h2>
            </div>
            <div>
                <?php (new Field('value', 'text', 'Nouveau nom d\'utilisateur', HtmlPatterns::$idPattern))->render() ?>
                <input type="hidden" name="change" value="id">
            </div>
            <input type="submit" value="Mettre à jour">
        </form>
        <form method="post" action="../_server/handlers/account_edit.php">
            <div>
                <h3>Changer de mot de passe</h3>
                <h2><span><?php for ($i = 0; $i < rand(8, 24); $i += 1) echo "&bull;" ?></span></h2>
            </div>
            <div>
                <?php
                (new Field('value', 'password', 'Nouveau mot de passe', HtmlPatterns::$pwdPattern))->render();
                (new Field('value2', 'password', 'Répéter le mot de passe', HtmlPatterns::$pwdPattern))->render();
                ?>
                <input type="hidden" name="change" value="pwd">
            </div>
            <input type="submit" value="Mettre à jour">
        </form>
    </div>
</div>
</body>
</html>
