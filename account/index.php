<?php

session_start();

if (!isset($_SESSION['logged']) || !$_SESSION['logged']) {
    header('location: /cwJS/login/');
}

spl_autoload_register(function ($class) {
    if (file_exists('../_server/components/' . $class . '.php'))
        include_once '../_server/components/' . $class . '.php';
});

?>

<!DOCTYPE html>
<html lang="fr">
<?php

(new Header(sprintf('%s - cwJS', $_SESSION['username']), [
    new Script('../_public/code/theme.js', true),
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
    <h1>Bonjour, <?php echo $_SESSION['fn'] ?></h1>
    <h2>Grilles en cours</h2>
    <h2>Vos grilles</h2>
</div>
</body>
</html>
