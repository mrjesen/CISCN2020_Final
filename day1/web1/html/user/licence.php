<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language = "JavaScript" src="../js/gg.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.title.value==""){
    alert("证件名称不能为空！");
	document.myform.title.focus();
	return false;
  }
  if (document.myform.img.value==""){
    alert("请上传证件图片！");
	return false;
  }
}
</SCRIPT>
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
if( isset($_GET["page"]) && $_GET["page"]!="") {$page=$_GET['page'];}else{$page=1;}
checkid($page,0);
checkstr($img,"upload");//入库前查上传文件地址是否合格

if ($_POST["action"]=="add"){
$isok=query("insert into zzcms_licence(title,img,editor,sendtime) values('$title','$img','$username','".date('Y-m-d H:i:s')."')") ; 

}elseif ($_POST["action"]=="modify"){

$oldimg=trim($_POST["oldimg"]);
checkstr($oldimg,"upload");
	$id=$_POST["id"];
	if ($id=="" || is_numeric($id)==false){
		$FoundErr=1;
		$ErrMsg="<li>参数不足！</li>";
		WriteErrMsg($ErrMsg);
	}else{
	$isok=query("update zzcms_licence set title='$title',img='$img',sendtime='".date('Y-m-d H:i:s')."',passed=0 where id='$id'");
		if ($oldimg<>$img && $oldimg<>"/image/nopic.gif"){
			$f="../".$oldimg;
			if (file_exists($f)){
			unlink($f);
			}
			$fs="../".str_replace(".","_small.",$oldimg)."";
			if (file_exists($fs)){
			unlink($fs);		
			}
		}		
	}
}
passed("zzcms_licence");
?>
<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	文件名：：<?php echo $title?><br>
	
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="licence_manage.php?page=<?php echo $page?>">[返回]</a></li>
	</div>
	</div>
	</div>

<?php
}


function add(){
$tablename="zzcms_licence";
include("checkaddinfo.php");
?>
<div class="admintitle">添加资质证书</div>
<FORM name="myform" action="?do=save" method="post" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="3" cellspacing="1">
    <tr> 
            <td width="15%" align="right" class="border">上传资质证书：
              <input name="img" type="hidden" id="img" value="">
       </td>
            <td width="85%" height="30" class="border"> 
	  <table width="120" height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
          <tr align="center" bgcolor="#FFFFFF"> 
            <td id="showimg" onClick="openwindow('/uploadimg_form.php',400,300)"> <input name="Submit2" type="button"  value="上传图片" /></td>
          </tr>
        </table>

	  </td>
    </tr>
    <tr> 
      <td align="right" class="border2">资质证书名称：</td>
      <td height="30" class="border2">
<input name="title" type="text" id="title" class="biaodan"> </td>
    </tr>
    <tr> 
      <td class="border">&nbsp;</td>
      <td height="30" class="border"><input name=Submit   type=submit class="buttons" id="Submit" value="保存 ">
        <input name="action" type="hidden" id="action" value="add"></td>
    </tr>
  </table>
	
  </form>
 <?php
 }
 
function modify(){
global $username;
?> 
<div class="admintitle">修改资质证书 </div>
<?php
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id);

$sql="select * from zzcms_licence where id='$id'";
$rs = query($sql); 
$row = fetch_array($rs);
if ($id!=0 && $row["editor"]<>$username) {
markit();
showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>
<FORM name="myform" action="?do=save" method="post" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="3" cellspacing="1">
    <tr> 
      <td width="15%" height="50" align="right" class="border">上传资质证书： <br>
       
        <input name="img" type="hidden" id="img" value="<?php echo $row["img"]?>">
        <input name="oldimg" type="hidden" id="oldimg" value="<?php echo $row["img"]?>">
        <input name="id" type="hidden" id="id" value="<?php echo $row["id"]?>">
      </td>
      <td width="85%" height="50" class="border"> 
              <table width="120" height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
                <tr> 
                  <td align="center" bgcolor="#FFFFFF" id="showimg" onClick="openwindow('/uploadimg_form.php',400,300)"> 
                    <?php
				  if($row["img"]<>""){
				  echo "<img src='".$row["img"]."' border=0 width=120 /><br>点击可更换图片";
				  }else{
				  echo "<input name='Submit2' type='button'  value='上传图片'/>";
				  }
				  ?>
                  </td>
                </tr>
              </table>
	        </td>
    </tr>
    <tr> 
      <td align="right" class="border2">资质证书名称：</td>
      <td height="30" class="border2">
<input name="title" type="text" id="title" value="<?php echo $row["title"]?>" class="biaodan"> </td>
    </tr>
    <tr> 
      <td class="border">&nbsp;</td>
      <td height="30" class="border"><input name=Submit   type=submit class="buttons" id="Submit" value="保存修改结果">
        <input name="action" type="hidden" id="action" value="modify"></td>
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