<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>发布网刊信息</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
if (str_is_inarr(usergr_power,'wangkan')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限');
}
?>
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
<script language = "JavaScript">
function CheckForm(){
/*if (document.myform.bigclassid.value==""){
    alert("请选择网刊类别！");
	document.myform.bigclassid.focus();
	return false;
  }	*/  
if (document.myform.title.value==""){
    alert("网刊名称不能为空！");
	document.myform.title.focus();
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

$img=getimgincontent(stripfxg($content,true));
checkstr($img,"upload");//入库前查上传文件地址是否合格
if ($_POST["action"]=="add" && $username<>''){//$username<>''防垃圾信息
$isok=query("Insert into zzcms_wangkan(bigclassid,title,content,img,editor,sendtime) values('$bigclassid','$title','$content','$img','$username','".date('Y-m-d H:i:s')."')") ;  
$id=insert_id();	
}elseif ($_POST["action"]=="modify"){
$isok=query("update zzcms_wangkan set bigclassid='$bigclassid',title='$title',content='$content',img='$img',
editor='$username',sendtime='".date('Y-m-d H:i:s')."',passed=0 where id='$id'");
}		
passed("zzcms_wangkan");
?>

<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	标题：<?php echo $title?><br>
	
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="wangkanmanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("wangkan",$id)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php
}

function add(){
$tablename="zzcms_wangkan";//checkaddinfo中用
include("checkaddinfo.php");
?>
<div class="admintitle">发布网刊信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">       
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="15%" align="right" class="border2">所属类别<font color="#FF0000">（必填）</font>： </td>
            <td width="85%" class="border2"> <select name="bigclassid" id="bigclassid" class="biaodan">
                <option value="" selected="selected">请选择类别</option>
                <?php  
		$sql="select classid,classname from zzcms_wangkanclass";
		$rs=query($sql);
		while($row= fetch_array($rs)){
			?>
                <option value="<?php echo $row["classid"]?>" ><?php echo $row["classname"]?></option>
                <?php
		  }
		  ?>
            </select></td>
          </tr>
          <tr> 
            <td align="right" class="border">名称<font color="#FF0000">（必填）</font>： </td>
            <td class="border"> <input name="title" type="text" id="title" size="50" maxlength="255" class="biaodan"> 
            </td>
          </tr>
        
          <tr> 
            <td align="right" class="border2" >内容：</td>
            <td class="border2" > <textarea    name="content" id="content"></textarea> 
              <script type="text/javascript">
				CKEDITOR.replace('content');	
			</script></td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="发布"> 
              <input name="action" type="hidden"  value="add"></td>
          </tr>
        </table>
</form>
<?php
}

function modify(){
global $username;
?>
<div class="admintitle">修改网刊信息</div>
<?php
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

$sql="select * from zzcms_wangkan where id='$id'";
$rs =query($sql); 
$row = fetch_array($rs);
if ($id!=0 && $row["editor"]<>$username) {
markit();
showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">    
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="15%" align="right" class="border2">所属类别<font color="#FF0000">（必填）</font>：</td>
            <td width="85%" class="border2"> <select name="bigclassid" id="bigclassid" class="biaodan">
                <option value="" selected="selected">请选择类别</option>
        <?php
		$rsn=query("select * from zzcms_wangkanclass");
		while($rown= fetch_array($rsn)){
		?>
      <option value="<?php echo $rown["classid"]?>"  <?php if ($rown["classid"]==$row["bigclassid"]) { echo "selected";}?> ><?php echo $rown["classname"]?></option>
          <?php
		  }
		  ?>
              </select></td>
          </tr>
          <tr> 
            <td align="right" class="border">名称<font color="#FF0000">（必填）</font>：</td>
            <td class="border"> <input name="title" type="text" id="title" class="biaodan" size="50" maxlength="255" value="<?php echo $row["title"]?>" />            </td>
          </tr>
          <tr> 
            <td align="right" class="border2" >内容：</td>
            <td class="border2" > <textarea    name="content" id="content" class="biaodan" style="height:auto"><?php echo stripfxg($row["content"])?></textarea> 
              <script type="text/javascript">
				CKEDITOR.replace('content');	
			</script>            </td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="修改"> 
              <input name="action" type="hidden" id="action3" value="modify">
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