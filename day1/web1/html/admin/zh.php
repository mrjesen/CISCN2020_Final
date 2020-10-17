<?php
include ("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
<script language="javascript" src="../js/timer.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.bigclassid.value==""){
    alert("请选择展会类型！");
	document.myform.bigclassid.focus();
	return false;
  }	 	  
if (document.myform.title.value==""){
    alert("展会名称不能为空！");
	document.myform.title.focus();
	return false;
  }
  if (document.myform.address.value==""){
    alert("展会地址不能为空！");
	document.myform.address.focus();
	return false;
  }
  if (document.myform.TimeStart.value==""){
    alert("展会开始时间不能为空！");
	document.myform.TimeStart.focus();
	return false;
  }
  if (document.myform.TimeEnd.value==""){
    alert("展会截止时间不能为空！");
	document.myform.TimeEnd.focus();
	return false;
  } 
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
checkadminisdo("zh");

$page = isset($_POST['page'])?$_POST['page']:1;//只从修改页传来的值
checkid($page);
$id = isset($_POST['id'])?$_POST['id']:0;
checkid($id,1);
$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:0;
checkid($bigclassid,1);

$passed = isset($_POST['passed'])?$_POST['passed']:0;
checkid($passed,1);

if (isset($_POST["elite"])){
$elite=$_POST["elite"];
	if ($elite>127){
	$elite=127;
	}elseif ($elite<0){
	$elite=0;
	}
}else{
$elite=0;
}
checkid($elite,1);

if ($_REQUEST["action"]=="add" && $_COOKIE["admin"]<>''){
checkadminisdo("zh_add");
query("INSERT INTO zzcms_zh (bigclassid,title,address,timestart,timeend,content,passed,elite,sendtime)VALUES('$bigclassid','$title','$address','$timestart','$timeend','$content','$passed','$elite','".date('Y-m-d H:i:s')."')");
}elseif ($_REQUEST["action"]=="modify") {
checkadminisdo("zh_modify");
query("update zzcms_zh set bigclassid='$bigclassid',title='$title',address='$address',timestart='$timestart',timeend='$timeend',content='$content',passed='$passed',elite='$elite',sendtime='".date('Y-m-d H:i:s')."' where id='$id'");	
}
echo  "<script>location.href='zh_manage.php?page=".$page."'</script>";
}

function add(){
//checkadminisdo("zh_add");
$szhclassid=isset($_SESSION["zhclassid"])?$_SESSION["zhclassid"]:'';
?>
<div class="admintitle">发布展会信息</div>
<form action="?do=save" method="post" name="myform" target="_self" id="myform" onSubmit="return CheckForm();">        
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td align="right" class="border">所属类别</td>
      <td class="border">   
        <?php
		$sql = "select classid,classname from zzcms_zhclass order by xuhao asc";
	    $rs=query($sql);
        $row=num_rows($rs);
		if (!$row){
			echo "<a href='class.php?tablename=zzcms_zhclass'>请先添加类别</a>";
		}else{
		?>
		<select name="bigclassid" id="bigclassid">
                <option value="" selected="selected">请选择类别</option>
                <?php
		while($row= fetch_array($rs)){
			?>
                <option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$szhclassid) { echo "selected";}?>><?php echo $row["classname"]?></option>
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
      <td width="100" align="right" class="border" >展会名称</td>
      <td class="border" > <input name="title" type="text" id="title" size="50" maxlength="50"></td>
    </tr>
    <tr> 
      <td align="right" class="border" >会议地址</td>
      <td class="border" > <input name="address" type="text" id="address" size="50" maxlength="50"></td>
    </tr>
    <tr> 
      <td align="right" class="border" >会议时间</td>
      <td class="border" > <input name="timestart" type="text" id="timestart" value="<?php echo date('Y-m-d H:i:s')?>" onFocus="JTC.setday(this)">
        至 
        <input name="timeend" type="text" id="timeend" value="<?php echo date('Y-m-d H:i:s')?>" onFocus="JTC.setday(this)"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border" >展会内容</td>
      <td class="border" > <textarea  name="content" id="content"></textarea>
	  	<script type="text/javascript">CKEDITOR.replace('content');	</script>
      </td>
    </tr>
    <tr> 
      <td align="right" class="border" >置顶值</td>
      <td class="border" ><input name="elite" type="text" id="elite" value="0" size="10" maxlength="3">
        (0-255之间的数字，数值大的排在前面) </td>
    </tr>
    <tr> 
      <td align="right" class="border" >&nbsp;</td>
      <td class="border" ><input type="submit" name="Submit" value="发 布" >
      <input name="action" type="hidden" id="action" value="add"></td>
    </tr>
  </table>
</form>  
<?php
}


function modify(){
//checkadminisdo("zh_modify");
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);
?>
<div class="admintitle">修改展会信息</div>
<?php
$sql="select * from zzcms_zh where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<form action="?do=save" method="post" name="myform"  id="myform" onSubmit="return CheckForm();"> 
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td align="right" class="border">所属类别</td>
      <td class="border"> 
	   <?php
		$sqln = "select classid,classname from zzcms_zhclass order by xuhao asc";
	    $rsn=query($sqln);
        $rown=num_rows($rsn);
		if (!$rown){
			echo "<a href='class.php?tablename=zzcms_zhclass'>请添加类别</a>";
		}else{
		?>
		<select name="bigclassid" id="bigclassid">
                <option value="" selected="selected">请选择类别</option>
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
      <td width="100" align="right" class="border">展会名称</td>
      <td class="border"> <input name="title" type="text" id="title22" value="<?php echo $row["title"]?>" size="50" maxlength="255"></td>
    </tr>
    <tr> 
      <td align="right" class="border">会议地址</td>
      <td class="border"><input name="address" type="text" id="address2" value="<?php echo $row["address"]?>" size="50"></td>
    </tr>
    <tr> 
      <td align="right" class="border">会议时间</td>
      <td class="border"> <input name="timestart" type="text" id="timestart" value="<?php echo $row["timestart"]?>" onFocus="JTC.setday(this)">
        至 
        <input name="timeend" type="text" id="timeend" value="<?php echo $row["timeend"]?>" onFocus="JTC.setday(this)"> 
      </td>
    </tr>
    <tr> 
      <td width="100" align="right" class="border">展会内容</td>
      <td class="border"><textarea name="content" id="content" ><?php echo stripfxg($row["content"])?></textarea> 
       	<script type="text/javascript">CKEDITOR.replace('content');	</script>
        <input name="id" type="hidden" id="id" value="<?php echo $row["id"]?>">
        <input name="page" type="hidden" id="page" value="<?php echo $page?>"></td>
    </tr>
    <tr> 
      <td align="right" class="border">审核</td>
      <td class="border"><input name="passed" type="checkbox" id="passed" value="1" <?php if ($row["passed"]==1){ echo "checked";}?>>
        （选中为通过审核）</td>
    </tr>
    <tr> 
      <td align="right" class="border">置顶值</td>
      <td class="border"> <input name="elite" type="text" id="url" value="<?php echo $row["elite"]?>" maxlength="3">
        (0-255之间的数字，数值大的排在前面) </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"><input name="Submit" type="submit" id="Submit" value="修 改" >
      <input name="action" type="hidden" id="action" value="modify"></td>
    </tr>
  </table>
</form> 
<?php
}
?>	  
</body>
</html>