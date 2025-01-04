<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/AccessHandler.php";

$handler = new AccessHandler('/cwJS/', true);
$handler->check();
manageRedirect(
    $handler->isSuperUser(),
    '/cwJS/'
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
    !isset($_POST['id']) ||
    !isset($_POST['grid']) ||
    !isset($_POST['words']) ||
    !isset($_POST['title']) ||
    !isset($_POST['dim']) ||
    !isset($_POST['diff']) ||
    !isset($_POST['count']),
    "/cwJS/"
);

if ($_POST['id'] === '') {
    $req = $pdo->prepare("INSERT INTO GRIDS(title, creator, dim, difficulty, grid, words, word_count, created) VALUES(:title, :creator, :dim, :diff, :grid, :words, :count, current_date())");
} else {
    $req = $pdo->prepare("UPDATE GRIDS SET title = :title, dim = :dim, difficulty = :diff, grid = :grid, words = :words, word_count = :count WHERE id = :id AND creator = :creator");
    $req->bindParam(':id', $_POST['id']);
}

$req->bindParam(':creator', $_SESSION['id']);
$req->bindParam(':title', $_POST['title']);
$dim = intval($_POST['dim']);
$count = intval($_POST['count']);
$req->bindParam(':dim', $dim, PDO::PARAM_INT);
$req->bindParam(':diff', $_POST['diff'], PDO::PARAM_INT);
$req->bindParam(':grid', $_POST['grid']);
$req->bindParam(':words', $_POST['words']);
$req->bindParam(':count', $count, PDO::PARAM_INT);
manageRedirect(
    !$req->execute(),
    "/cwJS/account?error=inter"
);

if ($_POST['id'] != '')
    header('Location: /cwJS/play/?id=' . $_POST['id']);
else
    header('Location: /cwJS/play/?id=' . $pdo->lastInsertId());
