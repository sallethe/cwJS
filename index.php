<?php

session_start();
include_once './_credentials.php';
include_once './_server/data/DatabaseHandler.php';
include_once './_server/data/patterns.php';
include_once './_server/data/AccessHandler.php';

spl_autoload_register(function ($class) {
    if (file_exists('./_server/components/' . $class . '.php'))
        include_once './_server/components/' . $class . '.php';
});

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
    showFailure(500);
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php

(new Header('cwJS', [
    new Script('./_public/code/theme.js', true),
]))->render();

?>
<body>
<?php

$buttons = [
    null,
    new Button('', './_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
];

if ((new AccessHandler())->isConnected())
    $buttons[0] = new Button('/cwJS/account', './_public/resources/icons/account.svg', $_SESSION['username'], 'account');
else
    $buttons[0] = new Button('/cwJS/login', './_public/resources/icons/login.svg', 'Se connecter', 'Connexion');

(new TopBar($buttons))->render();


?>

<div class="IndexContainer">
    <h1>Bienvenue sur <span>cwJS</span></h1>
    <form action="./" method="get" class="SearchField">
        <label for="searchbar" hidden>Rechercher</label>
        <input type="text" name="query" placeholder="Rechercher..." id="searchbar">
        <div>
            <label for="sort-by">Trier par</label>
            <select name="sort-by" id="sort-by">
                <option value="diff">difficulté</option>
                <option value="creat">date de création</option>
                <option value="dim">dimension</option>
            </select>
        </div>
        <div>
            <input type="checkbox" name="reverse" value="true" id="reverse">
            <label for="reverse">Inverser</label>
        </div>
        <input type="submit" value="Rechercher">
        <a href="./">
            <img src="_public/resources/icons/error.svg" alt="Reset">
        </a>
    </form>
    <div class="GridButtonContainer">
        <?php

        $select = "SELECT * FROM GRIDS WHERE title LIKE ?";
        $order = "";
        $like = "%%";

        if (
            isset($_GET['query'])
            && isset($_GET['sort-by'])
        ) {
            $like = "%" . $_GET['query'] . "%";
            $order = match ($_GET['sort-by']) {
                "dim" => "ORDER BY dim",
                "creat" => "ORDER BY created",
                default => "ORDER BY difficulty",
            };
            if (isset($_GET['reverse']) && $_GET['reverse'] == 'true') $order .= " DESC";
        }

        $query = $select . $order;
        $req = $pdo->prepare($query);
        if (!$req->execute(array($like)))
            (new Nothing())->render();
        if ($req->rowCount() == 0)
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
        ?>
    </div>
</div>

</body>
</html>