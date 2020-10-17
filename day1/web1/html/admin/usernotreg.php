<?php
include("admin.php");
include("../inc/fy.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
</head>
<body>
<?php
checkadminisdo("usernoreg");
$action=isset($_GET["action"])?$_GET["action"]:'';
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);
$keyword=isset($_POST["keyword"])?$_POST["keyword"]:'';
?>
<div class="admintitle">未进行邮件验证的临时注册用户管理（应是注册机注册较多）</div>
<div class="border"> <form name="form1" method="post" action="?"> 
      用户名： 
        <input name="keyword" type="text" id="keyword" value="<?php echo $keyword?>"> 
        <input type="submit" name="Submit" value="查找">   
</form>
 </div>
<?php
$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select * from zzcms_usernoreg where id<>0 ";

if ($keyword<>"") {
	$sql=$sql. " and username like '%".$keyword."%' ";
}
$rs = query($sql); 
$totlenum= num_rows($rs);  
$totlepage=ceil($totlenum/$page_size);

$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo "暂无信息";
}else{

?>
<form name="myform" method="post" action="" onSubmit="return anyCheck(this.form)">
  <table width="100%" border="0" cellspacing="1" cellpadding="5">
    <tr class="trtitle"> 
      <td width="5%" align="center"><label for="chkAll" style="cursor: pointer;">全选</label></td>
      <td width="10%">用户名</td>
      <td width="10%" align="center">用户身份</td>
      <td width="10%" align="center">公司名</td>
      <td width="10%" align="center">联系人</td>
      <td width="10%" align="center">电话</td>
      <td width="10%" align="center">email</td>
      <td width="10%" align="center">审请时间</td>
    </tr>
<?php
while($row = fetch_array($rs)){
?>
     <tr class="trcontent">  
      <td align="center" class="docolor"> <input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>"></td>
      <td ><?php echo $row["username"]?></td>
      <td align="center" ><?php echo $row["usersf"]?> </td>
      <td align="center"><?php echo $row["comane"]?> </td>
      <td align="center"><?php echo $row["somane"]?></td>
      <td align="center"><?php echo $row["phone"]?></td>
      <td align="center"><?php echo $row["email"]?></td>
      <td align="center"><?php echo $row["regdate"]?></td>
    </tr>
<?php
}
?>
  </table>
      <div class="border"> <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
        <label for="chkAll" style="cursor: pointer;">全选</label> 
        <input type="submit" onClick="myform.action='del.php';myform.target='_self';return ConfirmDel()" value="删除选中的信息" > 
		 <input name="pagename" type="hidden"  value="usernotreg.php?page=<?php echo $page ?>">     
		 <input name="tablename" type="hidden"  value="zzcms_usernoreg">
       
  </div>
</form>
<div class="border center"><?php echo showpage_admin()?></div>
<?php
}
?>
</body>
</html>