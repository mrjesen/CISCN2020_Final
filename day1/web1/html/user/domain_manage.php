<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
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

if ($action=="quxiao"){
	if ($username<>"" ){
	query("update zzcms_userdomain set del=1 where username='$username'");
	}
echo "<script>location.href='domain_manage.php'</script>";	
}

//不开放删除操作，当用户删除后，管理后台仍可看到被删除的域名以解除绑定用。
if ($action=="del"){
	//if ($username<>"" ){
	//query("delete from zzcms_userdomain where username='$username'") ;
	//}
	echo "<script>location.href='domain_manage.php'</script>";
}
?>

<div class="content">
<div class="admintitle">绑定域名</div>
<?php
$sql="select * from zzcms_userdomain where username='$username' and del=0";
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "<div class='box'><a href='domain.php?action=add' class='buttons'>添加域名</a></div>";
}else{
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="bgcolor">
<tr class="trtitle"> 
 <td width="10%"  align="center" class="border">ID</td>
 <td width="20%" align="center" class="border">域名</td>
 <td width="20%" align="center" class="border">状态</td>
 <td width="20%" align="center" class="border">操作</td>
  </tr>
  <?php
while($row = fetch_array($rs)){
?>
 <tr class="trcontent">  
    <td align="center" ><?php echo $row["id"]?></td>
    <td align="center" ><?php echo $row["domain"]?></td>
	<td align="center" ><?php 
	 if ($row["passed"]==1 ){ echo  '已生效';}else{echo '审请中';}
	 ?></td>
    <td align="center"> 
	<a href="domain.php?action=modify&id=<?php echo $row["id"]?>">修改</a> 
	| <a href="?action=quxiao&id=<?php echo $row["id"]?>" onClick="return ConfirmDel();">删除</a>
	
	</td>
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