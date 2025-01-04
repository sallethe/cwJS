<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/patterns.php";
include_once "../data/AccessHandler.php";

(new AccessHandler('/cwJS/account', false))->check();

manageRedirect(
    $_SERVER["REQUEST_METHOD"] != "POST",
    "/cwJS/login?error=invdt"
);

manageRedirect(
    !isset($_POST["fn"]) ||
    !isset($_POST["ln"]) ||
    !isset($_POST["username"]) ||
    !isset($_POST["passwd1"]) ||
    !isset($_POST["passwd2"]),
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

$firstname = $_POST["fn"];
$lastname = $_POST["ln"];
$username = $_POST["username"];
$passwd1 = $_POST["passwd1"];
$passwd2 = $_POST["passwd2"];

manageRedirect(
    !preg_match(PhpPatterns::$idPattern, $username)
    || !preg_match(PhpPatterns::$namePattern, $firstname)
    || !preg_match(PhpPatterns::$namePattern, $lastname)
    || !preg_match(PhpPatterns::$pwdPattern, $passwd1)
    || !preg_match(PhpPatterns::$pwdPattern, $passwd2),
    "/cwJS/login?error=invdt"
);

manageRedirect(
    $passwd1 != $passwd2,
    "/cwJS/login?error=nspwd"
);

$req = $pdo->prepare("SELECT COUNT(*) FROM USERS WHERE username = :username");
$req->bindParam(":username", $username);
manageRedirect(
    !$req->execute(),
    "/cwJS/login?error=inter"
);

manageRedirect(
    $req->fetchColumn() > 0,
    "/cwJS/login?error=exalr"
);

$passwdHash = password_hash($passwd1, PASSWORD_DEFAULT);

$req = $pdo->prepare("INSERT INTO USERS (username, first_name, last_name, pwd_hash, role) VALUES (:username, :fn, :ln, :passwd, 0)");
$req->bindParam(":username", $username);
$req->bindParam(":fn", $firstname);
$req->bindParam(":ln", $lastname);
$req->bindParam(":passwd", $passwdHash);
manageRedirect(
    !$req->execute(),
    "/cwJS/login?error=inter"
);

$req = $pdo->prepare("SELECT id FROM USERS WHERE username = :username");
$req->bindParam(":username", $username);
manageRedirect(
    !$req->execute(),
    "/cwJS/login?error=inter"
);

$id = $req->fetchColumn();

session_regenerate_id(true);

$_SESSION['username'] = $username;
$_SESSION['id'] = $id;
$_SESSION['su'] = false;
$_SESSION['fn'] = $firstname;
$_SESSION['ln'] = $lastname;

header('Location: /cwJS/account');
die();

