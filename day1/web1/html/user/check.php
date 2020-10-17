<?php
$usersf='';
$userid='';
if (!isset($_COOKIE["UserName"]) || !isset($_COOKIE["PassWord"])){
echo "<script>location.href='/user/login.php';</script>";
}else{
$username=nostr($_COOKIE["UserName"]);
	$rs=query("select id,usersf,lastlogintime from zzcms_user where lockuser=0 and username='".$username."' and password='".$_COOKIE["PassWord"]."'");
	$row=num_rows($rs);
		if (!$row){
		setcookie("UserName",'xxx',1,"/");//清缓存，让登录页直接显示登录表单
		setcookie("PassWord",'xxx',1,"/");//清缓存，让登录页直接显示登录表单
		echo "<script>alert('密码不正确，请重新登录');location.href='/user/login.php';</script>";
		}else{
		$row=fetch_array($rs);
		$usersf=$row['usersf'];//left.php中用
		$userid=$row['id'];//top中用
		$lastlogintime=$row['lastlogintime'];
		query("update zzcms_user set loginip = '".getip()."' where username='".$username."'");//更新最后登录IP
		
			if (date('Y-m-d')>date('Y-m-d',strtotime($lastlogintime))){
			query("update zzcms_user set totleRMB = totleRMB+".jf_login." where username='".$username."'");//登录时加积分
			query("insert into zzcms_pay (username,dowhat,RMB,mark,sendtime) values('".$username."','每天登录用送积分','+".jf_login."','','".date('Y-m-d H:i:s')."')");
			}
		
		query("update zzcms_user set lastlogintime = '".date('Y-m-d H:i:s')."' where username='".$username."'");//更新最后登录时间
		}
}
?>