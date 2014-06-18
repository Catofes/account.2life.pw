$("#d_w")[0].errordata=[];
$("input#i_un")[0].onchange=function(){
	$("#d_w")[0].errordata['loginfailed']="";
	requestdata = {"username" : this.value};
	$.ajax({
		type:	'POST',
		url:	'api/account.php?f=rcu',
		data:	requestdata,
		dataType:	"json",
		success:	function(data){
			if(data.code==0){
				$("div#g_un")[0].className="control-group error";
				$("#d_w")[0].errordata['username']="用户名已被占用";
				$("#d_w")[0].showerror();
			}else{
				$("div#g_un")[0].className="control-group success";
				$("#d_w")[0].errordata['username']="";
				$("#d_w")[0].showerror();
			}
		}
	})
}
$("#i_rpw")[0].onchange=function(){
	$("#d_w")[0].errordata['loginfailed']="";
	passwd=$("#i_pw")[0].value;
	rpasswd=$("#i_rpw")[0].value;
	if(passwd!=rpasswd){
		$("div#g_rpw")[0].className="control-group error";
		$("div#g_pw")[0].className="control-group error";
		$("#d_w")[0].errordata['passwd']="密码输入不一致";
		$("#d_w")[0].showerror();
	}else{
		$("div#g_rpw")[0].className="control-group success";
		$("div#g_pw")[0].className="control-group success";

		$("#d_w")[0].errordata['passwd']="";
		$("#d_w")[0].showerror();

	}
}
$("input#i_ic")[0].onchange=function(){
	$("#d_w")[0].errordata['loginfailed']="";
	requestdata = {"ic" : this.value};
	$.ajax({
		type:   'POST',
		url:    'api/account.php?f=rcic',
		data:   requestdata,
		dataType:   "json",
		success:    function(data){
			if(data.code==0){
				$("div#g_ic")[0].className="control-group error";
				$("#d_w")[0].errordata['username']="邀请码错误";
				$("#d_w")[0].showerror();
			}else{
				$("div#g_ic")[0].className="control-group success";
				$("#d_w")[0].errordata['username']="";
				$("#d_w")[0].showerror();
			}   
		}   
	})  
}
$("#d_w")[0].showerror=function(){
	this.haveerror=0;
	for(x in this.errordata)
	  if(this.errordata[x]!=''){
		  $("#a_w")[0].innerHTML=this.errordata[x];
		  this.haveerror=1;
		  break;
	  }
	if(this.haveerror!=0){
		$("button#b_s").removeClass("btn-success",300);
		$("button#b_s").addClass("btn-danger",300);
		$("button#b_s")[0].innerHTML="信息有误！";
		$("#d_w").removeClass("myhidden",600);
	}else{	
		$("button#b_s").removeClass("btn-danger",300);
		$("button#b_s").addClass("btn-success",300);
		$("button#b_s")[0].innerHTML="注册";
		$("#d_w").addClass("myhidden",600);
	}
}
function regist(){
	if($("#d_w")[0].haveerror!=0)return 0;
	registdata={"username" : $("#i_un")[0].value, "password" : $("#i_pw")[0].value, "ic": $("#i_ic")[0].value};
	$.ajax({
		type:   'POST',
		url:    'api/account.php?f=r',
		data:   registdata,
		dataType:   "json",
		success:    function(data){
			if(data.code==101){
				$("#a_w")[0].className="alert alert-success";
				$("#a_w")[0].innerText="注册成功，请登录。";
				$("#d_w").removeClass("myhidden",600);
				setTimeout(function(){window.location.replace("login.php");},2000);
			}else{
				$("#d_w")[0].errordata['loginfailed']=data.info_cn;
				$("#d_w")[0].showerror();
			}
		}
	}) 
}
