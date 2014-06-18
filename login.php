<?php
session_start();
if($_SESSION['login']==TRUE){
	header("Location: index.php");
	exit(0);
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>登录</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="dist/css/bootstrap.css" rel="stylesheet">
		<link href="css/login.css" rel="stylesheet">
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a href="#" class="navbar-brand">物院量子传输研究所</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
					</ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="register.php">注册</a></li>
                    </ul>
				</div>
			</div>
		</div>
		<div class="container">
			<?php if($_GET['status']==="error") echo "<div class=\"alert alert-danger\">用户名密码错误</div>";?>
			<form class="form-signin" method="post" action="module/account.php?function=login" onSubmit="return this" >
				<h2 class="form-signin-heading">登录</h2>
				<input type="text" id=username" name="username" class="form-control" placeholder="Username" required="" autofocus="" autocomplete="off">
				<input type="password"id="passwd" name="passwd" class="form-control" placeholder="Password" required="">
				<button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>
			</form>
		</div>
        <div class="footer">
			<div class="container">
				<p class="text-muted credit">
				Provide by
				<a href="about.php">Catofes</a>
				</p>
			</div>
		</div>
	<script type="text/javascript" src="/dist/jquery-1.10.2.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>

	</body>
</html>
