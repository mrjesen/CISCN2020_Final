<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
$go=0;
$action=isset($_REQUEST['action'])?$action=$_REQUEST['action']:'';
$id=isset($_REQUEST['id'])?$_REQUEST['id']:1;
checkid($id);

if ($action=="save" ){
	$saveas=trim($_REQUEST["saveas"]);
	$domain=trim(str_replace('http://','',$_POST["domain"]));
	if ($saveas=="add"){
	//验证域名是否被绑定
	$sql="select id from zzcms_userdomain where domain='$domain'";
	$rs = query($sql); 
	$row=num_rows($rs);
	if ($row) {
	showmsg('此域名已被绑定');
	}
	
	//验证用户是否已添加过域名绑定
	$sql="select id from zzcms_userdomain where username='$username' and del!=0";
	$rs = query($sql); 
	$row=num_rows($rs);
	if ($row) {
	showmsg('您已添加过域名绑定','domain_manage.php');
	}
	query("insert into zzcms_userdomain (username,domain)VALUES('$username','$domain') ");
	$go=1;
	}elseif ($saveas=="modify"){
	//验证域名是否被绑定
	$sql="select id from zzcms_userdomain where domain='$domain'";
	$rs = query($sql); 
	$row=num_rows($rs);
	if ($row) {
	showmsg('此域名已被绑定');
	}
	$sql="select username from zzcms_userdomain where id='$id'";
	$rs = query($sql); 
	$row = fetch_array($rs);
	if ($row["username"]<>$username) {
	markit();
	showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
	}
	query("update zzcms_userdomain set domain='$domain' where id='". $id."'");
	$go=1;
	}
}
?>
</head>
<body>
<div class="main">
<?php
include("top.php");
?>
<div class="pagebody">
<div class="left">
<?php
include("left.php");
?>
</div>
<div class="right">
<div class="content">
<?php 
if ($action=="add") {
?>
<div class="admintitle">绑定顶级域名</div>
<form action="?action=savedata&saveas=add" method="POST" name="myform" id="myform">
  <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr> 
      <td width="10%" align="right" class="border">域名：</td>
      <td class="border"> <input name="domain" type="text" />
        <input type="submit" name="Submit" class="buttons" value="提交" /></td>
    </tr>
</table>
 </form>
<?php
}
if ($action=="modify") {
$sql="select * from zzcms_userdomain where id='".$id."'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle">修改域名</div>  
<form action="?action=save&saveas=modify" method="POST" name="myform" id="myform">
  <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr> 
      <td width="10%" align="right" class="border">域名：</td>
      <td class="border"><input name="domain"  value="<?php echo $row["domain"]?>" type="text" />
        <input type="submit" class="buttons" value="提交" />
        <input name="id" type="hidden" value="<?php echo $row["id"]?>" /></td>
    </tr>
</table>
  </form>
<?php
}
if ($go==1){
echo "<script>location.href='domain_manage.php'</script>";
}
?>
</div>
</div>
</div>
</div>
</body>
</html>