<?php
include("./config.php");
session_start();

function login($link)
{
	$username=$_POST['username'];
	$passwd=substr(sha1($_POST['passwd']),0,16);
	if($stmt=mysqli_prepare($link,"select * from trueuser where username=?")){
		mysqli_stmt_bind_param($stmt,"s",$username);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row==false)return 201;//No User Found
		if($passwd!==$row['passwd'])return 202;//passwd error
		$_SESSION['login']=TRUE;
		$_SESSION['statue']=$row['statue'];
		$_SESSION['userid']=$row['id'];
		if($row['admin']==1)
			$_SESSION['admin']=TRUE;
		return 101;
	}
	return 301;
}

function logout($link)
{
	$_SESSION['login']=FALSE;
	session_unset();
	session_destroy();
	return 101;
}

function changepasswd($link)
{
	if($_SESSION['login']!==TRUE)return 203;
	$userid=$_SESSION['userid'];
	if($_POST['newpasswd']=='')return 204;
	$passwd=substr(sha1($_POST['newpasswd']),0,16);
	mysqli_query($link,"update trueuser set passwd='$passwd' where id='$userid';");
	return 101;
}

function changestatus($link)
{
	if($_SESSION['login']!==TRUE)return 203;
	if($_SESSION['statue']!==1)return 205;
	if($stmt=mysqli_prepare($link,"select * from user where username=?")){
		mysqli_stmt_bind_param($stmt,"s",$_GET['username']);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row==false)return 401;
		if($row['belong_user']!==$_SESSION['userid'])return 402;
		$username=$row['username'];
		if($row['active']==0){
			$userid=$_SESSION['userid'];
			$user['user']=mysqli_fetch_array(mysqli_query($link,"select * from trueuser where id='$userid';"));
			$userlevel=$user['user']['level'];
			$user['level']=mysqli_fetch_array(mysqli_query($link,"select * from level where level='$userlevel';"));
			$user['vpnuser']=mysqli_fetch_all(mysqli_query($link,"select * from user where belong_user='$userid' and active = 1;"),MYSQLI_ASSOC);
			if($user['vpnuser']==FALSE){
				$vpnnum=0;
			}else{
				$vpnnum=(!is_array($user['vpnuser']))? 1 : count($user['vpnuser']);
			}
			if($vpnnum>=$user['level']['max_user'])return 404;
		}
		mysqli_query($link,"update user set active=(!active) where username='$username';");
		return 101;
	}
	return 301;
}

function changevpn($link)
{
	if($_SESSION['login']!==TRUE)return 203;
	if($_SESSION['statue']!==1)return 205;
	if($stmt=mysqli_prepare($link,"select * from user where username=?")){
		mysqli_stmt_bind_param($stmt,"s",$_GET['username']);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row==false)return 401;
		if($row['belong_user']!==$_SESSION['userid'])return 402;
		if($stmt1=mysqli_prepare($link,"select * from user where username=?")){
			mysqli_stmt_bind_param($stmt1,"s",$_POST['newuser']);
			mysqli_stmt_execute($stmt1);
			$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt1));
			if($_GET['username']!==$_POST['newuser'])
				if($row!=false)return 403;
			if($_POST['newuser']==''||$_POST['newpasswd']=='')return 405;
			if($stmt2=mysqli_prepare($link,"update user set username=?,password=ENCRYPT(?) where username=?")){
				mysqli_stmt_bind_param($stmt2,"sss",$_POST['newuser'],$_POST['newpasswd'],$_GET['username']);
				mysqli_stmt_execute($stmt2);
				return 101;
			}
		}
	}
}

function newvpn($link)
{
	if($_SESSION['login']!==TRUE)return 203;
	if($_SESSION['statue']!==1)return 205;
	$userid=$_SESSION['userid'];
	$user['user']=mysqli_fetch_array(mysqli_query($link,"select * from trueuser where id='$userid';"));
	$userlevel=$user['user']['level'];
	$user['level']=mysqli_fetch_array(mysqli_query($link,"select * from level where level='$userlevel';"));
	$user['vpnuser']=mysqli_fetch_all(mysqli_query($link,"select * from user where belong_user='$userid';"),MYSQLI_ASSOC);
	if($user['vpnuser']==FALSE){
		$vpnnum=0;
	}else{
		$vpnnum=(!is_array($user['vpnuser']))? 1 : count($user['vpnuser']);
	}
	if($vpnnum>=$user['level']['max_user'])return 404;
	if($_POST['newuser']==''||$_POST['newpasswd']=='')return 405;
	if($stmt=mysqli_prepare($link,"select * from user where username=?")){
		mysqli_stmt_bind_param($stmt,"s",$_POST['newuser']);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row!=false)return 403;
		if($stmt2=mysqli_prepare($link,"insert into user VALUES (?, ENCRYPT(?), '1', CURRENT_TIMESTAMP, '', NULL, NULL, '30', '10737418240', '0', '0', '1', '10', ?);")){
			mysqli_stmt_bind_param($stmt2,"ssi",$_POST['newuser'],$_POST['newpasswd'],$_SESSION['userid']);
			mysqli_stmt_execute($stmt2);
			return 101;
		}
	}
}


function echocode($code){
	if($code==203){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/login.php\">
			<title>跳转中</title>   
			</head>
			</body>
			登录已过期，请重新登录。
			</body>
			</html>";
		return 0;
	}
	if($code==204){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			请输入新密码。
			</body>
			</html>";
		return 0;
	}
    if($code==205){
        echo "<html>
            <head>
            <meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
            <title>跳转中</title>   
            </head>
            </body>
            您的账户状态不正常，请查看首页的状态通告并联系管理员。
            </body>
            </html>";
        return 0;
    }
	if($code==401){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			帐号不存在。
			</body>
			</html>";
		return 0;
	}
	if($code==402){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			您无权禁用此帐号。
			</body>
			</html>";
		return 0;
	}
	if($code==403){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			用户名已被占用。
			</body>
			</html>";
		return 0;
	}
	if($code==404){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			不可增加额外的帐号。
			</body>
			</html>";
		return 0;
	}
	if($code==405){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			账户名和密码不能为空。
			</body>
			</html>";
		return 0;
	}
	if($code==406){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			流量不足，无法启用。
			</body>
			</html>";
		return 0;
	}
	if($code==101){
		echo "<html>
			<head>
			<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
			<title>跳转中</title>   
			</head>
			</body>
			操作成功。
			</body>
			</html>";
		return 1;
	}
	echo "<html>
		<head>
		<meta http-equiv=\"refresh\" content=\"1;url=/index.php\">
		<title>跳转中</title>   
		</head>
		</body>
		操作失败。
		</body>
		</html>";
	return 0;
}

if($_GET['function']==='login'){
	$code=login($link);
	echo $code;
	if($code==101){
		header("Location:/index.php");
	}
	else{
		header("Location:/login.php?status=error");
	}
	exit(0);
}
if($_GET['function']==='logout'){
	logout($link);
	header("Location:/login.php");
	exit(0);
}
if($_GET['function']==='changepasswd'){
	$code=changepasswd($link);
	echocode($code);
	if($code==101)logout($link);	
	exit(0);
}
if($_GET['function']==='changestatus'){
	$code=changestatus($link);
	echocode($code);
	exit(0);
}
if($_GET['function']==='changevpn'){
	$code=changevpn($link);
	echocode($code);
	exit(0);
}
if($_GET['function']==='newvpn'){
	$code=newvpn($link);
	echocode($code);
	exit(0);
}
?>
