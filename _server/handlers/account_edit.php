<?php

session_start();
include_once "../../_credentials.php";
include_once "../data/patterns.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: /cwJS/account?error=invdt');
    die();
}

if (
    !isset($_SESSION["logged"])
    || !$_SESSION["logged"]
) {
    header('Location: /cwJS/login');
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
    header('Location: /cwJS/login?error=inter');
    die();
}

if (
    !isset($_POST["change"])
    || !isset($_POST["value"])
) {
    header('Location: /cwJS/login?error=invdt');
    die();
}

switch ($_POST["change"]) {
    case 'names':
        if (
            !preg_match(PhpPatterns::$namePattern, $_POST['value'])
            || !isset($_POST['value2'])
            || !preg_match(PhpPatterns::$namePattern, $_POST['value2'])
        ) {
            header('Location: /cwJS/account?error=invdt');
            die();
        }
        $req = $pdo->prepare('UPDATE USERS SET first_name = :fn, last_name = :ln WHERE id = :id');
        $req->bindParam(':fn', $_POST['value']);
        $req->bindParam(':ln', $_POST['value2']);
        $_SESSION['fn'] = $_POST['value'];
        $_SESSION['ln'] = $_POST['value2'];
        break;
    case 'id':
        if (!preg_match(PhpPatterns::$idPattern, $_POST["value"])) {
            header('Location: /cwJS/account?error=invdt');
            die();
        }
        $req = $pdo->prepare('UPDATE USERS SET username = :user WHERE id = :id');
        $req->bindValue(':user', $_POST["value"]);
        $_SESSION['username'] = $_POST["value"];
        break;
    case 'pwd':
        if (!preg_match(PhpPatterns::$pwdPattern, $_POST["value"])
            || !isset($_POST['value2'])
            || !preg_match(PhpPatterns::$pwdPattern, $_POST['value2'])
            || $_POST['value2'] !== $_POST['value']) {
            header('Location: /cwJS/account?error=invdt');
            die();
        }
        $req = $pdo->prepare('UPDATE USERS SET pwd_hash = :pwd WHERE id = :id');
        $req->bindValue(':pwd', password_hash($_POST["value"], PASSWORD_DEFAULT));
        break;
    default:
        header('Location: /cwJS/account?error=invdt');
        die();
}

$req->bindValue(':id', $_SESSION["id"]);

$res = $req->execute();

if (!$res) {
    header('Location: /cwJS/account?error=inter');
    die();
}

session_regenerate_id();

header('Location: /cwJS/account');
die();