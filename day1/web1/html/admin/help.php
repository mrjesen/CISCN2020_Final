<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
<script language = "JavaScript">	
function CheckForm(){
if (document.myform.title.value==""){
    alert("标题不能为空！");
	document.myform.title.focus();
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
checkadminisdo("helps");
$id = isset($_POST['id'])?$_POST['id']:0;
checkid($id,1);
$page=isset($_POST["page"])?$_POST["page"]:1;//只从修改页传来的值
checkid($page);
$elite=isset($_POST["elite"])?$_POST["elite"]:0;
checkid($elite,1);
$b=isset($_POST["b"])?$_POST["b"]:0;
checkid($b,1);
$img=getimgincontent(stripfxg($content,true));
checkstr($img,"upload");//入库前查上传文件地址是否合格
if ($_REQUEST["action"]=="add"){
	query("INSERT INTO zzcms_help (classid,title,content,img,elite,sendtime)VALUES('$b','$title','$content','$img','$elite','".date('Y-m-d H:i:s')."')");
	}elseif ($_REQUEST["action"]=="modify"){
	query("update zzcms_help set classid='$b',title='$title',content='$content',img='$img',elite='$elite',sendtime='".date('Y-m-d H:i:s')."' where id='$id' ");
}
echo "<script>location.href='help_manage.php?b=".$b."&page=".$page."'</script>";
}


function add(){
$b=$_GET["b"];
if ($b<>"") {
checkid($b);
}
?>
<div class="admintitle">发布<?php if ($b==1) { echo "帮助"; }else { echo "公告";}?>信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">      
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="10%" align="right" class="border" >标题</td>
      <td width="90%" class="border" > <input name="title" type="text" id="title2" size="50" maxlength="255"></td>
    </tr>
    <tr id="trcontent"> 
      <td align="right" class="border" >内容</td>
      <td class="border" > <textarea name="content"  id="content"></textarea> 
        <script type="text/javascript">CKEDITOR.replace('content');	</script>      </td>
    </tr>
    <tr> 
      <td align="right" class="border" >首页显示</td>
      <td class="border" ><input name="elite" type="checkbox" id="elite" value="1" checked></td>
    </tr>
    <tr> 
      <td align="right" class="border" ><input name="b" type="hidden"  value="<?php echo $b?>">
      <input name="action" type="hidden"  value="add"></td>
      <td class="border" ><input type="submit" name="Submit" value="发 布" ></td>
    </tr>
  </table>
</form>  
<?php
}


function modify(){
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);

$id=$_GET["id"];
if ($id<>""){
checkid($id);
}
$b=$_GET["b"];
if ($b<>""){
checkid($b);
}
?>
<div class="admintitle">修改<?php if ($b==1) { echo "帮助"; }else { echo "公告";}?>信息</div>
<?php
$sql="select * from zzcms_help where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">  
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr> 
      <td width="10%" align="right" class="border">标题</td>
      <td width="90%" class="border"> <input name="title" type="text" id="title2" value="<?php echo $row["title"]?>" size="50" maxlength="255">      </td>
    </tr>
    <tr id="trcontent"> 
      <td align="right" class="border">内容</td>
      <td class="border"> <textarea name="content" id="content" ><?php echo stripfxg($row["content"])?></textarea> 
        <script type="text/javascript">CKEDITOR.replace('content');	</script>      </td>
    </tr>
    <tr> 
      <td align="right" class="border">首页显示</td>
      <td class="border"> <input name="elite" type="checkbox" id="elite" value="1" <?php if ($row["elite"]==1) { echo "checked";}?>>      </td>
    </tr>
    <tr> 
      <td align="right" class="border"><input name="b" type="hidden" id="b" value="<?php echo $row["classid"]?>"> 
        <input name="id" type="hidden" id="id" value="<?php echo $row["id"]?>"> 
        <input name="page" type="hidden" id="page" value="<?php echo $page?>"> 
        <input name="action" type="hidden" value="modify"></td>
      <td class="border"><input type="submit" name="Submit" value="提交"></td>
    </tr>
  </table>
</form>
<?php
}

?>	  	  
</body>
</html>