<?php 
include ("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script language = "JavaScript">	
function CheckForm(){	  
if (document.myform.sitename.value==""){
    alert("网站名称不能为空！");
	document.myform.sitename.focus();
	return false;
  }
  if (document.myform.url.value==""){
    alert("网址不能为空！");
	document.myform.url.focus();
	return false;
  }
  if (document.myform.content.value==""){
    alert("描述不能为空！");
	document.myform.content.focus();
	return false;
  }
  return true;  
} 
</script>
</head>
<body>
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
}


if ($do=="save"){
$page = isset($_POST['page'])?$_POST['page']:1;//只从修改页传来的值
checkid($page);
$id = isset($_POST['id'])?$_POST['id']:0;
checkid($id,1);
$passed = isset($_POST['passed'])?$_POST['passed']:0;
checkid($passed,1);
$elite = isset($_POST['elite'])?$_POST['elite']:0;
checkid($elite,1);

$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:0;
checkid($bigclassid,1);

$FriendSiteName=trim($_POST["sitename"]);
$url=addhttp($url);
$logo=addhttp($logo);

if ($_REQUEST["action"]=="add"){
checkadminisdo("friendlink_add");
query("INSERT INTO zzcms_link (bigclassid,sitename,url,logo,content,passed,elite,sendtime)VALUES('$bigclassid','$FriendSiteName','$url','$logo','$content','$passed','$elite','".date('Y-m-d H:i:s')."')");
}elseif ($_REQUEST["action"]=="modify") {
checkadminisdo("friendlink_modify");
query("update zzcms_link set bigclassid='$bigclassid',sitename='$FriendSiteName',url='$url',logo='$logo',content='$content',passed='$passed',elite='$elite',sendtime='".date('Y-m-d H:i:s')."' where id='$id'");	
}
$_SESSION["bigclassid"]=$bigclassid;

echo  "<script>location.href='linkmanage.php?b=".$bigclassid."&page=".$page."'</script>";
}



function add(){
//checkadminisdo("friendlink_add");
$sbigclassid = isset($_SESSION['bigclassid'])?$_SESSION['bigclassid']:'';
?>
<div class="admintitle">添加友情链接</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">    
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td align="right" class="border">所属类别：</td>
      <td class="border"> 
        <?php
		$sql = "select classid,classname from zzcms_linkclass order by xuhao asc";
	    $rs=query($sql);
        $row=num_rows($rs);
		if (!$row){
			echo "<a href='class.php?tablename=zzcms_linkclass'>添加类别</a>";
		}else{
		?>
		<select name="bigclassid" id="bigclassid">
                <option value="0" selected="selected">请选择类别</option>
                <?php
		while($row= fetch_array($rs)){
			?>
                <option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$sbigclassid) { echo "selected";}?>><?php echo $row["classname"]?></option>
                <?php
		  }
		  ?>
              </select>
		<?php
		}
		?>  
        </td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">网站名称：</td>
      <td class="border"><input name="sitename" type="text" id="sitename" size="50"></td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">网址：</td>
      <td class="border"> <input name="url" type="text" id="url" size="50"></td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">logo：</td>
      <td class="border"><input name="logo" type="text" id="logo" size="50"></td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">描述：</td>
      <td class="border"> <textarea name="content" cols="50" rows="3" id="content"></textarea></td>
    </tr>
    <tr> 
      <td align="right" class="border">审核：</td>
      <td class="border"> <input name="passed" type="checkbox" id="passed" value="1" checked>
        （选中为通过审核） </td>
    </tr>
    <tr> 
      <td align="right" class="border">首页显示：</td>
      <td class="border"> <input name="elite" type="checkbox" id="elite" value="1" checked>
        （选中显示在首页） </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"> <input type="submit" name="Submit" value="提交">
      <input name="action" type="hidden" id="action" value="add"></td>
    </tr>
  </table>
      </form>
<?php
}


function modify(){
//checkadminisdo("friendlink_modify");
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);
?>
<div class="admintitle">修改友情链接</div>
<?php
$sql="select * from zzcms_link where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr> 
      <td align="right" class="border">所属类别：</td>
      <td class="border"> 
        <?php
		$sqln = "select classid,classname from zzcms_linkclass order by xuhao asc";
	    $rsn=query($sqln);
        $rown=num_rows($rsn);
		if (!$rown){
			echo "<a href='class.php?tablename=zzcms_linkclass'>添加类别</a>";
		}else{
		?>
		<select name="bigclassid" id="bigclassid">
                <option value="0" selected="selected">请选择类别</option>
        <?php
		while($rown= fetch_array($rsn)){
		?>
        <option value="<?php echo $rown["classid"]?>" <?php if ($rown["classid"]==$row["bigclassid"]) { echo "selected";}?>><?php echo $rown["classname"]?></option>
          <?php
		  }
		  ?>
              </select>
		<?php
		}
		?> 
        </td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">网站名称：</td>
      <td class="border"><input name="sitename" type="text" id="title" value="<?php echo $row["sitename"]?>" size="50"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">网址：</td>
      <td class="border"> <input name="url" type="text" id="url" value="<?php echo $row["url"]?>" size="50"></td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">logo：</td>
      <td class="border"><input name="logo" type="text" id="logo" value="<?php echo $row["logo"]?>" size="50"></td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">描述：</td>
      <td class="border"> <textarea name="content" cols="50" rows="3" id="content"><?php echo stripfxg($row["content"])?></textarea> 
        <input name="id" type="hidden" id="id" value="<?php echo $row["id"]?>"> <input name="page" type="hidden" id="page" value="<?php echo $page?>"></td>
    </tr>
    <tr> 
      <td align="right" class="border">审核：</td>
      <td class="border"> <input name="passed" type="checkbox" id="passed" value="1" checked>
        （选中为通过审核） </td>
    </tr>
    <tr> 
      <td align="right" class="border">首页显示：</td>
      <td class="border"> <input name="elite" type="checkbox" id="elite" value="1" <?php if ($row["elite"]==1){ echo "checked";}?>>
        （选中显示在首页） </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"><input type="submit" name="Submit" value="修 改">
      <input name="action" type="hidden" id="action" value="modify"></td>
    </tr>
  </table>
      </form>
<?php
}
?>	  
</body>
</html>