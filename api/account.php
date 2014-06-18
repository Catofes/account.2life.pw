<?php
include("./config.php");
session_start();

function r_checkusername($link)
{
	if($stmt=mysqli_prepare($link,"select * from trueuser where username=?")){
		mysqli_stmt_bind_param($stmt,"s",$_POST['username']);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row==false){//No User Found
			echo json_encode(Array("code"=>1));
			return 0;
		}
		echo json_encode(Array("code"=>0));
	}
}

function r_checkic($link)
{
	if($stmt=mysqli_prepare($link,"select * from invitationcode where code=?")){
		mysqli_stmt_bind_param($stmt,"s",$_POST['ic']);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row==false){//No User Found
			echo json_encode(Array("code"=>0));
			return 0;
		}
		if($row['usable']==0){
			echo json_encode(Array("code"=>0));
			return 0;
		}
		echo json_encode(Array("code"=>1));
	}   
}

function EchoResult($code)
{
	if($code==101)exit(0);
	$info=Array(
		'203'=>'Session timeout. Please relogin.',
		'204'=>'New password please.',
		'205'=>'Unnormal account status.',
		'206'=>'Username is unvalible.',
		'207'=>'Invitation code is error.',
		'208'=>'Information uncomplete.',
		'401'=>'User do not exit.',
		'402'=>'Permission deny.',
		'403'=>'User existed.',
		'404'=>'The number of users reached max.',
		'405'=>'Empty username or password is illegal.',
		'406'=>'You have reached you data limit.'
	);
	$info_cn=Array(
		'203'=>'登陆过期，请重新登陆。',
		'204'=>'请输新入密码。',
		'205'=>'您的帐户状态不正常。',
		'206'=>'用户名不可用。',
		'207'=>'邀请码错误。',
		'208'=>'信息不完整。',
		'401'=>'帐号不存在。',
		'402'=>'权限错误。',
		'403'=>'用户名被占用。',
		'404'=>'账户数量到达上限。',
		'405'=>'帐户名密码不可为空。',
		'406'=>'流量不足。'
	);
	$redirection=Array(
		'203'=>'login.php',
		'204'=>'',
		'205'=>'',
		'206'=>'',
		'207'=>'',
		'208'=>'',
		'401'=>'',
		'402'=>'',
		'403'=>'',
		'404'=>'',
		'405'=>'',
		'406'=>''
	);
	echo json_encode(Array('code'=>$code,'info'=>$info[$code],'info_cn'=>$info_cn[$code],'redirection'=>$redirection[$code]));
	exit(0);
}


function r_regist($link)
{
	if($_POST['username']=='')
		return 208;
	if($_POST['password']=='')
		return 208;
	if($_POST['ic']=='')
		return 208;
	if($stmt=mysqli_prepare($link,"select * from trueuser where username=?")){
		mysqli_stmt_bind_param($stmt,"s",$_POST['username']);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row!=false)
			return 206;
	}
	if($stmt=mysqli_prepare($link,"select * from invitationcode where code=?")){
		mysqli_stmt_bind_param($stmt,"s",$_POST['ic']);
		mysqli_stmt_execute($stmt);
		$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
		if($row==false)
			return 207;
		if($row['usable']==0)
			return 207;
	}
	if($stmt=mysqli_prepare($link,"insert into trueuser VALUES (NULL , ? , SHA1(?) , '0' , '0' , '1' ,'2099-01-01');")){
		mysqli_stmt_bind_param($stmt , "ss" , $_POST['username'] , $_POST['password']);
		mysqli_stmt_execute($stmt);
		if($stmt=mysqli_prepare($link,"update invitationcode set claim=(select id from trueuser where username=?),usable=0 where code=? ;")){
			mysqli_stmt_bind_param($stmt , "ss" , $_POST['username'],$_POST['ic']);
			mysqli_stmt_execute($stmt);
			echo json_encode(Array("code"=>101));
		}
	}
	return 101;
}

if($_GET['f']=='rcu'){
	r_checkusername($link);
	exit(0);
}
if($_GET['f']=='rcic'){
	r_checkic($link);
	exit(0);
}
if($_GET['f']=='r'){
	EchoResult(r_regist($link));
}
	

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
