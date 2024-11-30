<?php

include_once '../_server/interfaces/Component.php';
include_once '../_server/components/Button.php';
include_once '../_server/components/TopBar.php';
include_once '../_server/components/Header.php';
include_once '../_server/components/Script.php';

if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

?>