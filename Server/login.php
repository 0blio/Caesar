<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio
	
		This project is released under the GPL 3 license. 	
	*/

	session_start ();

	// If the user is already logged in 
	if (isset($_SESSION['username'])) {
		header ('Location: shell.php');

	// else if Caesar has not been installed yet
	} else if (!file_exists('database/config.php')) {
		header ('Location: install.php');

	// Otherwise if the user sent a login request 
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['username']) and isset($_POST['password'])) {
			include 'database/connection.php';

			$username = trim($_POST['username']);
			$password = $_POST['password'];

			$db_password = $db -> table('users') -> columns('password') -> eq('username', $username) -> findAll();

			// If account exists
			if (count($db_password) != 0) {
				$db_password = $db_password[0]['password'];
				$valid_credentials = password_verify ($password, $db_password);

				if ($valid_credentials) {
					$_SESSION['username'] = $username;
					header ('Location: shell.php');
				} else {
					$error = '<p id="error">Invalid credentials!</p>';
				}
			} else {
				$error = '<p id="error">Invalid credentials!</p>';
			}
		} else {
			$error = '<p id="error">Fill out all fields!</p>';
		}

	} 

	if (!isset($error))
		$error = '';

	include 'templates/login.template.php';
?>
