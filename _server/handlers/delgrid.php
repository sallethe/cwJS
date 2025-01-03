<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/AccessHandler.php";

(new AccessHandler('/cwJS/', true))->check();

try {
    $pdo = new DatabaseHandler();
} catch (PDOException $e) {
    header("Location: /cwJS/account?error=inter");
    die();
}

if(
    !isset($_GET['id']) ||
    $_GET['id'] == ''
) {
    header("Location: /cwJS/account/?error=invdt");
    die();
}

$req = $pdo->prepare("DELETE FROM GRIDS WHERE id = :id");
$req->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
if(!$req->execute()) {
    header("Location: /cwJS/account?error=inter");
    die();
}

header("Location: /cwJS/account");
die();