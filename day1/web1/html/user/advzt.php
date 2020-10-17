<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language = "JavaScript" src="/js/gg.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.title.value==""){
alert('标题不能为空');
document.myform.title.focus();
return false;
} 

if (document.myform.link.value==""){
alert('连接地址不能为空');
document.myform.link.focus();
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
<div class="right">
<div class="content">
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
}


if ($do=="save"){

$page=isset($_POST["page"])?$_POST["page"]:1;
checkid($page);
checkstr($img,"upload");//入库前查上传文件地址是否合格
if ($_POST["action"]=="add"){
$isok=query("Insert into zzcms_ztad(classname,title,link,img,editor,passed) values('$classname','$title','$link','$img','$username',1)");  
$id=insert_id();		
}elseif ($_POST["action"]=="modify"){
$id=$_POST["id"];
$isok=query("update zzcms_ztad set classname='$classname',title='$title',link='$link',img='$img',editor='$username',passed=1 where id='$id'");	
}
passed("zzcms_ztad");	
?>


  <div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">标题：<?php echo $title?>
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="advzt_manage.php?classname=<?php echo $classname?>&page=<?php echo $page?>">[返回]</a></li>
	</div>
	</div>
	</div>

<?php
}

function add(){
?>
<div class="admintitle">发布展厅广告</div>	  
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr>
            <td width="25%" align="right" class="border">类别：</td>
            <td width="75%" class="border">
	<select name="classname" id="classname" class="biaodan">
     <option value="焦点图片广告">焦点图片广告</option>
     </select>            </td>
          </tr>
          <tr> 
            <td align="right" class="border">标题：</td>
			
            <td class="border">
			 <input name="title" type="text" id="title" size="50" maxlength="255" class="biaodan"></td>
          </tr>
          <tr>
            <td align="right" class="border">链接地址：</td>
            <td class="border"><input name="link" type="text" id="link2" size="50" class="biaodan"/></td>
          </tr>
          <tr>
            <td align="right" class="border">上传图片：
              <input name="img" type="hidden" id="img"></td>
            <td class="border">
                <table width="140" height="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
                  <tr align="center" bgcolor="#FFFFFF">
                    <td id="showimg" onClick="openwindow('/uploadimg_form.php?noshuiyin=1',400,300)"><input name="Submit2" type="button"  value="上传图片" /></td>
                  </tr>
              </table></td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="发布">
              <input name="action" type="hidden" value="add"></td>
          </tr>
        </table>
</form>

<?php
}

function modify(){
global $username;
?>
<div class="admintitle">修改展厅广告</div>
<?php
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id);

$sqlzx="select * from zzcms_ztad where id='$id'";
$rszx =query($sqlzx); 
$rowzx = fetch_array($rszx);
if ($id!=0 && $rowzx["editor"]<>$username) {
markit();
echo "<script>alert('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');history.back(-1);</script>";
exit;
}
?>	  
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr>
            <td width="25%" align="right" class="border">类别：</td>
            <td width="75%" class="border"><select name="classname" id="classname" class="biaodan">
                <option value="焦点图片广告">焦点图片广告</option>
              </select>            </td>
          </tr>
          <tr> 
            <td align="right" class="border">标题：</td>
			
            <td class="border">
			 <input name="title" type="text" id="title2" size="50" maxlength="255" class="biaodan" value="<?php echo $rowzx["title"]?>" ></td>
          </tr>
          <tr>
            <td align="right" class="border">链接地址：</td>
            <td class="border"><input name="link" type="text" id="link" class="biaodan" size="50" maxlength="255" value="<?php echo $rowzx["link"]?>"></td>
          </tr>
          <tr>
            <td align="right" class="border">上传图片：
              <input name="oldimg" type="hidden" id="oldimg" value="<?php echo $rowzx["img"]?>" />
                <input name="img" type="hidden" id="img" value="<?php echo $rowzx["img"]?>" /></td>
            <td class="border">
                <table width="140" height="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
                  <tr>
                    <td align="center" bgcolor="#FFFFFF" id="showimg" onClick="openwindow('/uploadimg_form.php?noshuiyin=1',400,300)"><?php
				 if ($rowzx["img"]<>""){
						if (substr($rowzx["img"],-3)=="swf"){
						$str=$str."<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0' width='120' height='120'>";
						$str=$str."<param name='wmode' value='transparent'>";
						$str=$str."<param name='movie' value='".$rowzx["img"]."' />";
						$str=$str."<param name='quality' value='high' />";
						$str=$str."<embed src='".$rowzx["img"]."' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='120'  height='120' wmode='transparent'></embed>";
						$str=$str."</object>";
						echo $str;
						}elseif (strpos("gif|jpg|png|bmp",substr($rowzx["img"],-3))!==false ){
                    	echo "<img src='".$rowzx["img"]."' width='120'  border='0'> ";
                    	}
					echo "点击可更换图片";	
					}else{
                     echo "<input name='Submit2' type='button'  value='上传图片'/>";
                    }	
				  ?>                    </td>
                  </tr>
                </table>              </td>
          </tr>
            <td align="right" class="border2">&nbsp;</td>
            <td class="border2"> <input name="Submit" type="submit" class="buttons" value="修改">
              <input name="id" type="hidden" id="id" value="<?php echo $rowzx["id"] ?>" />
              <input name="page" type="hidden" id="page" value="<?php echo $page?>" />
              <input name="action" type="hidden" id="action" value="modify" /></td>
          </tr>
        </table>
</form>
<?php
}
?>
</div>
</div>
<div class="left">
<?php
include("left.php");
?>
</div>
</div>
</div>
</body>
</html>