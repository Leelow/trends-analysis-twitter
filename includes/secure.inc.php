<?php

	session_start();
	if(!isset($_SESSION['connect']))
		$_SESSION['connect'] = false;
	
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];

	if($_SESSION['connect'] == false)
		header('Location: ../login.php'); 



?>