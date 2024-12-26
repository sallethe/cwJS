<?php

session_start();

include_once "../../_credentials.php";
include_once "../data/patterns.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: /cwJS/login?error=invdt');
    die();
}

if(
    !isset($_POST["fn"]) ||
    !isset($_POST["ln"]) ||
    !isset($_POST["username"]) ||
    !isset($_POST["passwd1"]) ||
    !isset($_POST["passwd2"])
) {
    session_abort();
    header('Location: /cwJS/login?error=invdt');
    die();
}

try {
    $pdo = new PDO(
        sprintf("mysql:host=%s;dbname=%s",
            Credentials::$url,
            Credentials::$database),
        Credentials::$username,
        Credentials::$password);
} catch (PDOException $e) {
    session_abort();
    header('Location: /cwJS/login?error=inter');
    die();
}

$firstname = $_POST["fn"];
$lastname = $_POST["ln"];
$username = $_POST["username"];
$passwd1 = $_POST["passwd1"];
$passwd2 = $_POST["passwd2"];


if(
    !preg_match(PhpPatterns::$idPattern, $username)
    || !preg_match(PhpPatterns::$namePattern, $firstname)
    || !preg_match(PhpPatterns::$namePattern, $lastname)
    || !preg_match(PhpPatterns::$pwdPattern, $passwd1)
    || !preg_match(PhpPatterns::$pwdPattern, $passwd2)
) {
    session_abort();
    header('Location: /cwJS/login?error=invdt');
    die();
}

if($passwd1 != $passwd2) {
    session_abort();
    header('Location: /cwJS/login?error=nspwd');
    die();
}

try {
    $req = $pdo->prepare("SELECT COUNT(*) FROM USERS WHERE username = :username");
    $req->bindParam(":username", $username);
    $req->execute();
} catch(PDOException $e) {
    session_abort();
    header('Location: /cwJS/login?error=inter');
    die();
}

if ($req->fetchColumn() > 0) {
    session_abort();
    header('Location: /cwJS/login?error=exalr');
    die();
}

$passwdHash = password_hash($passwd1, PASSWORD_DEFAULT);

try {
    $req = $pdo->prepare("INSERT INTO USERS (username, first_name, last_name, pwd_hash, role) VALUES (:username, :fn, :ln, :passwd, 0)");
    $req->bindParam(":username", $username);
    $req->bindParam(":fn", $firstname);
    $req->bindParam(":ln", $lastname);
    $req->bindParam(":passwd", $passwdHash);
    $req->execute();
} catch (PDOException $e) {
    session_abort();
    header('Location: /cwJS/login?error=inter');
    die();
}

try {
    $req = $pdo->prepare("SELECT id FROM USERS WHERE username = :username");
    $req->bindParam(":username", $username);
    $req->execute();
} catch(PDOException $e) {
    session_abort();
    header('Location: /cwJS/login?error=inter');
    die();
}

$id = $req->fetchColumn();

session_regenerate_id(true);

$_SESSION['username'] = $username;
$_SESSION['id'] = $id;
$_SESSION['logged'] = true;
$_SESSION['fn'] = $firstname;
$_SESSION['ln'] = $lastname;

header('Location: /cwJS/account');
die();

