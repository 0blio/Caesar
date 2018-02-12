<!-- 
		CAESAR

		Author : Michele '0blio' Cisternino
		Email  : miki.cisternino@gmail.com
		Github : https://github.com/0blio
	
		This project is released under the GPL 3 license. 	
-->

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=1,initial-scale=1,user-scalable=1" />
	<link rel='shortcut icon' type='image/x-icon' href='assets/images/favicon.ico' />
	<title>Caesar</title>
	
	<link href="http://fonts.googleapis.com/css?family=Lato:100italic,100,300italic,300,400italic,400,700italic,700,900italic,900" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/login_styles.css" />

	<style>
		#signin { background-color: #c0392b;  }
		#signin:hover { background-color: #e74c3c; }
		#title { color: #95a5a6; text-align: center; }
		#error { color: #c0392b; text-align: center; }
		#success { color: #2ecc71; text-align: center; }
	</style>
	
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	<section class="container login-form" style="">
		<form method="post" action="" role="login">

			<div class="form-group">
				<?php if (isset($message)) echo $message; ?>
   				<div class="input-group">
      				<div class="input-group-addon"><span class="glyphicon glyphicon-tasks"></span></div>
					<input type="text" name="db_name" placeholder="Database name" required class="form-control input-lg" autofocus />
				</div>
			</div>

			<div class="form-group">
   				<div class="input-group">
      				<div class="input-group-addon"><span class="glyphicon glyphicon-tasks"></span></div>
					<input type="text" name="db_user" placeholder="Database username" class="form-control input-lg" autofocus />
				</div>
			</div>

			<div class="form-group">
   				<div class="input-group">
      				<div class="input-group-addon"><span class="glyphicon glyphicon-tasks"></span></div>
					<input type="password" name="db_password" placeholder="Database password" class="form-control input-lg" />
				</div>
			</div>

			<div class="form-group">
   				<div class="input-group">
      				<div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
					<input type="text" name="username" placeholder="Caesar username" required class="form-control input-lg" />
				</div>
			</div>

			<div class="form-group">
   				<div class="input-group">
      				<div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
					<input type="password" name="password" placeholder="Caesar password" required class="form-control input-lg" />
				</div>
			</div>
			<button id="signin" type="submit" name="go" class="btn btn-lg btn-block btn-success">INSTALL</button>
			<section>
				<a href="https://github.com">Github</a>
			</section>
		</form>
	</section>
</body>
</html>
