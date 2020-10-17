<?php
ob_start();//打开缓冲区，可以setcookie
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<?php
checkadminisdo("zskeyword");
?>
<script language="JavaScript" src="/js/gg.js"></script>
<script language="JavaScript" src="/js/jquery.js"></script>
<script language="JavaScript" type="text/JavaScript">
function ConfirmDelBig(){
   if(confirm("确定要删除此关键词吗？"))
     return true;
   else
     return false;	 
}
function CheckForm(){  
if (document.myform.tag.value==""){
    alert("关键词不能为空！");
	document.myform.tag.focus();
	return false;
  }
    if (document.myform.url.value==""){
    alert("链接地址不能为空！");
	document.myform.url.focus();
	return false;
  } 
}
</script>
</head>
<body>
<?php
if (isset($_GET['tablename'])){
setcookie("tablename",$_GET['tablename'],time()+3600*24,"/");
echo "<script>location.href='tag.php'</script>";
}
if ($_COOKIE['tablename']==''){
showmsg('tablename 无参数');
}

$tablenames='';
$rs = query("SHOW TABLES"); 
while($row = fetch_array($rs)) { 
$tablenames=$tablenames.$row[0]."#"; 
}
$tablenames=substr($tablenames,0,strlen($tablenames)-1);

if (str_is_inarr($tablenames,$_COOKIE['tablename'])=='no'){
showmsg('tablename 参数有误','index.php');//返回到首页避免造成死循环
}

if ($_COOKIE['tablename']=="zzcms_tagzs"){
$title=channelzs;
}elseif($_COOKIE['tablename']=="zzcms_tagzx"){
$title="资讯";
}

$dowhat=isset($_REQUEST['dowhat'])?$_REQUEST['dowhat']:'';
switch ($dowhat){
case "addtag";
addtag();
break;
case "modifytag";
modifytag();
break;
default;
showtag();
}
function showtag(){
global $title;
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';

if ($action=="px") {
$sql="select * from `".$_COOKIE['tablename']."`";
$rs=query($sql);
while ($row=fetch_array($rs)){
$xuhao=$_POST["xuhao".$row["id"].""];//表单名称是动态显示的，并于FORM里的名称相同。
	   if (trim($xuhao) == "" || is_numeric($xuhao) == false) {
	       $xuhao = 0;
	   }elseif ($xuhao < 0){
	       $xuhao = 0;
	   }else{
	       $xuhao = $xuhao;
	   }
query("update `".$_COOKIE['tablename']."` set xuhao='$xuhao' where id=".$row['id']."");
}
}

if ($action=="del"){
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$_POST['id'][$i];
	checkid($id);
	query("delete from ".$_COOKIE['tablename']." where id ='$id'");
	}
}else{
echo "<script>alert('操作失败！至少要选中一条信息。');history.back()</script>";
}
echo "<script>location.href='?'</script>";	
}
?>
<div class="admintitle"><?php echo $title?>关键词设置</div>
<div  class="border2 center"><input name="submit3" type="submit" class="buttons" onClick="javascript:location.href='?dowhat=addtag'" value="添加关键词"></div>
	<?php
	$sql="select * From `".$_COOKIE['tablename']."` order by xuhao asc";
	$rs=query($sql);
	$row=num_rows($rs);
	if (!$row){
	echo "暂无信息";
	}else{
?>
      <form name="myform" method="post" action="">
    <table width="100%" border="0" cellpadding="5" cellspacing="1" >
    <tr class="trtitle">
      <td width="3%" align="center"><label for="chkAll" style="cursor: pointer;">
      全选</label></td> 
      <td width="20%" height="25">关键词</td>
      <td width="20%">url</td>
      <td width="20%">排序</td>
      <td width="20%" height="25">操作选项</td>
    </tr>
    <?php
	while ($row=fetch_array($rs)){
?>
     <tr class="trcontent">
       <td align="center" class="docolor"><input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>"></td>  
      <td width="265" height="22"><?php echo $row["keyword"]?><a name="B<?php echo $row["id"]?>"></a></td>
      <td width="302"><?php echo $row["url"]?></td>
      <td width="237" height="22"><input name="<?php echo "xuhao".$row["id"]?>" type="text" id="<?php echo "xuhao".$row["id"]?>" value="<?php echo $row["xuhao"]?>" size="4" maxlength="4"> 
       <input type="submit" name="Submit" value="更新序号" onClick="myform.action='?action=px'"></td>
      <td class="docolor"> <a href="?dowhat=modifytag&id=<?php echo $row["id"]?>">修改</a></td>
    </tr>
    <?php
	}
	?>
  </table>
  <div class="border">
   
    <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
 <label for="chkAll" style="cursor: pointer;">全选</label>
    <input type="submit" onClick="myform.action='?action=del';myform.target='_self';return ConfirmDelBig()" value="删除选中的信息"></div>
	  </form>
<?php
}
}
function addtag(){
global $title;
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
if ($action=="add"){
for($i=0; $i<count($_POST['tag']);$i++){
	$tag=str_replace("{","",trim($_POST['tag'][$i]));
	$url=addhttp(str_replace("{","",trim($_POST['url'][$i])));
	if ($tag!=''){
	$sql="select * from ".$_COOKIE['tablename']." where keyword='" . $tag . "'";
	$rs=query($sql);
	$row=num_rows($rs);
		if (!$row) {//重复的不入库
		query("insert into `".$_COOKIE['tablename']."` (keyword,url)VALUES('$tag','$url') ");
		}
	}
}	
echo "<script>location.href='?'</script>";
}else{	
?>
<div class="admintitle">添加<?php echo $title?>关键词</div>
<script language="javascript">   
//动态增加表单元素。
 function AddElement(){   
//得到需要被添加的html元素。
var TemO=document.getElementById("add");   
//var newInput = document.createElement("<input type='text' size='50' maxlength='50' name='tag[]' value='关键词'>");
if($.browser.msie) {
	var newInput = document.createElement("<input type='text' size='50' maxlength='50' name='tag[]' value='关键词'>"); 
}else{
	var newInput = document.createElement("input");
	newInput.type = "text";
	newInput.name = "tag[]";
	newInput.size = "50";
	newInput.maxlength = "50";
	newInput.value = "关键词";
}
TemO.appendChild(newInput);

//var newInput = document.createElement("<input name='url[]' type='text' value='#' size='50' maxlength='255'>");
if($.browser.msie) {
	var newInput = document.createElement("<input type='text' size='50' maxlength='255' name='url[]' value='#'>"); 
}else{
	var newInput = document.createElement("input");
	newInput.type = "text";
	newInput.name = "url[]";
	newInput.size = "50";
	newInput.maxlength = "255";
	newInput.value = "#";
}
TemO.appendChild(newInput); 
var newline= document.createElement("hr"); 
TemO.appendChild(newline);  
}    
</script>

<form name="myform" method="post" action="?dowhat=addtag">
  <table width="100%" border="0" cellpadding="5" cellspacing="0" class="border">
    <tr> 
      <td width="25%" align="right">&nbsp;</td>
      <td width="75%"> 
	  <div id="add">
	  <input name="tag[]" type="text" value="关键词" size="50" maxlength="50">
	  url： 
      <input name="url[]" type="text" id="url" value="#" size="50" maxlength="255">
	  <br>
	  </div>	  </td>
    </tr>
    <tr> 
      <td align="right">&nbsp;</td>
      <td><img src="image/icobigx.gif"> <a href="javascript:void(0)" onClick='AddElement()'><img src='image/icobig.gif' border="0"> 添加</a></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td> <input name="action" type="hidden" id="action" value="add"> <input name="add" type="submit" value=" 提交 ">      </td>
    </tr>
  </table>
</form>
<?php
}
}

