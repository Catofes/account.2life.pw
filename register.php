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
		<title>注册</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="css/register.css" rel="stylesheet">
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
                        <li><a href="login.php">登陆</a></li>
                    </ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<div class="container" style="margin-left:auto;margin-right:auto">
					<h3>注册</h3>
					<br>
					<div id="d_w" class="myhidden">
					<div class="alert alert-danger" id="a_w" style="width:300px;margin-bottom:0px;">成功！</div>
					<br>	
					</div>
					<div class="span3">
							<div id="g_un">
							<p>用户名:</p>
							<input type="text" id="i_un" value="" placeholder="Username" class="span3">
							</div>
							<div id="g_pw">
							<p>密码:</p>
							<input type="password" id="i_pw" value="" placeholder="Password" class="span3 input_error">
							</div>
							<div id="g_rpw">
							<input type="password" id="i_rpw" value="" placeholder="Retype Password" class="span3">
							</div>
							<div id="g_ic">
							<p>邀请码:</p>
							<input type="text" id="i_ic" value="" placeholder="Invitation Code" class="span3">
							</div>
							<br>
							<button id="b_s" class="btn btn-lg btn-success btn-block" onclick="regist()" style="width:300px;">注册</button>
					</div>	
				</div>	
				</div>	
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
	<script type="text/javascript" src="/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/dist/js/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="js/register.js"></script>
	</body>
</html>
