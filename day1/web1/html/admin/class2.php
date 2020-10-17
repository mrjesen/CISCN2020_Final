<?php
ob_start();//打开缓冲区，可以setcookie
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<title></title>
<script language="JavaScript" src="../js/gg.js"></script>
<script language="JavaScript" src="../js/jquery.js"></script>
<script language="JavaScript" type="text/JavaScript">
function ConfirmDelBig(){
   if(confirm("确定要删除此大类吗？删除此大类同时将删除所包含的小类，并且不能恢复！"))
     return true;
   else
     return false;	 
}
function ConfirmDelSmall(){
   if(confirm("确定要删除此小类吗？一旦删除将不能恢复！"))
     return true;
   else
     return false;	 
}
function CheckForm(){  
if (document.form1.classname.value==""){
    alert("名称不能为空！");
	document.form1.classname.focus();
	return false;
}
}
</script>
</head>
<body>
<?php
if (isset($_GET['tablename'])){
setcookie("tablename",$_GET['tablename'],time()+3600*24,"/");
echo "<script>location.href='?'</script>";
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
if ($_COOKIE['tablename']=="zzcms_zxclass"){
$TitleClass="资讯";$TemplateFileName='zx';
}elseif($_COOKIE['tablename']=="zzcms_zsclass"){
$TitleClass=channelzs."/".channeldl;$TemplateFileName='zs';
}elseif($_COOKIE['tablename']=="zzcms_askclass"){
$TitleClass="问答";$TemplateFileName='ask';
}elseif($_COOKIE['tablename']=="zzcms_specialclass"){
$TitleClass="专题";$TemplateFileName='special';
}elseif($_COOKIE['tablename']=="zzcms_jobclass"){
$TitleClass="招聘";$TemplateFileName='job';
}elseif($_COOKIE['tablename']=="zzcms_userclass"){
$TitleClass="企业";$TemplateFileName='user';
}


$dowhat=isset($_REQUEST['dowhat'])?$_REQUEST['dowhat']:'';
switch ($dowhat){
case "addbigclass";
checkadminisdo("zxclass");
addbigclass();
break;
case "addsmallclass";
checkadminisdo("zxclass");
addsmallclass();
break;
case "modifybigclass";
checkadminisdo("zxclass");
modifybigclass();
break;
case "modifysmallclass";
checkadminisdo("zxclass");
modifysmallclass();
break;
default;
showclass();
}

function showclass(){
global $TitleClass;
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
if ($action=="px") {
checkadminisdo("zxclass");
$sql="select * from `".$_COOKIE['tablename']."` where parentid=0";
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

$sqln="select * from `".$_COOKIE['tablename']."` where parentid=".$row["classid"]."";
$rsn=query($sqln);
while ($rown=fetch_array($rsn)){
$xuhaos=$_POST["xuhaos".$rown["classid"].""];//表单名称是动态显示的，并于FORM里的名称相同。
	   if (trim($xuhaos) == "" || is_numeric($xuhaos) == false) {
	       $xuhaos = 0;
	   }elseif ($xuhaos < 0){
	       $xuhaos = 0;
	   }else{
	       $xuhaos = $xuhaos;
	   }
query("update `".$_COOKIE['tablename']."` set xuhao='$xuhaos' where classid='".$rown['classid']."'");
}
}
}

if ($action=="delbig") {
checkadminisdo("zxclass");
$bigclassid=trim($_GET["bigclassid"]);
checkid($bigclassid);
if ($bigclassid<>"") {
	query("delete from `".$_COOKIE['tablename']."` where parentid='" . $bigclassid. "'");
	query("delete from `".$_COOKIE['tablename']."` where classid='" . $bigclassid. "'");
}     
echo "<script>location.href='?'</script>";
}

if ($action=="delsmall") {
checkadminisdo("zxclass");
$smallclassid=trim($_GET["smallclassid"]);
checkid($smallclassid);
if ($smallclassid<>"") {
	query("delete from `".$_COOKIE['tablename']."` where classid='" . $smallclassid. "'");
}
//      
echo "<script>location.href='?#B".$_GET["bigclassid"]."'</script>";
}
?>
<div class="admintitle"><?php echo $TitleClass?>类别设置</div>
<div class="border2">
<input type="submit" class="buttons" onClick="javascript:location.href='?dowhat=addbigclass'" value="添加大类">
</div>
<?php
$sql="select * from `".$_COOKIE['tablename']."` where parentid=0 order by xuhao";
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "暂无分类信息";
}else{
?>
<form name="form1" method="post" action="?action=px">
  <table width="100%" border="0" align="center" cellpadding="5" cellspacing="1">
    <tr class="trtitle"> 
      <td width="15%" >类别名称</td>
      <td width="10%"  >ID</td>
      <td width="10%"  >拼音</td>
      <td width="10%" align="center" >类别属性 </td>
      <td width="20%" align="center" >所用模板文件</td>
      <td width="15%"  >排序</td>
      <td width="20%"  >操作</td>
    </tr>
    <?php while ($row=fetch_array($rs)){?>
    <tr class="bgcolor1"> 
      <td style="font-weight:bold"><a name="B<?php echo $row["classid"]?>"></a><img src="image/icobig.gif" width="9" height="9"> 
        <?php echo $row["classname"]?></td>
      <td style="font-weight:bold"><?php echo $row["classid"]?></td>
      <td style="font-weight:bold"><?php echo $row["classzm"]?></td>
      <td align="center">是否显示 [ 
		<?php if ($row["isshow"]==1) { echo "显示";} else{ echo "<font color=red>不显</font>";}?> ] </td>
      <td >
	  <?php
	   $skin=explode("|",$row["skin"]);
	  ?>
	 <a title="分类页" href="/template/<?php echo siteskin?>/<?php echo $skin[0]?>" target="_blank"><?php echo $skin[0]?></a> 
	 | <a title="列表页" href="/template/<?php echo siteskin?>/<?php echo @$skin[1]?>" target="_blank"><?php echo @$skin[1]?></a>	  </td>
      <td > <input name="<?php echo "xuhao".$row["classid"]?>" type="text"  value="<?php echo $row["xuhao"]?>" size="4"> 
      <input type="submit" name="Submit" value="更新序号"></td>
      <td >[ <a href="?dowhat=modifybigclass&classid=<?php echo $row["classid"]?>">修改</a> 
        | <a href="?action=delbig&bigclassid=<?php echo $row["classid"]?>" onClick="return ConfirmDelBig();">删除</a> 
        | <a href="?dowhat=addsmallclass&bigclassid=<?php echo $row["classid"]?>">添加子栏目</a> 
        ] </td>
    </tr>
    <?php
	$n=0;
	$sqln="select * from `".$_COOKIE['tablename']."` where parentid='" . $row["classid"] . "' order by xuhao";
	$rsn=query($sqln);
	while ($rown=fetch_array($rsn)){
	?>
    <tr class="trcontent">  
      <td ><a name="S<?php echo $rown["classid"]?>"></a><img src="image/icosmall.gif" width="23" height="11"> 
        <?php echo $rown["classname"]?></td>
      <td ><?php echo $rown["classid"]?></td>
      <td ><?php echo $rown["classzm"]?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input name="<?php echo "xuhaos".$rown["classid"]?>" type="text"  value="<?php echo $rown["xuhao"]?>" size="4"> 
        <input name="checked" type="submit" id="checked" value="更新序号"></td>
      <td>[ <a href="?dowhat=modifysmallclass&classid=<?php echo $rown["classid"]?>">修改</a> 
        | <a href="?action=delsmall&smallclassid=<?php echo $rown["classid"]?>&bigclassid=<?php echo $row["classid"]?>" onClick="return ConfirmDelSmall();">删除</a> 
        ] </td>
    </tr>
    <?php
		$n=$n+1;
		}
	}
	  ?>
  </table>
</form>
<?php
}
}

