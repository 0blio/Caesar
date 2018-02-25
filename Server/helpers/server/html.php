<?php

	/*
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio

		This project is released under the GPL 3 license.
	*/

	// Convert a result set to html table
	function to_html_table ($db_table, $headers = NULL, $row_count = false) {
		$output = '<table>';

		// Printing headers
		if ($headers != NULL) {
			$output .= '<thead>';

			$output .= '<tr>';
			foreach ($headers as $header)
				$output .= '<th>' . $header . '</th>';
			$output .= '</tr>';

			$output .= '</thead>';
		}

		// Printing data
		if (count($db_table) > 0) {
			$output .= '<tbody>';

			if ($row_count == true)
				$count = 1;

			foreach ($db_table as $row) {
				$output .= '<tr>';

				if ($row_count == true) {
					$output .= '<td>' . $count . '</td>';
					$count++;
				}

				foreach ($row as $column_data) {
					/*
					if ($column_data == $row['output'])
						$output .= '<td>' . nl2br(htmlentities($column_data)) . '</td>';
					else
						$output .= '<td>' . $column_data . '</td>'; */

					if ((isset($row['online']) and $column_data == $row['online']) or
					    (isset($row['link']) and $column_data == $row['link'])
						 )
						$output .= '<td>' . $column_data . '</td>';
					else
						$output .= '<td>' . nl2br(htmlentities($column_data)) . '</td>';
				}

				$output .= '</tr>';
			}

			$output .= '</tbody>';
		}

		$output .= '</table>';

		return '<br>' . $output . '<br>';
	}

	function set_text_color ($string, $color) {
		return '<span style="color:' . $color . '">' . $string . '</span>';
	}


	function system_message ($message, $type = '') {
		$symbol = '';
		if ($type == 'added') { $symbol = set_text_color ('+', '#2ecc71'); }
		else if ($type == 'removed') { $symbol = set_text_color ('-', '#c0392b'); }
		else if ($type == 'notification') { $symbol = set_text_color ('*', '#3498db'); }
		else if ($type == 'error') { $symbol = set_text_color ('!', '#f39c12'); }

		$output = '<br>[' . $symbol . '] ' . $message . '<br>';
		return $output;
	}
