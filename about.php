<?php
	session_start();
	if($_SESSION['login']!==true){
		header("Location: /login.php");
		exit(0);
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>物院量子传输研究所</title>
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
                    <a href="index.php" class="navbar-brand">物院量子传输研究所</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
						<li><a href="announce.php">通知</a></l>
						<li><a href="status.php">服务器信息</a></li>
						<li><a href="about.php">关于</a></li>
					</ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="module/account.php?function=logout">登出</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container" style="max-width:970px">
            <div class="row row-offcanvas row-offcanvas-right">
                <div class="col-xs-12">
                    <div class="jumbotron">
                        <h1>关于</h1>
                    </div>
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="userinfo">
                                <h4>关于</h4>
                            </div>
			    <div class="panel-body">
			    <b>HTTPS证书文件:</b> &nbsp;<a href="/crt.zip">点此下载</a><br><br>
			    <b>openvpn配置文件:</b> &nbsp;<a href="/config.zip">点此下载</a><br><br>
			    <b>Openvpn程序:</b> &nbsp;<a href="/openvpn.exe">点此下载</a><br><br>
			                               <b>openvpn-obfs程序:</b> &nbsp;<a href="/openvpn-obfs.exe">点此下载</a><br><br>
                            <b>Openvpn-obfs配置:</b> &nbsp;<a href="/small-obfs.ovpn">点此下载</a><br><br>
 
				</div>
					</div>
				</div>
			</div>
		</div>
<script type="text/javascript" src="/dist/jquery-1.10.2.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>

	</body>
</html>
					
