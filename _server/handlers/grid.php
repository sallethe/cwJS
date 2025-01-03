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
    !isset($_POST['id']) ||
    !isset($_POST['grid']) ||
    !isset($_POST['words']) ||
    !isset($_POST['title']) ||
    !isset($_POST['dim']) ||
    !isset($_POST['count'])
) {
    header("Location: /cwJS/");
    die();
}

print_r($_POST);

if($_POST['id'] === '') {
    $req = $pdo->prepare("INSERT INTO GRIDS(title, creator, width, height, grid, words, word_count) VALUES(:title, :creator, :width, :height, :grid, :words, :count)");
    $req->bindParam(':creator', $_SESSION['id']);
} else {
    $req = $pdo->prepare("UPDATE GRIDS SET title = :title, width = :width, height = :height, grid = :grid, words = :words, word_count = :count WHERE id = :id");
    $req->bindParam(':id', $_POST['id']);
}

$req->bindParam(':title', $_POST['title']);
$dim = intval($_POST['dim']);
$count = intval($_POST['count']);
$req->bindParam(':width', $dim, PDO::PARAM_INT);
$req->bindParam(':height', $dim, PDO::PARAM_INT);
$req->bindParam(':grid', $_POST['grid']);
$req->bindParam(':words', $_POST['words']);
$req->bindParam(':count', $count, PDO::PARAM_INT);

if(!$req->execute()){
    header("Location: /cwJS/account?error=inter");
    die();
}

if($_POST['id'] != '') {
    header('Location: /cwJS/play/?id='.$_POST['id']);
} else {
    header('Location: /cwJS/play/?id='.$pdo->lastInsertId());
}
