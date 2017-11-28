<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio
	
		This project is released under the GPL 3 license. 	
	*/

	session_start();

	// If user is not logged in
	if (!isset($_SESSION['username'])) 
		header ('Location: login.php');
	else
		$_SESSION['csrf_token'] = md5(uniqid(rand(), TRUE));
	
	include 'templates/shell.template.php';
