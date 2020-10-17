<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
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
<div class="content">
<div class="admintitle">
<span><input name="Submit2" type="button" class="buttons" value="添加" onClick="javascript:location.href='licence.php?do=add'" /></span>资质证书管理</div>   
<?php
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);

$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_licence where editor='".$username."' ";
$rs = query($sql); 
$row = fetch_array($rs);
$totlenum = $row['total'];
$totlepage=ceil($totlenum/$page_size);

$sql="select * from zzcms_licence where editor='".$username."' ";	
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo  "暂无信息";
}else{
?>
  <form name="myform" method="post" action="del.php" onSubmit="return anyCheck(this.form)">
  <table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
    <tr class="trtitle"> 
      <td width="29%" class="border"> 资质证书名称</td>
	  <td width="35%" align="center" class="border">证件</td>
	  <td width="14%" align="center" class="border">审核</td>
	  <td width="13%" align="center" class="border">管理</td>
	  <td width="9%" align="center" class="border">删除</td>
    </tr>
    <?php
	while($row=fetch_array($rs)){
	?>
    <tr class="trcontent"> 
      <td><?php echo $row["title"]?></td>
      <td height="30" align="center"> <a href="<?php echo $row["img"]?>" target="_blank"><img src="<?php echo getsmallimg($row["img"])?>"></a>      </td>
      <td align="center"><?php if ($row["passed"]==0) { echo "<font color=red>待审</font>";} else{ echo "已审"; }?></td>
            <td align="center"><a href="licence.php?do=modify&id=<?php echo $row["id"]?>">修改</a></td>
            <td align="center"><input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>" /></td>
    </tr>
<?php
}
?>
</table>
<div class="fenyei">
<?php echo showpage()?> 
  <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox" />
   <label for="chkAll">全选</label>
<input name="submit"  type="submit" class="buttons"  value="删除" onClick="return ConfirmDel()" >
        <input name="pagename" type="hidden" id="page2" value="licence.php?page=<?php echo $page ?>"> 
		<input name="tablename" type="hidden" id="tablename" value="zzcms_licence">  
</div>
</form>
<?php
}
?>
</div>
</div>
</div>
</div>
</body>
</html>