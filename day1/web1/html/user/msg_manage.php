<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
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
<?php
$action=isset($_REQUEST["action"])?$_REQUEST["action"]:'';
$id=isset($_REQUEST["id"])?$_REQUEST["id"]:1;
checkid($id);
if ($action=="elite"){
	if ($id<>"" ){
	query("Update zzcms_msg set elite=0 ");//只有一条为1的
	query("update zzcms_msg set elite=1 where id='$id'");
}
echo "<script>location.href='msg_manage.php'</script>";	
}

if ($action=="del"){
	if ($id<>"" ){
	query("delete from zzcms_msg where id='$id'") ;
	}
	echo "<script>location.href='msg_manage.php'</script>";
}
?>
<script language="JavaScript" src="/js/gg.js"></script>
<div class="content">
<div class="admintitle"><span><a href="msg.php?action=add" class="buttons">添加邮件/短信内容</a></span>邮件/短信内容设置</div>
<?php
$sql="select * from zzcms_msg";
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "暂无信息";
}else{
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="bgcolor">
  <tr class="title"> 
    <td width="10%" height="25" align="center" class="border">ID</td><td width="50%" align="center" class="border">内容</td><td width="10%" align="center" class="border">默认短信模板</td><td width="20%" height="20" align="center" class="border">操作</td>
  </tr>
  <?php
while($row = fetch_array($rs)){
?>
 <tr class="trcontent">  
    <td height="22" align="center" ><?php echo $row["id"]?></td>
    <td height="22" align="center" ><?php echo stripfxg($row["content"],true)?></td>
    <td height="22" align="center" ><?php
	if ($row["elite"]==1 ){ echo  "默认模板";}
	?>
	
	</td>
    <td align="center"> 
	<a href="?action=elite&id=<?php echo $row["id"]?>">设为默认</a> 
	| <a href="msg.php?action=modify&id=<?php echo $row["id"]?>">修改</a> 
    | <a href="?action=del&id=<?php echo $row["id"]?>" onClick="return ConfirmDel();">删除</a></td>
  </tr>
<?php
}
?>
</table>
<?php
}
?>
</div>
</div>
</div>
</div>
</body>
</html>