<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/patterns.php";
include_once "../data/AccessHandler.php";

(new AccessHandler('/cwJS/login', true))->check();

manageRedirect(
    $_SERVER["REQUEST_METHOD"] != "POST",
    "/cwJS/account?error=invdt");

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
    manageRedirect(true, "/cwJS/login?error=inter");
}

if (isset($_POST['fn']) && isset($_POST['ln'])) {
    manageRedirect(
        !preg_match(PhpPatterns::$namePattern, $_POST['fn'])
        || !preg_match(PhpPatterns::$namePattern, $_POST['ln']),
        "/cwJS/account?error=invdt");
    $req = $pdo->prepare('UPDATE USERS SET first_name = :fn, last_name = :ln WHERE id = :id');
    $req->bindParam(':fn', $_POST['fn']);
    $req->bindParam(':ln', $_POST['ln']);
    $_SESSION['fn'] = $_POST['fn'];
    $_SESSION['ln'] = $_POST['ln'];
} elseif (isset($_POST['id'])) {
    manageRedirect(
        !preg_match(PhpPatterns::$idPattern, $_POST["id"]),
        "/cwJS/account?error=invdt"
    );
    $req = $pdo->prepare('UPDATE USERS SET username = :user WHERE id = :id');
    $req->bindParam(':user', $_POST["id"]);
    $_SESSION['username'] = $_POST["id"];
} elseif (isset($_POST['pwd']) && isset($_POST['pwd2'])) {
    manageRedirect(
        !preg_match(PhpPatterns::$pwdPattern, $_POST["pwd"])
        || !preg_match(PhpPatterns::$pwdPattern, $_POST['pwd2'])
        || $_POST['pwd'] !== $_POST['pwd2'],
        "/cwJS/account?error=invdt"
    );
    $req = $pdo->prepare('UPDATE USERS SET pwd_hash = :pwd WHERE id = :id');
    $password_hash = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
    $req->bindParam(':pwd', $password_hash);
} else {
    manageRedirect(
        true,
        "/cwJS/account?error=invdt"
    );
}

$req->bindParam(':id', $_SESSION["id"]);

manageRedirect(
    !$req->execute(),
    "/cwJS/account?error=inter");

session_regenerate_id();

header('Location: /cwJS/account');
die();