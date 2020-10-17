<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>发布展会信息</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
if (str_is_inarr(usergr_power,'zh')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限');
}
?>
<script language="javascript" src="../js/timer.js"></script>
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
<script language = "JavaScript">
function CheckForm(){
/*
if (document.myform.bigclassid.value==""){
    alert("请选择展会类型！");
	document.myform.bigclassid.focus();
	return false;
  }
*/	  
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
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
}

if ($do=="save"){
$page = isset($_POST['page'])?$_POST['page']:1;//返回列表页用
checkid($page);
$id = isset($_POST['id'])?$_POST['id']:0;
checkid($id);

if ($_POST["action"]=="add" && $username<>''){//$username<>''防垃圾信息
$isok=query("Insert into zzcms_zh(bigclassid,title,address,timestart,timeend,content,editor,sendtime) values('$bigclassid','$title','$address','$timestart','$timeend','$content','$username','".date('Y-m-d H:i:s')."')") ;  
$id=insert_id();
		
}elseif ($_POST["action"]=="modify"){
$isok=query("update zzcms_zh set bigclassid='$bigclassid',title='$title',address='$address',timestart='$timestart',timeend='$timeend',content='$content',
editor='$username',sendtime='".date('Y-m-d H:i:s')."' where id='$id'");
}		
passed("zzcms_zh");
?>

<div class="boxsave"> 
    <div class="title">
	<?php
	if ($_REQUEST["action"]=="add") {echo '添加';}else{echo '修改';}
	if ($isok) {  echo '成功';}else{echo '失败';}
     ?>
	</div>
	<div class="content_a">
	标题：<?php echo $title?><br/>
	地点：<?php echo  $address?><br/>
	时间：<?php echo $timestart?>-<?php echo $timeend?>
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="zhmanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("zh",$id)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php
}

function add(){
$tablename="zzcms_zh";//checkaddinfo中用
include("checkaddinfo.php");
?>
<div class="admintitle">发布展会信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
              
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="15%" align="right" class="border2">所属类别<font color="#FF0000">（必填）</font>： </td>
            <td width="85%" class="border2"> <select name="bigclassid" id="bigclassid" class="biaodan">
                <option value="" selected="selected">请选择类别</option>
                <?php  
		$rs=query("select classid,classname from zzcms_zhclass");
		while($row= fetch_array($rs)){
			?>
                <option value="<?php echo $row["classid"]?>" ><?php echo $row["classname"]?></option>
                <?php
		  }
		  ?>
            </select></td>
          </tr>
          <tr> 
            <td align="right" class="border">展会名称<font color="#FF0000">（必填）</font>： </td>
            <td class="border"> <input name="title" type="text" id="title" size="50" maxlength="255" class="biaodan"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="border2" >会议地址<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <input name="address" type="text" id="address" size="50" maxlength="255" class="biaodan"/></td>
          </tr>
          <tr> 
            <td align="right" class="border" >会议时间<font color="#FF0000">（必填）</font>：</td>
            <td class="border" > <input name="timestart" type="text" id="timestart" class="biaodan" value="<?php echo date('Y-m-d')?>" onfocus="JTC.setday(this)" />
              至 
              <input name="timeend" type="text" id="timeend" class="biaodan" value="<?php echo date('Y-m-d')?>" onfocus="JTC.setday(this)" /> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="border2" >展会内容：</td>
            <td class="border2" > <textarea    name="content" id="content"></textarea> 
              <script type="text/javascript">
				CKEDITOR.replace('content');	
			</script></td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="发 布"> 
              <input name="action" type="hidden" id="action3" value="add"></td>
          </tr>
        </table>
</form>
<?php
}

function modify(){
global $username;
?>
<div class="admintitle">修改展会信息</div>
<?php
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id);

$sql="select * from zzcms_zh where id='$id'";
$rs =query($sql); 
$row = fetch_array($rs);
if ($id!=0 && $row["editor"]<>$username) {
markit();showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">    
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="15%" align="right" class="border2">所属类别<font color="#FF0000">（必填）</font>： </td>
            <td width="85%" class="border2"> <select name="bigclassid" id="bigclassid" class="biaodan">
                <option value="" selected="selected">请选择类别</option>
                <?php
		$sqln="select classid,classname from zzcms_zhclass";
		$rsn=query($sqln);
		while($rown= fetch_array($rsn)){
		?>
      <option value="<?php echo $rown["classid"]?>"  <?php if ($rown["classid"]==$row["bigclassid"]) { echo "selected";}?> ><?php echo $rown["classname"]?></option>
          <?php
		  }
		  ?>
              </select></td>
          </tr>
          <tr> 
            <td align="right" class="border">展会名称<font color="#FF0000">（必填）</font>：</td>
            <td class="border"> <input name="title" type="text" id="title" class="biaodan" size="50" maxlength="255" value="<?php echo $row["title"]?>" /> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="border2" >会议地址<font color="#FF0000">（必填）</font>： </td>
            <td class="border2" > <input name="address" type="text" id="address" class="biaodan" size="50" maxlength="255" value="<?php echo $row["address"]?>"/></td>
          </tr>
          <tr> 
            <td align="right" class="border" >会议时间<font color="#FF0000">（必填）</font>：</td>
            <td class="border" > 
<input name="timestart" type="text" id="timestart" class="biaodan" value="<?php echo date("Y-m-d",strtotime($row['timestart']))?>" onfocus="JTC.setday(this)" />
-
<input name="timeend" type="text" id="timeend"  class="biaodan" value="<?php echo date("Y-m-d",strtotime($row['timeend']))?>" onfocus="JTC.setday(this)" /> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="border2" >展会内容：</td>
            <td class="border2" > <textarea    name="content" id="content" class="biaodan" style="height:auto"><?php echo stripfxg($row["content"])?></textarea> 
              <script type="text/javascript">
				CKEDITOR.replace('content');	
			</script>
            </td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="修改"> 
              <input name="action" type="hidden"  value="modify">
              <input name="page" type="hidden" id="page" value="<?php echo $page?>" />
              <input name="id" type="hidden" id="id" value="<?php echo $row["id"] ?>" /></td>
          </tr>
        </table>
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