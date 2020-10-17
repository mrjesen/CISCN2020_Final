<?php
include("../inc/conn.php");
if (isset($_COOKIE["admin"]) && isset($_COOKIE["pass"])){
	$sql="select * from zzcms_admin where admin='".$_COOKIE["admin"]."'";
	$rs=query($sql) or showmsg('查寻管理员信息出错');
	$ok=is_array($row=fetch_array($rs));
	if($ok){
		if ($_COOKIE["pass"]!=$row['pass']){
		showmsg('管理员密码不正确，你无权进入该页面','../"'.admin_mulu.'"/login.php');
		}
	}else{
	showmsg('管理员已不存在，你无权进入该页面','../"'.admin_mulu.'"/login.php');
	}
}else{
echo "<script>top.location.href = '../".admin_mulu."/login.php';</script>";
}
?>