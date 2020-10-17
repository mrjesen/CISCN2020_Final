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
$action=isset($_REQUEST["action"])?$_REQUEST["action"]:'';
$id=isset($_REQUEST["id"])?$_REQUEST["id"]:1;
checkid($id);

if ($action=="savedata" ){
	$saveas=trim($_REQUEST["saveas"]);
	$content=rtrim($_POST["info_content"]);
	if ($saveas=="add"){
	query("insert into zzcms_msg (content)VALUES('$content') ");
	$go=1;
	}elseif ($saveas=="modify"){
	query("update zzcms_msg set content='$content' where id='". $id."'");
	$go=1;
	}
}
?>
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
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
<div class="admintitle">添加短信内容模板</div>
<form action="?action=savedata&saveas=add" method="POST" name="myform" id="myform">
  <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr> 
      <td width="10%" align="right" class="border">内容</td>
      <td class="border"> <textarea name="info_content" id="info_content" ></textarea> 
       	<script type="text/javascript">CKEDITOR.replace('info_content');	</script>      </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"> 
        <input type="submit" name="Submit" class="buttons" value="提交" ></td>
    </tr>
</table>
 </form>
<?php
}
if ($action=="modify") {
$sql="select * from zzcms_msg where id='".$id."'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle">修改短信内容模板</div>  
<form action="?action=savedata&saveas=modify" method="POST" name="myform" id="myform">
  <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr> 
      <td width="10%" align="right" class="border">内容</td>
      <td class="border"> <textarea name="info_content" id="info_content" ><?php echo stripfxg($row["content"],true)?></textarea> 
	  	<script type="text/javascript">CKEDITOR.replace('info_content');	</script>        </td>
    </tr>
    <tr>
      <td align="right" class="border"><input name="id" type="hidden" value="<?php echo $row["id"]?>"></td>
      <td class="border">
<input type="submit" name="Submit2" class="buttons" value="修改"></td>
    </tr>
</table>
  </form>
<?php
}
if ($go==1){
echo "<script>location.href='msg_manage.php'</script>";
}
?>
</div>
</div>
</div>
</div>
</body>
</html>