<?php

session_start();
require_once "../../_credentials.php";
require_once "../data/AccessHandler.php";
require_once "../data/DatabaseHandler.php";

$handler = new AccessHandler('/cwJS/login/?error=accdn', true);
$handler->check();
manageRedirect(
    !$handler->isSuperUser()
    || !isset($_GET['id'])
    || $_GET['id'] == '',
    "/cwJS/account/?error=accdn"
);

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
    manageRedirect(
        true,
        "/cwJS/account/?error=inter"
    );
}

$req = $pdo->prepare("SELECT * FROM USERS WHERE id = :id");
$req->bindParam(":id", $_GET['id']);
manageRedirect(
    !$req->execute(),
    "/cwJS/account/?error=invdt",
);

$user = $req->fetch();

manageRedirect(
    $user === false,
    "/cwJS/account/?error=loger"
);

session_regenerate_id(true);

$_SESSION['username'] = $user['username'];
$_SESSION['id'] = $user['id'];
$_SESSION['su'] = 0;
$_SESSION['fn'] = $user['first_name'];
$_SESSION['ln'] = $user['last_name'];

header('Location: /cwJS/account/');
die();

