<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<?php
checkadminisdo("label");
$action = isset($_REQUEST['action'])?$_REQUEST['action']:"";
$ml = isset($_GET['ml'])?$_GET['ml']:"";
if ($ml==""){
$ml = isset($_POST['ml'])?$_POST['ml']:"";
}

if ($ml!=""){
$dirs='';
$dirskin = opendir("../skin");
while(($dir = readdir($dirskin))!=false){
	if ($dir!="." && $dir!="..") { //不读取. ..
		$dirs=$dirs.$dir."#";
	} 
}
closedir($dirskin);	
$dirs=substr($dirs,0,strlen($dirs)-1);//去除最后面的"#"
//echo $dirs;
if (str_is_inarr($dirs,$ml)=='no'){
showmsg($ml.'参数有误');
}

}

if ($action=="add") {
checkadminisdo("label_add");

$title=nostr($_POST["title"]);
$title_old=$_POST["title_old"];
if (strpos(strtolower($title),'php')!==false){
showmsg('只能是htm或css这两种格式,模板名称：后面加上.htm或.css');
}
$start=stripfxg($_POST["start"],true);//stripfxg如果有自动加反斜杠去反斜杠

if (strpos(strtolower($start),'<?')!==false || strpos(strtolower($start),'<%')!==false){
showmsg('有非法内容');
}

$fp="../skin/".$ml."/".$title;
$f=fopen($fp,"w+");//fopen()的其它开关请参看相关函数
$isok=fputs($f,$start);
fclose($f);
if ($isok){
$title==$title_old ?$msg='修改成功':$msg='添加成功';
}else{
$msg="失败";
}
echo "<script>alert('".$msg."');location.href='?ml=".$ml."&title=".$title."'</script>";
}

if ($action=="del"){ 
checkadminisdo("label_del");
$title=nostr($_POST["title"]);
if (strpos(strtolower($title),'php')!==false){
showmsg('只能是htm或css这两种格式,模板名称：后面加上.htm或.css');
}
$f="../skin/".$ml."/".$title;
	if (file_exists($f)){
	unlink($f)?$msg='成功删除':$msg='删除失败';
	}else{
	$msg='请选择要删除的文件';
	}
echo "<script>alert('".$msg."');location.href='?ml=".$ml."'</script>";	
}
?>
<script language = "JavaScript">
function ConfirmDel(){
   if(confirm("确定要删除吗？一旦删除将不能恢复！"))
     return true;
   else
     return false;	 
}
function CheckForm(){
//创建正则表达式
var re=/^[0-9a-zA-Z_.]{1,30}$/; //只输入数字和字母的正则
if (document.myform.title.value==""){
    alert("模板名称不能为空！");
	document.myform.title.focus();
	return false;
  }
if(document.myform.title.value.search(re)==-1)  {
    alert("模板名称只能用字母，数字，_ 。且长度小于20个字符！");
	document.myform.title.focus();
	return false;
  }
if (document.myform.start.value==""){
    alert("模板内容不能为空！");
	document.myform.start.focus();
	return false;
  }
}  
</script>
</head>
<body>

<div class="admintitle">模板管理</div>
<form action="" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="100" align="right" class="border" >模板名称：</td>
      <td class="border" ><div class="boxlink">
        <?php
$dirskin = opendir("../skin");
while(($dir = readdir($dirskin))!=false){
	if ($dir!="." && $dir!="..") { //不读取. ..
		if ($ml==$dir){
  		echo "<li><a href='?ml=".$dir."' style='color:#000000;background-color:#FFFFFF'>".$dir."</a></li>";
		}else{
		echo "<li><a href='?ml=".$dir."'>".$dir."</a></li>";
		}
	} 
}
closedir($dirskin);	
?>
      </div></td>
    </tr>
<?php 
if ($ml<>''){
?>	
    <tr>
      <td align="right" class="border" >模板文件：</td>
      <td class="border" ><div class="boxlink">
        <?php 
$title="";
$fcontent="";
if (isset($_GET['title'])){
$title=$_GET['title'];
if (strpos(strtolower($title),'php')!==false){
showmsg('只能是htm或css这两种格式');//防止直接输入php 文件地址显示PHP代码
}
}
	$dir2 = opendir("../skin/".$ml);
	while(($file = readdir($dir2))!=false){
  		if ($file!="." && $file!=".." && $file!='image') { //不读取. ..
			if ($title==$file){
  			echo "<li><a href='?ml=".$ml."&title=".$file."' style='color:#000000;background-color:#FFFFFF'>".$file."</a></li>";
			}else{
			echo "<li><a href='?ml=".$ml."&title=".$file."'>".$file."</a></li>";
			}
  		} 
	}
	closedir($dir2);	
	//读取现有标签中的内容
	if ($title!=''){
	$fp='../skin/'.$ml.'/'.$title;
	$f=fopen($fp,'r');
	$fcontent=fread($f,filesize($fp));
	fclose($f);
	} 
?>
      </div></td>
    </tr>
    <tr> 
      <td align="right" class="border" >模板文件名称：</td>
      <td class="border" ><input name="title" type="text" id="title" value="<?php echo $title?>" size="50" maxlength="255">
      <input name="ml" type="hidden" id="ml" value="<?php echo $ml?>" size="50" maxlength="255">
      <input name="title_old" type="hidden" id="title_old" value="<?php echo $title?>" size="50" maxlength="255"></td>
    </tr>
    <tr> 
      <td align="right" class="border" >模板文件内容：</td>
      <td class="border" ><textarea name="start" cols="150" rows="30" id="start" class="bigtextarea"><?php echo $fcontent?></textarea></td>
    </tr>
    <tr> 
      <td align="right" class="border" >&nbsp;</td>
      <td class="border" > <input type="submit" name="Submit" value="添加/修改" onClick="myform.action='?action=add'"> 
        <input type="submit" name="Submit2" value="删除选中" onClick="myform.action='?action=del';return ConfirmDel()"></td>
    </tr>
  <?php 
	  }	
	  ?>	
  </table>
</form>	
</body>
</html>