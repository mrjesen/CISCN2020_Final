<?php
ob_start();//打开缓冲区，可以setcookie
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>发布招聘信息</title>
<?php
if (str_is_inarr(usergr_power,'job')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限');
}
?>
<script src="../js/area.js"></script>
<script language = "JavaScript">
function CheckForm(){
	ischecked=false;
 	for(var i=0;i<document.myform.bigclassid.length;i++){ 
		if(document.myform.bigclassid[i].checked==true)  {
		 ischecked=true ;
   		} 
	}
   if(document.myform.bigclassid.checked==true)  {
		 ischecked=true ;
   		} 
 	if (ischecked==false){
	alert("请选择大类别！");	
    return false;
	}

  if (document.myform.province.value=="请选择省份"){
	document.myform.province.focus();
    alert("请选择省份！");
	return false;
  }  
  
  if (document.myform.city.value=='请选择城区'){
	document.myform.city.focus();
    alert("请选择城市！");
	return false;
  }   
ischecked=false;
 	for(var i=0;i<document.myform.smallclassid.length;i++){ 
		if(document.myform.smallclassid[i].checked==true){
		 ischecked=true ;
   		} 
	}
	
   if(document.myform.smallclassid.checked==true)  {
		 ischecked=true ;
   		} 
 	if (ischecked==false){
	alert("请选择小类别！");	
    return false;
	}
}	
function doClick_E(o){
	 var id,e;
	id=0;
	 for(var i=1;i<=document.myform.bigclassid.length;i++){
	   id ="E"+i;
	   e = document.getElementById("E_con"+i);
	   if(id != o.id){
	   	 e.style.display = "none";		
	   }else{
		e.style.display = "block";
	   }
	 }
	   if(id==0){
		document.getElementById("E_con1").style.display = "block";
	   }
	 //document.write(classnum)
	 }
function addSrcToDestList() {
}	 
</script>
</head>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
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
$jobid=isset($_POST["jobid"])?$_POST["jobid"]:0;
checkid($jobid);

$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:'0';
$smallclassid = isset($_POST['smallclassid'])?$_POST['smallclassid']:'0';
$smallclassname="未指定小类";
if ($bigclassid!=0){
$rs = query("select classname from zzcms_jobclass where classid='$bigclassid'"); 
$row= fetch_array($rs);
$bigclassname=$row["classname"];
}

if ($smallclassid !=0){
$rs = query("select classname from zzcms_jobclass where classid='$smallclassid'"); 
$row= fetch_array($rs);
$smallclassname=$row["classname"];
}

$rs=query("select comane,id from zzcms_user where username='".$username."'");
$row=fetch_array($rs);
$comane=$row["comane"];
$userid=$row["id"];

//判断大小类是否一致，修改产品时有用
if ($smallclassid<>0){ 
$sql="select * from zzcms_jobclass where parentid='".$bigclassid."' and  classid='".$smallclassid."'";
$rs=query($sql);
$row=fetch_array($rs);
if (!$row){
echo"<script>alert('请选择小类');location.href='?do=modify&id=".$jobid."'</script>";
}
}

//判断是不是重复信息
if ($_POST["action"]=="add" ){
$sql="select * from zzcms_job where jobname='".$jobname."' and editor='".$username."' ";
$rs=query($sql);
$row=num_rows($rs);
if ($row){
showmsg('您已发布过这条信息，请不要发布重复的信息！','jobmanage.php');
}
}elseif($_POST["action"]=="modify"){
$sql="select * from zzcms_job where jobname='".$jobname."' and editor='".$username."' and id<>'".$jobid."' ";
$rs=query($sql);
$row=num_rows($rs);
if ($row){
showmsg('您已发布过这条信息，请不要发布重复的信息！','jobmanage.php');
}
}
  
if ($_POST["action"]=="add"){
$isok=query("Insert into zzcms_job(jobname,bigclassid,smallclassid,sm,province,city,xiancheng,sendtime,editor,userid,comane) values('$jobname','$bigclassid','$smallclassid','$sm','$province','$city','$xiancheng','".date('Y-m-d H:i:s')."','$username','$userid','$comane')") ;  
$jobid=insert_id();		
}elseif ($_POST["action"]=="modify"){
$isok=query("update zzcms_job set jobname='$jobname',bigclassid='$bigclassid',smallclassid='$smallclassid',sm='$sm',
province='$province',city='$city',xiancheng='$xiancheng',sendtime='".date('Y-m-d H:i:s')."',
editor='$username',userid='$userid',comane='$comane',passed=0 where id='$jobid'");
}
passed("zzcms_job");
setcookie("bigclassid",$bigclassid,time()+3600*24,"/user");

?>
<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	标题：<?php echo $jobname?><br>
	
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $jobid?>">[修改]</a></li>
	<li><a href="jobmanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("job",$jobid)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php
}

function add(){
$tablename="zzcms_job";
include("checkaddinfo.php");
?>

<div class="admintitle">发布招聘信息</div>
<form  action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="20%" align="right" valign="top" class="border">职位类别：</td>
            <td valign="middle" class="border" > 
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td> <fieldset class="fieldsetstyle">
                    <legend>请选择大类</legend>
                    <?php
        $sql = "select * from zzcms_jobclass where parentid='0' order by xuhao asc";
		$rs = query($sql); 
		$n=1;
		while($row= fetch_array($rs)){
		if (@$_COOKIE['bigclassid']==$row['classid']){
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this)' value='$row[classid]' checked/><label for='E$n'>$row[classname]</label>";
		}else{
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this)' value='$row[classid]'/><label for='E$n'>$row[classname]</label>";
		}
		$n ++;
		if (($n-1) % 7==0) {echo "<br/>";}
		}
			?>
                    </fieldset></td>
                </tr>
                <tr> 
                  <td> 
                    <?php
