<?php

session_start();
include_once '../_credentials.php';
include_once '../_server/data/DatabaseHandler.php';
include_once '../_server/data/patterns.php';
include_once "../_server/data/AccessHandler.php";
include_once "../_server/data/CookieHandler.php";

$handler = new AccessHandler('/cwJS/login', true);
$handler->check();

spl_autoload_register(function ($class) {
    if (file_exists('../_server/components/' . $class . '.php'))
        include_once '../_server/components/' . $class . '.php';
});

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
    showFailure(500);
}

$cookies = new CookieHandler();
$savedGrids = $cookies->getGrids();

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

$buttons = [
    new Button('../_server/handlers/logout.php', "../_public/resources/icons/logout.svg", "Se déconnecter", 'Logout'),
    new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
];

if (!$handler->isSuperUser())
    array_unshift($buttons, new Button('../create/', "../_public/resources/icons/edit.svg", "Créer", 'create'));

(new TopBar($buttons))->render();

if (isset($_GET['error']))
    (new ErrorMessage($_GET['error']))->render();
?>

<div class="AccountContainer">
    <div>
        <img src="../_public/resources/images/account.svg" alt="Hello">
        <h1>Bonjour, <?php echo $_SESSION['fn'] ?></h1>
    </div>
    <h2>Vos grilles en cours</h2>
    <div class="GridButtonContainer">
        <?php

        if (!$handler->isSuperUser() && count($savedGrids) > 0) {
            $placeholders = implode(',', array_fill(0, count($savedGrids), '?'));
            $req = $pdo->prepare('SELECT * FROM GRIDS WHERE id IN (' . $placeholders . ')');
            if (!$req->execute($savedGrids))
                (new Nothing())->render();
            else
                while ($data = $req->fetch())
                    (new GridButton(
                        $data['id'],
                        $data['title'],
                        intval($data['dim']),
                        intval($data['difficulty']),
                        $data['word_count'],
                        false)
                    )->render();
        } else (new Nothing())->render();

        ?>
    </div>
    <h2>Vos grilles</h2>
    <div class="GridButtonContainer">
        <?php

        if (!$handler->isSuperUser()) {
            $req = $pdo->prepare("SELECT * FROM GRIDS WHERE creator = :creator");
            $req->bindParam(':creator', $_SESSION['id']);
            if (!$req->execute() || $req->rowCount() == 0) (new Nothing())->render();
            else
                while ($data = $req->fetch())
                    (new GridButton(
                        $data['id'],
                        $data['title'],
                        intval($data['dim']),
                        intval($data['difficulty']),
                        $data['word_count'],
                        true)
                    )->render();
        } else
            (new Nothing())->render();
        ?>
    </div>
    <?php

    if ($handler->isSuperUser()) {
        echo '<h2>[ADMIN] Utilisateurs</h2>';
        echo '<div class="GridButtonContainer">';
        $req = $pdo->prepare("SELECT * FROM USERS WHERE role = 0");
        if (!$req->execute() || $req->rowCount() == 0) (new Nothing())->render();
        while ($data = $req->fetch())
            (new UserButton(
                $data['id'],
                $data['username'],
                $data['first_name'],
                $data['last_name'])
            )->render();
        echo '</div>';
    }
    ?>
    <h2>Votre compte</h2>
    <div class="FormsContainer">
        <form method="post" action="../_server/handlers/account_edit.php">
            <div>
                <h3>Changer de nom et prénom</h3>
                <h2><span><?php echo $_SESSION['fn'] ?>
                        <?php echo $_SESSION['ln'] ?></span></h2>
            </div>
            <div>
                <?php

                (new Field('fn', 'text', 'Nouveau prénom', HtmlPatterns::$namePattern))->render();
                (new Field('ln', 'text', 'Nouveau nom', HtmlPatterns::$namePattern))->render();

                ?>
            </div>
            <input type="submit" value="Mettre à jour">
        </form>
        <form method="post" action="../_server/handlers/account_edit.php">
            <div>
                <h3>Changer d'identifiant</h3>
                <h2><span><?php echo $_SESSION['username'] ?></span></h2>
            </div>
            <div>
                <?php (new Field('id', 'text', 'Nouveau nom d\'utilisateur', HtmlPatterns::$idPattern))->render() ?>
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
                (new Field('pwd', 'password', 'Nouveau mot de passe', HtmlPatterns::$pwdPattern))->render();
                (new Field('pwd2', 'password', 'Répéter le mot de passe', HtmlPatterns::$pwdPattern))->render();
                ?>
            </div>
            <input type="submit" value="Mettre à jour">
        </form>
    </div>
</div>
</body>
</html>
