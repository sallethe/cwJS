<?php

session_start();
session_destroy();
setcookie(session_name(), session_id(), time() - 3600);
$_SESSION = array();
header('Location: /cwJS/login/');
die();