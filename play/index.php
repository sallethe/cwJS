<?php

session_start();
include_once '../_credentials.php';
include_once '../_server/data/DatabaseHandler.php';
include_once '../_server/data/AccessHandler.php';
include_once '../_server/data/CookieHandler.php';

spl_autoload_register(function ($class) {
    include_once '../_server/components/' . $class . '.php';
});

manageRedirect(
    !isset($_GET['id']) || $_GET['id'] == '',
    "../index.php"
);

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
    showFailure(500);
}

$req = $pdo->prepare("SELECT * FROM GRIDS WHERE id = :id");
$req->bindParam(':id', $_GET['id']);
manageRedirect(
    !$req->execute(),
    "../index.php"
);
$data = $req->fetch();

(new CookieHandler())->addGrid($_GET['id']);

?>

<!DOCTYPE html>
<html lang="fr">
<?php

(new Header($data['title'] . ' - cwJS', [
    new Script('../_public/code/grid.js'),
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

if ((new AccessHandler())->isConnected())
    $buttons[1] = new Button('../account', '../_public/resources/icons/account.svg', $_SESSION['username'], 'account');
else
    $buttons[1] = new Button('../login', '../_public/resources/icons/login.svg', 'Se connecter', 'Connexion');

(new TopBar($buttons))->render();

?>
<div class="GridContainer">
    <div>
        <div class="Grid" id="grid"></div>
    </div>
    <div class="WordSet" id="words">
        <h2>Horizontal</h2>
        <div id="horizontal"></div>
        <h2>Vertical</h2>
        <div id="vertical"></div>
        <button id="check">Vérifier</button>
    </div>
</div>
<script>
    const answers = <?php echo $data['grid'] ?>;
    const words = <?php echo $data['words'] ?>;
    const id = <?php echo $_GET['id'] ?>

    const gr = new Grid(answers)
    const wd = new WordSet(words)
    const s = new Solver(gr, answers, id)

    gr.generate()
    wd.generate()
</script>
</body>
</html>