function addbigclass(){
global $TitleClass;
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$FoundErr=0;
$ErrMsg="";
if ($action=="add"){
for($i=0; $i<count($_POST['classname']);$i++){
	$classname=($_POST['classname'][$i]);
	$classzm=pinyin($classname);
	$isshow=isset($_POST['isshow'])?$_POST['isshow'][$i]:0;
	if ($classname!=''){
	$sql="select * from `".$_COOKIE['tablename']."` where classname='" . $classname . "'";
	$rs=query($sql);
	$row=num_rows($rs);
		if (!$row) {
		query("insert into `".$_COOKIE['tablename']."` (classname,classzm,parentid,isshow)values('$classname','$classzm',0,'$isshow')");	
		$bcid=insert_id();
		}
	}
}	
echo "<script>location.href='?#B".$bcid."'</script>";	
}
if ($FoundErr==1){
WriteErrMsg($ErrMsg);
}else{
?>
<div class="admintitle">添加<?php echo $TitleClass?>大类</div>
<form name="form1" method="post" action="?dowhat=addbigclass">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="100%" class="border">
	  	  <script language="javascript">   
//动态增加表单元素。
function AddElement(){   
//得到需要被添加的html元素。
var TemO=document.getElementById("add");   
	if($.browser.msie) {
	var newInput = document.createElement("<input type='text' size='50' maxlength='50' name='classname[]' value='大类别名称'>");
	TemO.appendChild(newInput);
	var newInput = document.createElement("<input name='isshow[]' type='checkbox' value='1' title='是否在前台显示该类别' checked>");
	TemO.appendChild(newInput);    
	}else{
	var newInput = document.createElement("input");
	newInput.type = "text";
	newInput.name = "classname[]";
	newInput.size = "50";
	newInput.maxlength = "50";
	newInput.value = "大类别名称";
	TemO.appendChild(newInput);
	
	var newInput = document.createElement("input");
	newInput.type = "checkbox";
	newInput.name = "isshow[]";
	newInput.title = "是否在前台显示该类别";
	newInput.value = "1";
	newInput.checked =true;
	TemO.appendChild(newInput);
	} 
var newline= document.createElement("hr"); 
TemO.appendChild(newline);   
}   
</script>
<div id="add">
	  <input name="classname[]" type="text" id="classname[]" size="50" maxlength="50"  value='大类别名称' >
	  <label><input name="isshow[]" type="checkbox" value="1" checked>
	  是否在前台显示该类别</label><hr/>
	  </div>	  
	   <img src="image/icobigx.gif" width="23" height="11"> <a href="javascript:void(0)" onClick='AddElement()'><img src='image/icobig.gif' border="0"> 添加新类别</a>
	  <input name="action" type="hidden" id="action" value="add"> 
        <input name="add" type="submit" value="提交">
	  </td>
    </tr>
  </table>
</form>
<?php
}
}

