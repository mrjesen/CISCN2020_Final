<?php
include("admin.php");
checkadminisdo("user");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<?php
$go=0;
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$id=isset($_REQUEST['id'])?$_REQUEST['id']:0;
checkid($id,1);

if ($action=="savedata" ){
	$saveas=$_GET["saveas"];
	if ($saveas=="add"){
	query("insert into zzcms_userdomain (username,domain)VALUES('$username','$domain') ");
	$go=1;
	}elseif ($saveas=="modify"){
	query("update zzcms_userdomain set domain='$domain' where id='". $id."' ");
	$go=1;
	}
}
?>
</head>
<body>

<?php 
if ($action=="add") {
?>
<div class="admintitle">绑定顶级域名</div>
<form action="?action=save&saveas=add" method="POST" name="myform" id="myform">
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
        <input type="submit" name="Submit2" class="buttons" value="提交" />
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
</body>
</html>