function modifytag(){
global $id,$title;;
checkid($id);
if ($id==""){
echo "<script>location.href='?'</script>";
}
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$tag=isset($_POST['tag'])?str_replace("{","",trim($_POST['tag'])):'';
$url=isset($_POST['url'])?addhttp(str_replace("{","",trim($_POST['url']))):'';

if ($action=="modify"){
	$sql="select * from `".$_COOKIE['tablename']."` where id='$id'";
	$rs=query($sql);
	$row=num_rows($rs);
	if (!$row){
		$FoundErr==1;
		$ErrMsg="<li>不存在！</li>";
		WriteErrMsg($ErrMsg);
	}else{
	query("update `".$_COOKIE['tablename']."` set keyword='$tag',url='$url' where id='$id'");
	}	
	echo "<script>location.href='?#B".$id."'</script>";
}else{
$sql="select * from `".$_COOKIE['tablename']."` where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle">修改<?php echo $title?>关键词</div>
<form name="myform" method="post" action="?dowhat=modifytag" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0" class="border">
    
    <tr> 
      <td width="25%" align="right">关键词：</td>
      <td width="75%"> <input name="tag" type="text" id="tag" value="<?php echo $row["keyword"]?>" size="50" maxlength="50"></td>
    </tr>
    <tr> 
      <td align="right">url：</td>
      <td><input name="url" type="text" id="url" value="<?php echo $row["url"]?>" size="50" maxlength="255"> 
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="id" type="hidden" id="id" value="<?php echo $row["id"]?>"> 
	  <input name="action" type="hidden" id="action" value="modify"> <input name="save" type="submit" id="save" value=" 修改 "> 
      </td>
    </tr>
  </table>
</form>
<?php
}
}
?>
</body>
</html>