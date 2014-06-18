<?php
include("./config.php");
session_start();



function CheckAccount()
{
	if($_SESSION['login']!==TRUE)return 701;
	if($_SESSION['admin']!==TRUE)return 702;
	return 101;
}

function EchoResult($code)
{
	if($code==101)exit();
	$info=Array(
		'701'=>'You must login',
		'702'=>'You didn\'t have permit',
		'703'=>'Nothing to do',
		'704'=>'Same username have be in used',
		'705'=>'Mysql error'
	);
	echo json_encode(Array('code'=>$code,'info'=>$info[$code]));
	exit();
}

function ListUser($link)
{
	$login=CheckAccount();
	if($login!==101)return $login;
	$user=mysqli_fetch_all(mysqli_query($link,"select * from trueuser;"),MYSQLI_ASSOC);
	$result=Array();
	while(list($key,$value)=each($user)){
		$eachuser=Array('userid'=>$value['id'],'username'=>$value['username'],'level'=>$value['level'],'due_day'=>$value['due_day']);
		array_push($result,$eachuser);
	}
	$finalresult=Array('code'=>1,'result'=>$result);
	echo json_encode($finalresult);
	return 101;
}

function AddUser($link)
{
	$login=CheckAccount();
	if($login!==101)return $login;
	$username=$_GET['username'];
	$passwd=$_GET['passwd'];
	$level=$_GET['level'];
	$dueday=$_GET['dueday'];
	$user=mysqli_fetch_array(mysqli_query($link,"select * from trueuser where username='$username';"));
	if($user!=FALSE)return 704;
	if($stmt=mysqli_prepare($link,"insert into trueuser VALUES (NULL,?, SHA1(?), ?, '0', '0',?);")){
		mysqli_stmt_bind_param($stmt,"ssis",$username,$passwd,$level,$dueday);
		mysqli_stmt_execute($stmt);
		echo json_encode(Array('code'=>101,'user'=>mysqli_fetch_array(mysqli_query($link,"select * from trueuser where username='$username';"))));
		return 101;
	}
	return 705;
}

function UpdateUser($link)
{
	$login=CheckAccount();
	if($login!==101)return $login;
	$userid=$_GET['id'];
	$user=mysqli_fetch_array(mysqli_query($link,"select * from trueuser where id='$userid';"));
	if($user==FALSE)return 706;
}

if($_GET['f']=='ListUser'){
	EchoResult(ListUser($link));
}
if($_GET['f']=='AddUser'){
	EchoResult(AddUser($link));
}
EchoResult(703);

?>
