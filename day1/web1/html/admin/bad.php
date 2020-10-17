<?php include("admin.php");?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
</head>
<body>
<?php
checkadminisdo("badusermessage");
$action=isset($_REQUEST["action"])?$_REQUEST["action"]:'';
if ($action<>""){
	$id="";
	if(!empty($_POST['id'])){
    	for($i=0; $i<count($_POST['id']);$i++){
    	$id=$id.($_POST['id'][$i].',');
    	}
	$id=substr($id,0,strlen($id)-1);//去除最后面的","
	}

	if ($id==""){
	echo "<script>alert('操作失败！至少要选中一条信息。');history.back();</script>";
	}
}
if ($action=="del"){
	 if (strpos($id,",")>0){
		$sql="delete from zzcms_bad where id in (". $id .")";
	}else{
		$sql="delete from zzcms_bad where id='$id'";
	}

query($sql);
echo "<script>location.href='showbad.php'</script>";
}
if ($action=="lockip"){
	 if (strpos($id,",")>0){
		$sql="update  zzcms_bad set lockip=1 where id in (". $id .")";
	}else{
		$sql="update  zzcms_bad set lockip=1 where id='$id'";
	}
query($sql);
echo "<script>location.href='showbad.php'</script>";
}
?>
<div class="admintitle">不良操作记录</div>
<?php
$sql="select * from zzcms_bad order by id desc";
$rs=query($sql);
$row=num_rows($rs);	 
if (!$row){
echo "暂无信息";
}else{
?>
<form name="myform" method="post" action="">
    <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr class="trtitle"> 
      <td width="5%" align="center"><label for="chkAll" style="cursor: pointer;">全选</label></td>
      <td width="10%">用户名</td>
      <td width="10%">IP</td>
      <td width="10%">IP状态</td>
      <td width="10%">备注</td>
      <td width="10%">时间</td>
      <td width="5%" align="center">操作</td>
    </tr>
  <?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td align="center" > 
        <input name="id[]" type="checkbox" value="<?php echo $row["id"]?>">
      </td>
      <td><?php echo $row["username"]?></td>
      <td><?php echo $row["ip"]?></td>
      <td>
<?php if ($row["lockip"]==1) { echo"被封";} else{ echo"正常";}?>
</td>
      <td><?php echo urldecode($row["dose"])?></td>
      <td><?php echo $row["sendtime"]?></td>
      <td align="center" class="docolor"><a href="ShowBad.php?action=lockip&ID=<?php echo $row["id"]?>"> 
        </a><a href="usermanage.php?keyword=<?php echo $row["username"]?>">锁定该用户</a></td>
    </tr>
    <?php
}
?>
  </table>

    <div class="border"> <label><input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
        全选 </label>
        <input name="del" type="submit" value="删除选中的信息" onClick="myform.action='?action=del';myform.target='_self'">
        <input name="lockip" type="submit" value="封IP" onClick="myform.action='siteconfig.php#SiteOpen';myform.target='_self'">
        <input name="pagename" type="hidden"  value="showbad.php?page=<?php echo $page ?>">
        <input name="tablename" type="hidden"  value="zzcms_bad">
  </div>

</form>
<?php
}

?>
</body>
</html>