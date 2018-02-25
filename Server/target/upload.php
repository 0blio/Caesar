<?php

  // If the request is valid
  /*, $_FILES['file_to_upload'])*/
  if (isset($_POST['unique_id'], $_POST['command'], $_POST['task_id'])) {
    $unique_id = $_POST['unique_id'];
    $command = $_POST['command'];
    $task_id = $_POST['task_id'];

    // If the user is not trying to alter the unique id (checking if the md5 string is valid)
    if (!preg_match('/[^A-Za-z0-9]/', $unique_id) and strlen($unique_id) == 32) {
      include '../database/connection.php';

      // Checking if the request is valid
      $valid_request = $db -> table ('tasks') -> eq ('command', $command) -> eq ('task_id', $task_id) -> exists();

      if ($valid_request) {

        $full_upload_path = "../files/" . $unique_id . "/" . basename($_FILES["file_to_upload"]["name"]);
        $upload_path_parts = pathinfo ($full_upload_path);

        // Assigning an incremental filename if the file already exists
        for ($i = 0; file_exists($full_upload_path); $i++)
          $full_upload_path = "../files/" . $unique_id . "/" . $upload_path_parts['filename'] . $i . "." . $upload_path_parts['extension'];

        if (move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $full_upload_path)) {
          echo 'UPLOADED';
        } else {
          echo 'Error';
        }
      }

    }

  } else {
    echo "Invalid request!";
  }
