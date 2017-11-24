<?php
	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio
	
		This project is released under the GPL 3 license. 	
	*/

	include 'include.php';
	include 'config.php';

	use PicoDb\Database;

	$db = new Database([
		'driver' => 'mysql',
		'hostname' => $hostname,
		'username' => $username,
		'password' => $password,
		'database' => $database,
	]); 

	$db -> execute ('SET NAMES utf8');
	$db -> execute ('SET CHARACTER SET utf8');
	