function addsmallclass(){
global $bigclassid,$TitleClass;
checkid($bigclassid);
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$FoundErr=0;
$ErrMsg="";

if ($action=="add") {
    for($i=0; $i<count($_POST['classname']);$i++){
    $classname=($_POST['classname'][$i]);
		if ($classname!=''){
		$sql="select * from `".$_COOKIE['tablename']."` where parentid='" . $bigclassid . "' and classname='" . $classname . "'";
		$rs=query($sql);
		$row=num_rows($rs);
			if (!$row) {
			query("insert into `".$_COOKIE['tablename']."` (parentid,classname)values('$bigclassid','$classname')");
			}
		}
	}	
    echo "<script>location.href='?#B".$bigclassid."'</script>";	
}
if ($FoundErr==1){
WriteErrMsg($ErrMsg);
}else{
?>

<div class="admintitle">添加<?php echo $TitleClass?>小类</div>
<form name="form" method="post" action="?dowhat=addsmallclass" >
  <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="20%" align="right" class="border">所属大类：</td>
      <td width="80%" class="border"> 
        <?php
		$sqlb = "select classid,classname from `".$_COOKIE['tablename']."` where parentid=0";
	    $rsb=query($sqlb);
		?>
		<select name="bigclassid" id="bigclassid">
         <option value="" selected="selected">请选择类别</option>
         <?php 
		 while($rowb= fetch_array($rsb)){
		 ?>
       <option value="<?php echo $rowb["classid"]?>" <?php if ($rowb["classid"]==$bigclassid) { echo "selected";}?>><?php echo $rowb["classname"]?></option>
          <?php
		  }
		  ?>
        </select>
	 	 </td>
    </tr>
    <tr class="tdbg"> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border">
	  <script language="javascript">   
//动态增加表单元素。
function AddElement(){   
//得到需要被添加的html元素。
var TemO=document.getElementById("add");   
//var newInput = document.createElement("<input type='text' size='50' maxlength='50' name='classname[]' value='小类别名称'>");
	if($.browser.msie) {
	var newInput = document.createElement("<input type='text' size='50' maxlength='50' name='classname[]' value='小类别名称'>");
	}else{
	var newInput = document.createElement("input");
	newInput.type = "text";
	newInput.name = "classname[]";
	newInput.size = "50";
	newInput.maxlength = "50";
	newInput.value = "小类别名称";
	}
TemO.appendChild(newInput);     
var newline= document.createElement("hr"); 
TemO.appendChild(newline);   
}   
</script>
<div id="add">
	   <input name="classname[]" type="text" size="50" maxlength="50" value="小类别名称" style="margin:4px 0">
<hr/>
	  </div> 
	  <img src="image/icobigx.gif" width="23" height="11"> <a href="javascript:void(0)" onClick='AddElement()'><img src='image/icobig.gif' border="0"> 添加新类别</a>	   
      <input name="action" type="hidden" id="action3" value="add">
      <input type="submit" value="提交"></td>
    </tr>
  </table>
</form>
<?php
}
}

