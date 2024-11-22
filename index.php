<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>cwJS</title>
    <script defer src="code/main.mjs" type="module"></script>
    <link rel="icon" href="resources/icons/favicon.svg">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
<div class="TopBar">
    <a href="./">
        <img src="resources/images/logo.svg" alt="Logo">
    </a>
    <div>
        <a title="Règles">
            <img src="resources/icons/rules.svg" alt="Règles">
        </a>
        <a title="Se connecter">
            <img src="resources/icons/login.svg" alt="Se connecter">
        </a>
        <a id="theme" title="Changer de thème">
            <img src="resources/icons/theme.svg" alt="Thème">
        </a>
    </div>
</div>
<div class="GridContainer">
    <div class="Grid" id="grid"></div>
    <div class="Words" id="words"></div>
</div>
</body>
</html>