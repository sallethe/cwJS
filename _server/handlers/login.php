<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/AccessHandler.php";

(new AccessHandler('/cwJS/account', false))->check();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: /cwJS/login?error=invdt');
    die();
}

if (!isset($_POST['username'])
    || !isset($_POST['passwd'])
) {
    session_abort();
    header('Location: /cwJS/login?error=invdt');
    die();
}

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
    session_abort();
    header('Location: /cwJS/login?error=inter');
    die();
}

$username = $_POST["username"];
$password = $_POST["passwd"];

if (
    !preg_match('/^[a-zA-Z0-9]{5,20}$/', $username) ||
    !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)
) {
    session_abort();
    header('Location: /cwJS/login?error=invdt');
    die();
}

$req = $pdo->prepare("SELECT * FROM USERS WHERE username = :username");
$req->bindParam(":username", $username);
if (!$req->execute()) {
    session_abort();
    header('Location: /cwJS/login?error=inter');
    die();
}

$user = $req->fetch();

if ($user === false) {
    session_abort();
    header('Location: /cwJS/login?error=loger');
    die();
}

if (!password_verify($password, $user['pwd_hash'])) {
    session_abort();
    header('Location: /cwJS/login?error=pwder');
    die();
}

session_regenerate_id(true);

$_SESSION['username'] = $user['username'];
$_SESSION['id'] = $user['id'];
$_SESSION['logged'] = true;
$_SESSION['fn'] = $user['first_name'];
$_SESSION['ln'] = $user['last_name'];

header('Location: /cwJS/account');
die();