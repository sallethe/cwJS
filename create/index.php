<?php

session_start();
include_once "../_server/data/AccessHandler.php";
include_once "../_credentials.php";
include_once "../_server/data/DatabaseHandler.php";

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

$title = "";
$words = "[]";
$grid = '[[""]]';
$id = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $req = $pdo->prepare('SELECT * FROM GRIDS WHERE id = :id AND creator = :creator');
    $req->bindParam(':id', $_GET['id']);
    $req->bindParam(':creator', $_SESSION['id']);
    manageRedirect(
        !$req->execute(),
        "/cwJS/account/?error=inter"
    );
    $data = $req->fetch();
    manageRedirect(
        $data === false,
        "/cwJS/account/?error=accdn"
    );
    $words = $data['words'];
    $grid = $data['grid'];
    $title = $data['title'];
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php

(new Header('Créer une grille - cwJS', [
    new Script('../_public/code/theme.js', true),
    new Script('../_public/code/grid.js'),
    new Script('../_public/code/create.js')
]))->render();

?>
<body>
<?php

(new TopBar([
    new Button('../account', '../_public/resources/icons/account.svg', $_SESSION['username'], 'account'),
    new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
]))->render();

?>

<div class="GridContainer">
    <div>
        <div id="grid" class="Grid"></div>
    </div>
    <div id="wordslist" class="EditableWordsList">
        <h2>Base</h2>
        <div id="base">
            <div class="ButtonsSet">
                <label hidden for="titleInput">Titre</label>
                <input type="text" placeholder="Titre" id="titleInput" value="<?php echo $title ?>">
                <label hidden for="diffInput">Difficulté</label>
                <select id="diffInput">
                    <option value="3">Difficile</option>
                    <option value="2">Moyen</option>
                    <option value="1" selected>Facile</option>
                </select>
            </div>
            <div class="ButtonsSet">
                <button id="minus">-</button>
                <p id="dimensions"></p>
                <button id="plus">+</button>
            </div>
        </div>
        <h2>Horizontal</h2>
        <div id="horizontal">
            <div class="ButtonsSet">
                <button>Ajouter</button>
                <button>Retirer</button>
            </div>

        </div>
        <h2>Vertical</h2>
        <div id="vertical">
            <div class="ButtonsSet">
                <button>Ajouter</button>
                <button>Retirer</button>
            </div>
        </div>
        <h2>Vérifier</h2>
        <div class="ButtonsSet">
            <button id="checker">
                Vérifier
            </button>
            <p id="error"></p>
            <form action="../_server/handlers/grid.php" method="post" id="submitter">
                <input type="hidden" name="id" value="<?php echo $id ?>">
                <input type="hidden" name="title" value="" id="title">
                <input type="hidden" name="diff" value="" id="diff">
                <input type="hidden" name="dim" value="" id="dim">
                <input type="hidden" name="count" value="" id="count">
                <input type="hidden" name="grid" value="" id="gride">
                <input type="hidden" name="words" value="" id="wordse">
            </form>
        </div>
    </div>
</div>

<script>
    const g = new EditableGrid(JSON.parse(`<?php echo $grid; ?>`))
    g.generate()
    const w = new EditableWordSet(JSON.parse(`<?php echo $words; ?>`))
    const v = new Verificator(w, g)
</script>

</body>
