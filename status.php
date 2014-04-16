<?php
	include("./module/config.php");
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
                        <h1>服务器情况</h1>
                    </div>
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="userinfo">
                                <h4>流量使用情况</h4>
                            </div>
							<div class="panel-body">
<?php 
	echo "<table class=\"table\"
		<tr>
		<td>用户名</td>
		<td>使用量</td>
		<td>套餐量</td>
		</tr>";
	$user['user']=mysqli_fetch_all(mysqli_query($link,"select * from trueuser"),MYSQLI_ASSOC);
	while(list($key,$var)=each($user['user'])){
		$userid=$var['id'];
		$use=mysqli_fetch_all(mysqli_query($link,"select * from user where belong_user='$userid';"),MYSQLI_ASSOC);
		$monthused=0;
		while(list($key1,$var1)=each($use))
			$monthused+=$var1['month_used'];
		$level=$var['level'];
		$total=mysqli_fetch_array(mysqli_query($link,"select * from level where level='$level';"))['quote_cycle'];
		echo "<tr>
			<td>".$var['username']."</td>
			<td>".round($monthused/1024/1024/1024,4)."GB</td>
			<td>".(($var['level']==0) ? "按量计费" : (round($total/1024/1024/1024,4))."GB")."</td>
			</tr>";
	}
	echo "</table>";
?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<script type="text/javascript" src="/dist/jquery-1.10.2.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>

	</body>
</html>


