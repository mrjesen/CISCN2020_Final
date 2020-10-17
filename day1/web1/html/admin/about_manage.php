<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
</head>
<body>
<?php
checkadminisdo("about");
$action=isset($_GET["action"])?$_GET["action"]:'';
$id=isset($_POST["id"])?$_POST["id"]:0;
checkid($id,1);
if ($action=="del"){
checkadminisdo("about_del");
	if ($id<>0){
	query("delete from zzcms_about where id='$id'") ;
	}
	echo "<script>location.href='about_manage.php'</script>";
}
?>

<div class="admintitle">单页管理</div>
<div class="border2">
<input name="submit3" type="submit" class="buttons" onClick="javascript:location.href='about.php?action=add'" value="添加单页">
</div>
<?php
$sql="select * from zzcms_about";
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "暂无信息";
}else{
?>
<table width="100%" border="0" cellspacing="1">
  <tr class="trtitle"> 
    <td width="5%" height="25" align="center">ID</td>
    <td width="25%" align="center">名称</td>
    <td width="25%" align="center">链接地址</td>
    <td width="25%" height="20" align="center">操作选项</td>
  </tr>
  <?php
while($row = fetch_array($rs)){
?>
 <tr class="trcontent">  
    <td align="center" ><?php echo $row["id"]?></td>
    <td align="center" ><?php echo $row["title"]?></td>
    <td align="center" ><?php echo $row["link"]?></td>
    <td align="center"> <a href="about.php?action=modify&id=<?php echo $row["id"]?>">修改</a> 
      | <a href="?action=del&id=<?php echo $row["id"]?>" onClick="return ConfirmDel();">删除</a></td>
  </tr>
<?php
}
?>
</table>
<?php
}
?>
</body>
</html>