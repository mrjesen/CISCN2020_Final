<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>展厅留言</title>
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
<div class="admintitle">展厅留言本</div>
<?php
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);
$page_size=pagesize_ht;  
$show=isset($_GET["show"])?$_GET['show']:'';

$sql="select * from zzcms_guestbook where passed=1 and saver='".$username."' ";
if ($show=="new") {
$sql=$sql." and looked=0 ";
}

$offset=($page-1)*$page_size;
$rs = query($sql); 
$totlenum= num_rows($rs);  
$totlepage=ceil($totlenum/$page_size);

$sql=$sql." order by id desc limit $offset,$page_size";
$rs = query($sql); 
$row= num_rows($rs);//返回记录数
if(!$row){
echo "暂无信息";
}else{
?>
<form name="myform" method="post" action="del.php" onSubmit="return anyCheck(this.form)">
  <table width="100%" border="0" cellpadding="5" cellspacing="1"  class="bgcolor">
    <tr class="trtitle"> 
    <td width="40%" class="border">部分内容</td>
	<td width="20%" class="border">留言时间</td>
	<td width="10%" class="border" align="center">状态</td>
	<td width="10%" align="center" class="border">操作</td>
	<td width="10%" align="center" class="border">
	<label for="chkAll" style="text-decoration: underline;cursor: hand;">全选</label></td>
    </tr>
          <?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td><a href="ztliuyan_show.php?id=<?php echo $row["id"]?>" target="_blank"><?php echo cutstr($row["content"],10)?></a></td>
      <td><?php echo $row["sendtime"]?></td>
      <td align="center"><?php if ($row["looked"]==0){ echo "<span class='textbg'>未读</span>";} else {echo "已读";}?></td>
      <td align="center"><a href="ztliuyan_show.php?id=<?php echo $row["id"]?>" target="_blank">查看</a></td>
      <td align="center"><input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>" /></td>
    </tr>
<?php
}
?>
  </table>

<div class="fenyei" >
<?php echo showpage()?> 
 <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox" />
          <label for="chkAll">全选</label>
<input name="submit"  type="submit" class="buttons"  value="删除" onClick="return ConfirmDel()">
<input name="pagename" type="hidden"  value="ztliuyan.php?page=<?php echo $page ?>" /> 
<input name="tablename" type="hidden" id="tablename" value="zzcms_guestbook" /> 
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