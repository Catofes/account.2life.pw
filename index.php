<?php
include("./module/config.php");
session_start();
if($_SESSION['login']!==TRUE){
	header("Location:login.php");
	exit(0);
}

function getaccountinfo($link)
{
	$userid=$_SESSION['userid'];
	$user['user']=mysqli_fetch_array(mysqli_query($link,"select * from trueuser where id='$userid';"));
	$userlevel=$user['user']['level'];
	$user['level']=mysqli_fetch_array(mysqli_query($link,"select * from level where level='$userlevel';"));
	$user['vpnuser']=mysqli_fetch_all(mysqli_query($link,"select * from user where belong_user='$userid';"),MYSQLI_ASSOC);
	return $user;
}

function showalert()
{
	if($_SESSION['statue']==0)
		echo "<div class=\"alert alert-danger\">您的账户未启用，请联系管理员。</div>";
	if($_SESSION['statue']==2)
		echo "<div class=\"alert alert-danger\">您的账户已到期停用，请联系管理员。</div>";
	if($_SESSION['statue']==3)
		echo "<div class=\"alert alert-danger\">您的账户流量已用完。</div>";
}
$user=getaccountinfo($link);

if($user['vpnuser']==FALSE){
	$vpnnum=0;
	$monthuse=0;
}else{
	$vpnnum=(!is_array($user['vpnuser']))? 1 : count($user['vpnuser']);
	$monthuse=0;
	if(is_array($user['vpnuser'])){
		while(list($key,$var)=each($user['vpnuser']))
			$monthuse+=$var['month_used'];
	}else{
		$monthuse=$user['vpnuser']['month_used'];
	}
}
date_default_timezone_set('Asia/Shanghai');
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
                    <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".navbar-collapse">
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
			<div class="row row-offcanvas row-offcanvas-left">
				<div class="col-xs-12">
					<div class="jumbotron">
						<h1>用户管理</h1>
					</div>
					<div class="row">
						<?php showalert();?>
						<div class="panel panel-default">
							<div class="panel-heading" id="userinfo">
								<h4>用户信息</h4>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-xs-8 col-sm-6">
										<b>用户名: &nbsp; </b> <?php echo $user['user']['username'];?><br>
										<b>用户id ： &nbsp;</b> <?php echo $user['user']['id'];?><br>
										<b>用户等级 : &nbsp;</b> <?php echo $user['user']['level'];?><br>
										<b>用户状态 ： &nbsp;</b> <?php echo (($user['user']['statue']==1) ? "正常" : "不正常");?><br>
										<b>到期日期 ： &nbsp;</b> <?php echo $user['user']['due_day'];?><br>
									</div>
									<div class="col-xs-4 col-sm-3">
										<b>更改密码 : </b>
										<form method="post" action="/module/account.php?function=changepasswd" onSubmit="return this">
											<input type="text" name="newpasswd" class="form-control" placeholder="输入新密码"><br>
											<button class="btn btn-primary btn-block" type="submit">更改</button>
										</form>							
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading" id="vpninfo">
								<h4>vpn信息</h4>
							</div>
							<div class="panel-body">
								<br>
								<b>已用账户个数/可用账户个数： &nbsp;</b> <?php echo $vpnnum."/".$user['level']['max_user'];?><br><br>
								<b>已使用流量/总流量： &nbsp;</b> <?php echo round($monthuse/1024/1024/1024,3)."GB/".($user['level']['quote_cycle']/1024/1024/1024)."GB";?><br><br>
								<div class="progress">								
									<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $monthuse/$user['level']['quote_cycle']*100;?>%">
									</div><center>
									<?php echo round(($user['level']['quote_cycle']-$monthuse)/1024/1024/1024,3)."GB剩余"?>
									</center>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading" id="vpnadmin">
								<h4>vpn管理</h4>
							</div>
							<div class="panel-body">
								
<?php
	if($vpnnum==0)
		$vpn=array();
	if($vpnnum==1)
		$vpn=$user['vpnuser'];
	if($vpnnum>1)
		$vpn=$user['vpnuser'];
	while(list($key,$var)=each($vpn)){
		echo "
<div class=\"row\">
<div class=\"col-xs-8 col-sm-6\">
<br>
<b>vpn账户名: &nbsp;</b>".$var['username']."<br><br>
<b>vpn账户状态: &nbsp;</b>".($var['active'] ? "启用" : "停用")."<br><br>
<b>vpn账户流量使用情况: &nbsp;</b>".round($var['month_used']/1024/1024/1024,3)."GB<br>
<br>
<a href=\"module/account.php?function=changestatus&username=".$var['username']."\">".(!$var['active'] ? "启用" : "停用")."此账户</a>
</div>
<div class=\"col-xs-4 col-sm-3\">
<b>更改登录用户名和密码 : </b>
<form method=\"post\" action=\"/module/account.php?function=changevpn&username=".$var['username']."\" onSubmit=\"return this\">
<input type=\"text\" name=\"newuser\" class=\"form-control\" placeholder=\"输入新用户名\"><br>
<input type=\"text\" name=\"newpasswd\" class=\"form-control\" placeholder=\"输入新密码\"><br>
<button class=\"btn btn-primary btn-block\" type=\"submit\">更改</button>
</form>    
</div>
</div>
<hr />";
	}
?>
<b>新建帐号 : </b>
<form method="post" action="/module/account.php?function=newvpn" onSubmit="return this">
<input type="text" name="newuser" class="form-control" placeholder="输入新用户名"><br>
<input type="text" name="newpasswd" class="form-control" placeholder="输入新密码"><br>
<button class="btn btn-success btn-block" type="submit">新建</button>
</form>  
						</div>
					</div>
				</div>
			</div>
		</div>
<script type="text/javascript" src="/dist/jquery-1.10.2.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
	</body>
</html>
