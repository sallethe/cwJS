<?php

include_once '../_server/interfaces/Component.php';
include_once '../_server/components/Button.php';
include_once '../_server/components/TopBar.php';
include_once '../_server/components/Header.php';
include_once '../_server/components/Script.php';

if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php

(new Header('Jeu - cwJS', [
    new Script('../_public/code/grid.js'),
    new Script('../_public/code/theme.js', true),
]))->render();

?>
<body>
<?php
(new TopBar([
    new Button('../login', '../_public/resources/icons/login.svg', 'Se connecter', 'Connexion'),
    new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')])
)->render();
?>
<div class="GridContainer">
    <div class="Grid" id="grid"></div>
    <div class="WordSet" id="words">
        <h2>Horizontal</h2>
        <div id></div>
        <h2>Vertical</h2>
        <div id></div>
    </div>
</div>
<script>
    const gr = new Grid(10, [
        c(0, 0),
        c(5, 7),
    ])
    gr.generate()
    const wd = new WordSet([
        new Word(false, c(0, 0), 5, 'Le chat'),
        new Word(true, c(0, 0), 6, 'Mme Selmi le dit avec un accent parfait.'),
    ], gr)
    wd.generate()
</script>
</body>
</html>