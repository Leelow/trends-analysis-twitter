<?php

    /* Sécurise les pages webs/API */

	session_start();
	if(!isset($_SESSION['connect']))
        $_SESSION['connect'] = false;

    /* Mémorisation */
    function remember() {
        $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    }

    /* Sécurisation */
    function onlyConnected() {
        if($_SESSION['connect'] == false)
            header('Location: login.php');
        exit();
    }

?>