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
        '/cwJS/account/?error=inter'
    );
}

$req = $pdo->prepare("DELETE FROM USERS WHERE id = :id");
$req->bindParam(":id", $_GET['id']);
manageRedirect(
    !$req->execute(),
    '/cwJS/account/?error=invdt'
);

header('Location: /cwJS/account/');
die();