function modifybigclass(){
global $classid,$TitleClass,$TemplateFileName;
checkid($classid);
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';

$FoundErr=0;
$ErrMsg="";

if ($action=="modify"){
$classname=trim($_POST["classname"]);
$classzm=pinyin($classname);
$oldclassname=trim($_POST["oldclassname"]);
$img=trim($_POST["img"]);
$isshow=isset($_POST['isshow'])?$_POST['isshow'][0]:0;
$title=trim($_POST["title"]);
if ($title=="") {$title=$classname;}

$keyword=trim($_POST["keyword"]);
if ($keyword=="") {$keyword=$classname;}

$description=trim($_POST["description"]);
if ($description==""){$description=$classname;}
$skin=$_POST["skin"][0]."|".$_POST["skin"][1];

	if ($classname==''){
	$FoundErr=1;
	$ErrMsg=$ErrMsg . "<li>请填大类名！</li>";
	}

	$sql="select * from `".$_COOKIE['tablename']."` where classid='" .$classid."'";
	$rs=query($sql);
	$row=num_rows($rs);
	if (!$row){
	$FoundErr=1;
	$ErrMsg=$ErrMsg . "<li>此大类不存在！</li>";
	}
	
	if ($classname<>$oldclassname) {
	$sqln="select * from `".$_COOKIE['tablename']."` where parentid=0 and classname='".$classname."'";
	$rsn=query($sqln);
	$rown=num_rows($rsn);
	if ($rown){
		$FoundErr=1;
		$ErrMsg=$ErrMsg . "<li>此大类名称已存在！</li>";
	}
	}
		
	if ($FoundErr==0){
	query("update `".$_COOKIE['tablename']."` set classname='$classname',classzm='$classzm',img='$img',isshow='$isshow',
	title='$title',keyword='$keyword',description='$description',skin='$skin' where classid='" .$classid."'");
	
	if ($classname<>$oldclassname) {//类名改变的情况下
		
		if ($_COOKIE['tablename']=='zzcms_zxclass' || $_COOKIE['tablename']=='zzcms_specialclass' ){//专题表和资讯表中有classname其它没有
		query("Update `zzcms_".$TemplateFileName."` set bigclassname='" . $classname . "'  where bigclassid='" . $classid . "' ");
		}
		
	}	
	
	echo "<script>location.href='?#B".$classid."'</script>";
	}
}

if ($FoundErr==1){
WriteErrMsg($ErrMsg);
}else{
$sql="select * from `".$_COOKIE['tablename']."` where classid='" .$classid."'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle">修改<?php echo $TitleClass?>大类</div>
<form name="form1" method="post" action="?dowhat=modifybigclass" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="20%" align="right" class="border">大类ID：</td>
      <td width="80%" class="border"><?php echo $row["classid"]?> <input name="classid" type="hidden" id="classid" value="<?php echo $row["classid"]?>"></td>
    </tr>
    <tr> 
      <td align="right" class="border">大类名称：</td>
      <td class="border"> <input name="classname" type="text" id="classname" value="<?php echo $row["classname"]?>" size="60" maxlength="30"> 
        <input name="oldclassname" type="hidden" id="oldclassname" value="<?php echo $row["classname"]?>" size="60" maxlength="30"></td>
    </tr>
    <tr>
      <td align="right" class="border">大类名称前的图标地址：</td>
      <td class="border"><input name="img" type="text" id="img" value="<?php echo $row["img"]?>" size="60" maxlength="50">
      </td>
    </tr>
    <tr class="tdbg"> 
      <td align="right" class="border">是否显示该类别：</td>
      <td class="border"><input name="isshow[]" type="checkbox" id="isshow[]" value="1" <?php if ($row["isshow"]==1) { echo "checked";}?>>
        （选中为显示） </td>
    </tr>
    <tr> 
      <td colspan="2" class="border">SEO优化设置（如与大类名称相同，以下可以留空不填）</td>
    </tr>
    <tr> 
      <td align="right" class="border" >标题（title）：</td>
      <td class="border" ><input name="title" type="text" id="title" value="<?php echo $row["title"]?>" size="60" maxlength="255"></td>
    </tr>
    <tr> 
      <td align="right" class="border" >关键词（keyword）：</td>
      <td class="border" ><input name="keyword" type="text" id="keyword"  value="<?php echo $row["keyword"]?>" size="60" maxlength="255">
        (多个关键词以“,”隔开)</td>
    </tr>
    <tr> 
      <td align="right" class="border" >描述（description）：</td>
      <td class="border" ><input name="description" type="text" id="description"  value="<?php echo $row["description"]?>" size="60" maxlength="255">
        (适当出现关键词，最好是完整的句子)</td>
    </tr>
    <tr id="trseo">
      <td colspan="2" class="border" >模板选择</td>
    </tr>
    <tr id="trkeywords">
      <td align="right" class="border" >类别页模板文件</td>
      <td class="border" ><select name="skin[]" id="skin[]">
          <?php
$dir = opendir("../template/".siteskin);
$skin=explode("|",$row["skin"]);
while(($file = readdir($dir))!=false){
if ($file!="." && $file!=".." && strpos($TemplateFileName."_class",substr($file,0,8))!==false) { //不读取. ..
?>
<option value="<?php echo $file?>" <?php if ($skin[0]==$file){ echo  "selected";}?>><?php echo $file?></option>
<?php
}
}
closedir($dir);
?>
      </select></td>
    </tr>
    <tr id="trkeywords">
      <td align="right" class="border" >列表页模板文件</td>
      <td class="border" ><select name="skin[]" id="skin[]">
          <?php
$dir = opendir("../template/".siteskin);
$skin=explode("|",$row["skin"]);
while(($file = readdir($dir))!=false){
if ($file!="." && $file!=".." && strpos($TemplateFileName."_list",substr($file,0,7))!==false) { //不读取. ..
?>
<option value="<?php echo $file?>" <?php if (@$skin[1]==$file){ echo  "selected";}?>><?php echo $file?></option>
<?php
}
}
closedir($dir);
?>
      </select></td>
    </tr>
    <tr> 
      <td class="border">&nbsp;</td>
      <td class="border"> <input name="action" type="hidden" id="action" value="modify"> 
        <input name="save" type="submit" id="save" value=" 修 改 "> </td>
    </tr>
  </table>
</form>
<?php
}
}

function modifysmallclass(){
global $classid,$TitleClass,$TemplateFileName;
checkid($classid);
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';

$FoundErr=0;
$ErrMsg="";

if ($action=="modify"){
$bigclassid=trim($_POST["bigclassid"]);
checkid($bigclassid);
$oldbigclassid=trim($_POST["oldbigclassid"]);
$classname=trim($_POST["classname"]);
$oldclassname=trim($_POST["oldclassname"]);
$classzm=pinyin($classname);

$title=trim($_POST["title"]);
if ($title=="") {$title=$classname;}

$keyword=trim($_POST["keyword"]);
if ($keyword=="") {$keyword=$classname;}

$description=trim($_POST["description"]);
if ($description==""){$description=$classname;}

	if ($classname==''){
	$FoundErr=1;
	$ErrMsg=$ErrMsg . "<li>请填小类名！</li>";
	}
	
	$sql="select * from `".$_COOKIE['tablename']."` where classid='" .$classid."'";
	$rs=query($sql);
	$row=num_rows($rs);
	if (!$row){
	$FoundErr=1;
	$ErrMsg=$ErrMsg . "<li>此小类不存在！</li>";
	}
	
	if ($classname<>$oldclassname || $bigclassid<>$oldbigclassid) {
	$sqln="select * from `".$_COOKIE['tablename']."` where parentid='".$bigclassid."' and classname='".$classname."'";
	$rsn=query($sqln);
	$rown=num_rows($rsn);
	if ($rown){
		$FoundErr=1;
		$ErrMsg=$ErrMsg . "<li>此小类名称已存在！</li>";
	}
	}
	
	if ($FoundErr==0) {
	query("update `".$_COOKIE['tablename']."` set parentid='$bigclassid',classname='$classname',classzm='$classzm',
	title='$title',keyword='$keyword',description='$description' where  classid='" .$classid."'");
	if ($bigclassid<>$oldbigclassid) {//小类别改变所属大类情况下
	query("Update `zzcms_".$TemplateFileName."` set bigclassid='".$bigclassid."' where bigclassid='".$oldbigclassid."' and smallclassid='" .$classid ."'");
	
	if ($_COOKIE['tablename']=='zzcms_zxclass' || $_COOKIE['tablename']=='zzcms_specialclass' ){//专题表和资讯表中有classname其它没有
	query("Update `zzcms_".$TemplateFileName."` set bigclassname=(select classname from `".$_COOKIE['tablename']."` where classid='".$bigclassid."') 
	where bigclassid='" . $bigclassid . "' and smallclassid='" . $classid . "' ");	
	}
	
	}
	if ($classname<>$oldclassname) {//小类名改变的情况下
		if ($_COOKIE['tablename']=='zzcms_zxclass' || $_COOKIE['tablename']=='zzcms_specialclass' ){//专题表和资讯表中有classname其它没有
		query("Update `zzcms_".$TemplateFileName."` set smallclassname='".$classname ."' where bigclassid='".$bigclassid."' and smallclassid='".$classid ."'");
		}
	}
	echo "<script>location.href='?#S".$classid."'</script>";
	}
}

if ($FoundErr==1){
WriteErrMsg($ErrMsg);
}else{
$sql="select * from `".$_COOKIE['tablename']."` where classid='".$classid."'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle">修改<?php echo $TitleClass?>小类</div>
<form name="form1" method="post" action="?dowhat=modifysmallclass" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="20%"  align="right" class="border">所属大类：</td>
      <td width="80%" class="border"> 
	    <?php
		$sqlb = "select * from `".$_COOKIE['tablename']."` where parentid=0";
	    $rsb=query($sqlb);
		?>
		<select name="bigclassid" id="bigclassid">
                <option value="" selected="selected">请选择类别</option>
                <?php
		while($rowb= fetch_array($rsb)){
			?>
<option value="<?php echo $rowb["classid"]?>" <?php if ($rowb["classid"]==$row["parentid"]) { echo "selected";}?>><?php echo $rowb["classname"]?></option>
                <?php
		  }
		  ?>
        </select>

      <input name="oldbigclassid" type="hidden" id="oldbigclassid" value="<?php echo $row["parentid"]?>"></td>
    </tr>
    <tr> 
      <td align="right" class="border">小类名称：</td>
      <td class="border"> <input name="classname" type="text" id="classname" value="<?php echo $row["classname"]?>" size="60" maxlength="30">
        <input name="oldclassname" type="hidden" id="oldclassname" value="<?php echo $row["classname"]?>"></td>
    </tr>
    <tr> 
      <td colspan="2" class="border">SEO优化设置（如与大类名称相同，以下可以留空不填）</td>
    </tr>
    <tr> 
      <td align="right" class="border" >标题（title）：</td>
      <td class="border" ><input name="title" type="text" id="title"  value="<?php echo $row["title"]?>" size="60" maxlength="255"></td>
    </tr>
    <tr> 
      <td align="right" class="border" >关键词（keyword）：</td>
      <td class="border" ><input name="keyword" type="text" id="keyword"  value="<?php echo $row["keyword"]?>" size="60" maxlength="255">
        (多个关键词以“,”隔开)</td>
    </tr>
    <tr> 
      <td align="right" class="border" >描述（description）：</td>
      <td class="border" ><input name="description" type="text" id="description"  value="<?php echo $row["description"]?>" size="60" maxlength="255">
        (适当出现关键词，最好是完整的句子)</td>
    </tr>
    <tr> 
      <td class="border">&nbsp;</td>
      <td class="border"> <input name="classid" type="hidden" id="classid" value="<?php echo $row["classid"]?>">
        <input name="action" type="hidden" id="action4" value="modify"> 
        <input name="save" type="submit" id="save" value=" 修 改 "> </td>
    </tr>
  </table>
</form>
<?php
}
}
?>
</body>
</html>