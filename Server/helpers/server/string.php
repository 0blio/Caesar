<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio
	
		This project is released under the GPL 3 license. 	
	*/

	// Check if a string starts with a specific prefix
	function startswith ($string, $prefix) { 
		return (strncmp ($string, $prefix, strlen($prefix)) === 0); 
	}

	// Get an array of arguments from the command
	function get_arguments ($command_root, $complete_command) {
		    $arguments = array();
		    
			if ($command_root !== $complete_command) {
				if ($complete_command[strlen($command_root)] == ' ') {
					$arguments = str_replace ($command_root, "", $complete_command);
					if ($arguments != "")
						$arguments = explode (" ", trim($arguments));
				}
			}

			return $arguments;  
	}

	// Check if the string contains only digits
	function only_digits ($string) {
		if (ctype_digit((string)$string))
			return true;
		else
			return false;
	}

	// Generate a random string with the specified lenght
	function random_string ($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
		    $str .= $charset[mt_rand(0, $count-1)];
		}
		return $str;
	}
