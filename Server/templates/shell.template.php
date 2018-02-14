<!-- 
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio
	
		This project is released under the GPL 3 license. 	
-->

<html>
	<head>
		<title>Caesar</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<link rel="stylesheet" type="text/css" href="assets/css/shell_styles.css" />
		<link rel='shortcut icon' type='image/x-icon' href='assets/images/favicon.ico' />
		<script src="assets/jquery/jquery.min.js"></script>
		<script src="helpers/client/html.js"></script>
	</head>

	<body>
		<div id="output">
			<pre style="font-family:monospace; color:#e74c3c;">
  .oooooo.                                                   
 d8P'  `Y8b                                                  
888           .oooo.    .ooooo.   .oooo.o  .oooo.   oooo d8b 
888          `P  )88b  d88' `88b d88(  "8 `P  )88b  `888""8P 
888           .oP"888  888ooo888 `"Y88b.   .oP"888   888     
`88b    ooo  d8(  888  888    .o o.  )88b d8(  888   888     
 `Y8bood8P'  `Y888""8o `Y8bod8P' 8""888P' `Y888""8o d888b  
			<span style="color:#27ae60">Coded by </span><span style="color:#2980b9">0blio</span>                                      
			</pre>
			
			<pre style="color:#bdc3c7; font-family:monaco">
Legal disclaimer: Usage of Caesar for attacking targets without prior mutual consent is illegal. 
It is the end user's responsibility to obey all applicable local, state and federal laws. Developers 
assume no liability and are not responsible for any misuse or damage caused by this software.
			</pre>
                                                  
			Type 'help' for a list of commands. <br/><br/>
		</div>

		<span id="software"><span style="color:#EF2929">Caesar</span> <span style="color:#729FCF">» </span></span>
		<input id="input" type="text" name="command" autofocus contenteditable="true">
		<input id="csrf_token" style="display:none" value="<?php echo $_SESSION['csrf_token']; ?>">
		<span id="selected_target" style="display:none"></span>
		<span id="shell" style="display:none">false</span>

		<script src="controller-logic/clientside_controller.js"></script>
	</body>
</html>
