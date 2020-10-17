<?php
ob_start();//打开缓冲区，可以setcookie
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/js/gg.js"></script>
<script language="JavaScript" src="/js/jquery.js"></script>
<script language="JavaScript" type="text/JavaScript">
function ConfirmDelBig(){
   if(confirm("确定要删除此类吗？"))
     return true;
   else
     return false;	 
}
</script>
</head>
<body>
<?php
if (isset($_GET['tablename'])){
setcookie("tablename",$_GET['tablename'],time()+3600*24,"/");
echo "<script>location.href='class.php'</script>";
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
if ($_COOKIE['tablename']=="zzcms_zhclass"){
$title="展会";
}elseif($_COOKIE['tablename']=="zzcms_linkclass"){
$title="友情链接";
}elseif($_COOKIE['tablename']=="zzcms_wangkanclass"){
$title="网刊";
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
global $title,$bigclassid;
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
if ($action=="px") {
$sql="select * from `".$_COOKIE['tablename']."`";
$rs=query($sql);
while ($row=fetch_array($rs)){
$xuhao=$_POST["xuhao".$row["classid"].""];//表单名称是动态显示的，并于FORM里的名称相同。
	   if (trim($xuhao) == "" || is_numeric($xuhao) == false) {
	       $xuhao = 0;
	   }elseif ($xuhao < 0){
	       $xuhao = 0;
	   }else{
	       $xuhao = $xuhao;
	   }
query("update `".$_COOKIE['tablename']."` set xuhao='$xuhao' where classid='".$row['classid']."'");
}
}
if ($action=="del"){
checkid($bigclassid);
checkadminisdo("siteconfig");
if ($bigclassid<>""){
	$sql="delete from `".$_COOKIE['tablename']."` where classid='" .$bigclassid. "' ";
	query($sql);
}    
echo "<script>location.href='?'</script>";
}
?>
<div class="admintitle"><?php echo $title ?>类别管理</div> 
<div class="center border2"><input type="submit" class="buttons" onClick="javascript:location.href='?dowhat=addtag'" value="添加"></div>
	<?php
	$sql="select * from `".$_COOKIE['tablename']."` order by xuhao asc";
	$rs=query($sql);
	$row=num_rows($rs);
	if (!$row){
	echo "暂无信息";
	}else{
?>
      <form name="form1" method="post" action="?action=px">
        
  <table width="100%" border="0" cellpadding="5" cellspacing="1" >
    <tr class="trtitle"> 
      <td width="20%">ID</td>
      <td width="20%">类别</td>
      <td width="20%">排序</td>
      <td width="20%">操作选项</td>
    </tr>
    <?php
	while ($row=fetch_array($rs)){
?>
     <tr class="trcontent">  
      <td><?php echo $row["classid"]?><a name="B<?php echo $row["classid"]?>"></a></td>
      <td><?php echo $row["classname"]?></td>
      <td><input name="<?php echo "xuhao".$row["classid"]?>" type="text" id="<?php echo "xuhao".$row["classid"]?>" value="<?php echo $row["xuhao"]?>" size="4" maxlength="4"> 
       <input type="submit" name="Submit" value="更新序号"></td>
      <td class="docolor"> <a href="?dowhat=modifytag&bigclassid=<?php echo $row["classid"]?>">修改名称</a> 
        | <a href="?action=del&bigclassid=<?php echo $row["classid"]?>" onClick="return ConfirmDelBig();">删除</a></td>
    </tr>
    <?php
	}
	?>
  </table>
	  </form>
<?php
}
}

function addtag(){
global $title;
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
if ($action=="add"){
    for($i=0; $i<count($_POST['bigclassname']);$i++){
    $bigclassname=($_POST['bigclassname'][$i]);
		if ($bigclassname!=''){
		$sql="select * from `".$_COOKIE['tablename']."` where classname='" . $bigclassname . "'";
		$rs=query($sql);
		$row=num_rows($rs);
			if (!$row) {
			query("insert into `".$_COOKIE['tablename']."` (classname)VALUES('$bigclassname') ");
			}
		}
	}	
    echo "<script>location.href='?'</script>";		
}else{	
?>
<div class="admintitle">添加<?php echo $title ?>类别</div>
<form name="form1" method="post" action="?dowhat=addtag">
  <table width="100%" border="0" cellpadding="5" cellspacing="0" class="border">
    <tr> 
      <td width="70%">
<script language="javascript">   
//动态增加表单元素。
function AddElement(){   
//得到需要被添加的html元素。
var TemO=document.getElementById("add");   
//var newInput = document.createElement("<input type='text' size='50'maxlength='50' name='bigclassname[]' id='bigclassname[]' value='类别名称'>");
if($.browser.msie) {
	var newInput = document.createElement("<input type='text' size='50' maxlength='50' name='bigclassname[]' id='bigclassname[]' value='类别名称'>");
	}else{
	var newInput = document.createElement("input");
	newInput.type = "text";
	newInput.name = "bigclassname[]";
	newInput.id = "bigclassname[]";
	newInput.size = "50";
	newInput.maxlength = "50";
	newInput.value = "类别名称";
	}
TemO.appendChild(newInput);     
var newline= document.createElement("hr"); 
TemO.appendChild(newline);   
}   
</script>
	<div id="add">
	  <input name="bigclassname[]" type="text" id="bigclassname[]" value="类别名称" size="50" maxlength="50">
	  <hr>
	  </div>	  </td>
    </tr>
    <tr> 
      <td> <img src="image/icobigx.gif" width="23" height="11">
        <a href="javascript:void(0)" onClick='AddElement()'><img src='image/icobig.gif' border="0"> 添加新类别</a>
        <input name="add" type="submit" value="提交">
        <input name="action" type="hidden" id="action" value="add">
        </td>
    </tr>
  </table>
</form>
<?php
}
}

function modifytag(){
global $title,$bigclassid;
$action = isset($_REQUEST['action']) ? $_REQUEST['action']:''; 
checkid($bigclassid);
$bigclassname = isset($_POST['bigclassname']) ? $_POST['bigclassname']:''; 
$oldbigclassname = isset($_POST['oldbigclassname'])?$_POST['oldbigclassname']:''; 

if ($action=="modify"){
	$sql="select * from `".$_COOKIE['tablename']."` where classid='" . $bigclassid."'";
	$rs=query($sql);
	$row=num_rows($rs);
	if (!$row){
		$FoundErr==1;
		$ErrMsg="<li>不存在！</li>";
		WriteErrMsg($ErrMsg);
	}else{
	query("update ".$_COOKIE['tablename']." set classname='$bigclassname' where classid='". $bigclassid."' ");
	if ($_COOKIE['tablename']=='zzcms_adclass' && $bigclassname!=$oldbigclassname){
	query("update zzcms_ad set bigclassname='$bigclassname' where bigclassname='$oldbigclassname' ");
	}
	
	}	
	echo "<script>location.href='?#B".$bigclassid."'</script>";
}else{
$sql="select * from ".$_COOKIE['tablename']." where classid='".$bigclassid."'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle">修改<?php echo $title ?>类别</div>
<form name="form1" method="post" action="?dowhat=modifytag" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0" class="border">
    <tr> 
      <td width="30%" align="right">类别名称：</td>
      <td width="70%"> <input name="bigclassname" type="text" id="bigclassname" value="<?php echo $row["classname"]?>" size="50" maxlength="50">
      <input name="oldbigclassname" type="hidden" id="oldbigclassname" value="<?php echo $row["classname"]?>" size="50" maxlength="50"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td><input name="bigclassid" type="hidden" id="bigclassid" value="<?php echo $row["classid"]?>"> 
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