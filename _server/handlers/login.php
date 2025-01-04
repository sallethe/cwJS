<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/AccessHandler.php";
include_once "../data/patterns.php";

(new AccessHandler('/cwJS/account', false))->check();

manageRedirect(
    $_SERVER["REQUEST_METHOD"] != "POST",
    "/cwJS/login?error=invdt"
);

manageRedirect(
    !isset($_POST['username'])
    || !isset($_POST['passwd']),
    "/cwJS/login?error=invdt"
);

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
    manageRedirect(
        true,
        "/cwJS/login?error=inter"
    );
}

$username = $_POST["username"];
$password = $_POST["passwd"];

manageRedirect(
    !preg_match(PhpPatterns::$idPattern, $username) ||
    !preg_match(PhpPatterns::$pwdPattern, $password),
    "/cwJS/login?error=invdt"
);

$req = $pdo->prepare("SELECT * FROM USERS WHERE username = :username");
$req->bindParam(":username", $username);
manageRedirect(
    !$req->execute(),
    "/cwJS/login/?error=inter"
);
$user = $req->fetch();

manageRedirect(
    $user === false,
    "/cwJS/login/?error=loger"
);

manageRedirect(
    !password_verify($password, $user['pwd_hash']),
    "/cwJS/login/?error=pwder"
);

session_regenerate_id(true);

$_SESSION['username'] = $user['username'];
$_SESSION['id'] = $user['id'];
$_SESSION['su'] = $user['role'] === 1;
$_SESSION['fn'] = $user['first_name'];
$_SESSION['ln'] = $user['last_name'];


header('Location: /cwJS/account');
die();