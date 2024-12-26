<?php

session_start();

spl_autoload_register(function ($class) {
    include_once '../_server/components/'.$class.'.php';
});

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

if($_SESSION['logged']){
    $buttons = [
        new Button('../account', '../_public/resources/icons/account.svg', $_SESSION['username'], 'account'),
        new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
    ];
} else {
    $buttons = [
        new Button('../login', '../_public/resources/icons/login.svg', 'Se connecter', 'Connexion'),
        new Button('', '../_public/resources/icons/theme.svg', 'Thème', 'Thème', 'theme')
    ];
}

(new TopBar($buttons))->render();

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