$sql="select * from zzcms_jobclass where parentid=0 order by xuhao asc";
$rs = query($sql); 
$n=1;
while($row= fetch_array($rs)){
if (@$_COOKIE['bigclassid']==$row["classid"]) {  
echo "<div id='E_con$n' style='display:block;'>";
}else{
echo "<div id='E_con$n' style='display:none;'>";
}
echo "<fieldset class='fieldsetstyle'><legend>请选择小类</legend>";

$sqln="select * from zzcms_jobclass where parentid='$row[classid]' order by xuhao asc";
$rsn =query($sqln); 
$nn=1;
while($rown= fetch_array($rsn)){
echo "<input name='smallclassid' id='radio$nn$n' type='radio' value='$rown[classid]' />";
echo "<label for='radio$nn$n'>$rown[classname]</label>";
if (($nn-1) % 7==0) {echo "<br/>";}
$nn ++;
}
echo "</fieldset>";
echo "</div>";
$n ++;
}
?>                  </td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td align="right" class="border" >职位：</td>
            <td class="border" ><input name="jobname" type="text" id="jobname" class="biaodan" size="50" maxlength="255" /></td>
          </tr>
		
          <tr> 
            <td align="right" class="border" >内容：</td>
            <td class="border" > <textarea name="sm" cols="60" rows="4" class="biaodan" style="height:auto"></textarea></td>
          </tr>
          <tr> 
            <td align="right" class="border2">工作地点：</td>
            <td class="border2">       
<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan"></select>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '', '', '');
</script>
</td>
          </tr>
		  	    
          <tr> 
            <td align="center" class="border2" >&nbsp;</td>
            <td class="border2" > <input name="action" type="hidden" id="action2" value="add" /> 
              <input name="Submit" type="submit" class="buttons" value="填好了，发布" /></td>
          </tr>
        </table>
</form>
<?php
}

function modify(){
global $username;

$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

$sql="select * from zzcms_job where id='$id'";
$rs = query($sql); 
$row = fetch_array($rs);
if ($id!=0 && $row["editor"]<>$username) {
markit();showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>

<div class="admintitle">修改招聘信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="20%" align="right" valign="top" class="border2" >职位类别：</td>
            <td width="80%" class="border2" > 
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr> 
                  <td> <fieldset class="fieldsetstyle">
                    <legend>请选择大类</legend>
                    <?php
        $sqlB = "select classid,classname from zzcms_jobclass where parentid='0' order by xuhao asc";
		$rsB =query($sqlB); 
		$n=1;
		while($rowB= fetch_array($rsB)){
		if ($row['bigclassid']==$rowB['classid']){
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this)' value='$rowB[classid]' checked/><label for='E$n'>$rowB[classname]</label>";
		}else{
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this)' value='$rowB[classid]' /><label for='E$n'>$rowB[classname]</label>";
		}
		$n ++;
		if ($n % 7==0) {echo "<br/>";}
		}
			?>
                    </fieldset></td>
                </tr>
                <tr> 
                  <td> 
                    <?php
$sqlB="select classid,classname from zzcms_jobclass where parentid='0' order by xuhao asc";
$rsB =query($sqlB); 
$n=1;
while($rowB= fetch_array($rsB)){
if ($row["bigclassid"]==$rowB["classid"]) {  
echo "<div id='E_con$n' style='display:block;'>";
}else{
echo "<div id='E_con$n' style='display:none;'>";
}
echo "<fieldset class='fieldsetstyle'><legend>请选择小类</legend>";
$sqlS="select * from zzcms_jobclass where parentid='$rowB[classid]' order by xuhao asc";
$rsS =query($sqlS); 
$nn=1;
while($rowS= fetch_array($rsS)){

if ($row['smallclassid']==$rowS['classid']){
echo "<input name='smallclassid' id='radio$nn$n' type='radio' value='$rowS[classid]' checked/>";
}else{
echo "<input name='smallclassid' id='radio$nn$n' type='radio' value='$rowS[classid]' />";
}
echo "<label for='radio$nn$n'>$rowS[classname]</label>";
$nn ++;
if ($n % 7==0) {echo "<br/>";}           
}
echo "</fieldset>";
echo "</div>";
$n ++;
}
?>                  </td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td align="right" class="border" >职位：</td>
            <td class="border" ><input name="jobname" type="text" id="jobname" class="biaodan" value="<?php echo $row["jobname"]?>" size="60" maxlength="45"></td>
          </tr>
		   
          <tr> 
            <td align="right" class="border" >内容：</td>
            <td class="border" > <textarea name="sm" cols="60" rows="4" id="sm" class="biaodan" style="height:auto"><?php echo stripfxg($row["sm"]) ?></textarea></td>
          </tr>
          <tr> 
            <td align="right" class="border">工作地点：</td>
            <td class="border">
			              
<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan"></select>

<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo $row['province']?>', '<?php echo $row["city"]?>', '<?php echo $row["xiancheng"]?>');
</script>
			</td>
          </tr>
		  
          <tr> 
            <td align="center" class="border2" >&nbsp;</td>
            <td class="border2" > <input name="jobid" type="hidden" id="jobid" value="<?php echo $row["id"] ?>"> 
              <input name="action" type="hidden" id="action2" value="modify"> 
              <input name="page" type="hidden" id="action" value="<?php echo $page ?>"> 
              <input name="Submit" type="submit" class="buttons" value="修改"></td>
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