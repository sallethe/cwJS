<?php

session_start();

include_once "../_credentials.php";

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
    empty($firstname) ||
    empty($lastname) ||
    empty($username) ||
    empty($passwd1) ||
    empty($passwd2)
) {
    session_abort();
    header('Location: /cwJS/login?error=invdt');
    die();
}

$namePattern = '/^[a-zA-Z\- ]{2,50}$/';
$idPattern = '/^[a-zA-Z0-9]{5,20}$/';
$pwdPattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

if(
    !preg_match($idPattern, $username)
    || !preg_match($namePattern, $firstname)
    || !preg_match($namePattern, $lastname)
    || !preg_match($pwdPattern, $passwd1)
    || !preg_match($pwdPattern, $passwd2)
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

session_regenerate_id(true);

$_SESSION['username'] = $username;
$_SESSION['logged'] = true;
$_SESSION['fn'] = $firstname;
$_SESSION['ln'] = $lastname;

header('Location: /cwJS/account');
die();

