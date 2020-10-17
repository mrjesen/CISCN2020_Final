<?php
ob_start();//打开缓冲区，可以setcookie
define ("checkadminlogin",1);//当关网站时，如果是管理员登录时使链接正常打开
include("../inc/conn.php");
define('trytimes',10);//可尝试登录次数
define('jgsj',15*60);//间隔时间，秒
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
//判断是否为空
if($admin=='' || $pass==''){
WriteErrMsg("<li>用户名和密码不能为空</li>");
}else{

$admin=nostr(trim($_POST["admin"]));
$pass=trim($_POST["pass"]);
$pass=md5($pass);

$error=0;
$errmsg='';

//判断登录次数
$ip=getip();
$sql="select * from zzcms_login_times where ip='$ip' and count>='".trytimes."' and unix_timestamp()-unix_timestamp(sendtime)<".jgsj." ";
$rs = query($sql); 
$row= num_rows($rs);
if ($row){
$jgsj=jgsj/60;
$error=1;
$errmsg=$errmsg."<li>密码错误次数过多，请于".$jgsj."分钟后再试！</li>";
}

//判断验证码是否正确
//checkyzm($_POST["yzm"]);

if ($error==1){
WriteErrMsg($errmsg);
}else{

$sql = "select * from zzcms_admin where admin='" .$admin. "' and pass='". $pass ."'";
	$rs = query($sql);
	$row= num_rows($rs);//返回记录数
if (!$row){
//记录登录次数
	$sqln="select * from zzcms_login_times where ip='$ip'";
	$rsn =query($sqln); 
	$rown= num_rows($rsn);
		if ($rown){
			$rown= fetch_array($rsn);	
			if ($rown['count']>=trytimes && strtotime(date("Y-m-d H:i:s"))-strtotime($rown['sendtime'])>jgsj){//15分钟前登录过的归0
			query("update zzcms_login_times set count = 0 where ip='$ip'");
			}
		query("update zzcms_login_times set count = count+1,sendtime='".date('Y-m-d H:i:s')."' where ip='$ip'");//有记录的更新
		}else{
		query("insert into  zzcms_login_times (count,sendtime,ip)values(1,'".date('Y-m-d H:i:s')."','$ip')");
		}
	$sqln="select * from zzcms_login_times where ip='$ip'";
	$rsn =query($sqln); 
	$rown= fetch_array($rsn);
	$count=	$rown['count'];
	$trytimes=trytimes-$count;
	echo "<script>alert('用户名或密码错误！你还可以尝试 $trytimes 次');history.back()</script>";			
}else{
	query("delete from zzcms_login_times where ip='$ip'");//登录成功后，把登录次数记录删了
	query("delete from zzcms_login_times where sendtime<'".date("Y-m-d",strtotime("-30 day"))."'");//登录成功后，清理30天之前的无用记录
	$sql="update zzcms_admin set showlogintime=lastlogintime,showloginip=loginip,logins=logins+1,
	loginip='".getip()."',lastlogintime='".date('Y-m-d H:i:s')."' where admin='$admin'";
	query($sql);
	setcookie("admin",$admin,time()+3600*24*365,"/");
	setcookie("pass",$pass,time()+3600*24*365,"/");
	echo "<script>location.href='index.php'</script>";
}
}
}
?>
</body>
</html>