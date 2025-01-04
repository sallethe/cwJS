<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/AccessHandler.php";

$handler = new AccessHandler('/cwJS/', true);
$handler->check();
manageRedirect(
    $handler->isSuperUser(),
    "/cwJS/"
);

try {
    $pdo = new DatabaseHandler();
} catch (PDOException $e) {
    manageRedirect(
        true,
        "/cwJS/account?error=inter"
    );
}

manageRedirect(
    !isset($_GET['id']) ||
    $_GET['id'] == '',
    "/cwJS/account/?error=invdt"
);

$req = $pdo->prepare("DELETE FROM GRIDS WHERE id = :id");
$req->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
manageRedirect(
    !$req->execute(),
    '/cwJS/account?error=inter'
);

header("Location: /cwJS/account");
die();