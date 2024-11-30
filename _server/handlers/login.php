<?php

if (!isset($_POST['username'])
    || !isset($_POST['passwd'])
) {
    header('Location: /cwJS/login?error=invdt');
    die();
}

header('Location: /cwJS/login?error=invdt');
die();

// TODO : pattern check