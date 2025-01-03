<?php

session_start();
include_once '../../_credentials.php';
include_once "../data/DatabaseHandler.php";
include_once "../data/patterns.php";
include_once "../data/AccessHandler.php";

(new AccessHandler('/cwJS/login', true))->check();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: /cwJS/account?error=invdt');
    die();
}

try {
    $pdo = new DatabaseHandler();
} catch (Exception $e) {
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

if (isset($_POST['fn']) && isset($_POST['ln'])) {
    if (
        !preg_match(PhpPatterns::$namePattern, $_POST['fn'])
        || !preg_match(PhpPatterns::$namePattern, $_POST['ln'])
    ) {
        header('Location: /cwJS/account?error=invdt');
        die();
    }
    $req = $pdo->prepare('UPDATE USERS SET first_name = :fn, last_name = :ln WHERE id = :id');
    $req->bindParam(':fn', $_POST['fn']);
    $req->bindParam(':ln', $_POST['ln']);
    $_SESSION['fn'] = $_POST['fn'];
    $_SESSION['ln'] = $_POST['ln'];
} elseif (isset($_POST['id'])) {
    if (!preg_match(PhpPatterns::$idPattern, $_POST["id"])) {
        header('Location: /cwJS/account?error=invdt');
        die();
    }
    $req = $pdo->prepare('UPDATE USERS SET username = :user WHERE id = :id');
    $req->bindValue(':user', $_POST["id"]);
    $_SESSION['username'] = $_POST["id"];
} elseif (isset($_POST['pwd']) && isset($_POST['pwd2'])) {
    if (!preg_match(PhpPatterns::$pwdPattern, $_POST["pwd"])
        || !preg_match(PhpPatterns::$pwdPattern, $_POST['pwd2'])
        || $_POST['pwd'] !== $_POST['pwd2']) {
        header('Location: /cwJS/account?error=invdt');
        die();
    }
    $req = $pdo->prepare('UPDATE USERS SET pwd_hash = :pwd WHERE id = :id');
    $req->bindValue(':pwd', password_hash($_POST["pwd"], PASSWORD_DEFAULT));
} else {
    header('Location: /cwJS/account?error=invdt');
    die();
}

$req->bindValue(':id', $_SESSION["id"]);

if (!$req->execute()) {
    header('Location: /cwJS/account?error=inter');
    die();
}

session_regenerate_id();

header('Location: /cwJS/account');
die();