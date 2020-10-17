<?php
ob_start();//打开缓冲区，可以setcookie
include("../inc/conn.php");
include("check.php");

?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<?php
if (str_is_inarr(usergr_power,'pp')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限');
}
?>
<script language = "JavaScript" src="../js/gg.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.proname.value==""){
	document.myform.proname.focus();
    document.myform.proname.value='此处不能为空';
    document.myform.proname.select();
	return false;
  }
 if (document.myform.sm.value==""){
	document.myform.sm.focus();
    document.myform.sm.value='此处不能为空';
    document.myform.sm.select();
	return false;
  }
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
	id=0
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
</script>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
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
$page = isset($_POST['cpid'])?$_POST['cpid']:1;//返回列表页用
checkid($page);
$cpid = isset($_POST['cpid'])?$_POST['cpid']:'0';
checkid($cpid,1);

$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:'0';
checkid($bigclassid,1);

$smallclassid=isset($_POST['smallclassid'])?$_POST["smallclassid"]:0;
checkid($smallclassid,1);

checkstr($img,"upload");//入库前查上传文件地址是否合格

$rs=query("select comane,id from zzcms_user where username='".$username."'");
$row=fetch_array($rs);
$comane=$row["comane"];
$userid=$row["id"];

//判断大小类是否一致，修改产品时有用
if ($smallclassid<>0){
$sql="select * from zzcms_zsclass where parentid='".$bigclassid."' and  classid='".$smallclassid."'";
$rs=query($sql);
$row=fetch_array($rs);
if (!$row){
showmsg('请选择小类',"?do=modify&id=".$cpid);//这里传的ID参数两边不要加''，否则提示
}
}


//判断是不是重复信息
if ($_REQUEST["action"]=="add" ){
$sql="select * from zzcms_pp where ppname='".$proname."' and editor='".$username."' ";
$rs=query($sql);
$row=num_rows($rs);
if ($row){
showmsg('您已发布过该信息，请不要发布重复的信息！');
}
}elseif($_REQUEST["action"]=="modify"){
$sql="select * from zzcms_pp where ppname='".$proname."' and editor='".$username."' and id<>'".$cpid."' ";
$rs=query($sql);
$row=num_rows($rs);
if ($row){
showmsg('您已发布过该信息，请不要发布重复的信息！');
}
}

if ($_POST["action"]=="add"){
$isok=query("Insert into zzcms_pp(ppname,bigclassid,smallclassid,sm,img,sendtime,editor,userid,comane) values('$proname','$bigclassid','$smallclassid','$sm','$img','".date('Y-m-d H:i:s')."','$username','$userid','$comane')") ;
$cpid=insert_id();		
}elseif ($_POST["action"]=="modify"){
$oldimg=trim($_POST["oldimg"]);
checkstr($oldimg,"upload");
$isok=query("update zzcms_pp set ppname='$proname',bigclassid='$bigclassid',smallclassid='$smallclassid',sm='$sm',img='$img',sendtime='".date('Y-m-d H:i:s')."',editor='$username',userid='$userid',comane='$comane',passed=0 where id='$cpid'");

	if ($oldimg<>$img && $oldimg<>"/image/nopic.gif") {
	//deloldimg
		$f=$oldimg;
		if (file_exists($f)){
		unlink($f);		
		}
		$fs=str_replace(".","_small.",$oldimg);
		if (file_exists($fs)){
		unlink($fs);		
		}
	}
}

passed("zzcms_pp");		
setcookie("bigclassid",$bigclassid,time()+3600*24,"/user");
?>
<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	名称：<?php echo $proname?><br>
	
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $cpid?>">[修改]</a></li>
	<li><a href="ppmanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("pp",$cpid)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php
}

function add(){
$tablename="zzcms_pp";
include("checkaddinfo.php");
?>
<div class="admintitle">发布品牌信息</div>

<form  action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="20%" align="right" class="border2" >名称<font color="#FF0000">（必填）</font>：</td>
            <td width="80%" class="border2" > <input name="proname" type="text" id="proname" class="biaodan" onClick="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';" onBlur="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';" size="60" maxlength="45" /></td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border">类别<font color="#FF0000">（必填）</font>：</td>
            <td valign="middle" class="border" > 
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr> 
                  <td> <fieldset class="fieldsetstyle">
                    <legend>请选择所属大类</legend>
                    <?php
        $sql = "select * from zzcms_zsclass where parentid=0 order by xuhao asc";
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
$sql="select * from zzcms_zsclass where parentid=0 order by xuhao asc";
$rs = query($sql); 
$n=1;
while($row= fetch_array($rs)){
if (@$_COOKIE['bigclassid']==$row["classid"]) {  
echo "<div id='E_con$n' style='display:block;'>";
}else{
echo "<div id='E_con$n' style='display:none;'>";
}
echo "<fieldset class='fieldsetstyle'><legend>请选择所属小类</legend>";

$sqln="select * from zzcms_zsclass where parentid='$row[classid]' order by xuhao asc";
$rsn =query($sqln); 
$nn=1;
while($rown= fetch_array($rsn)){
echo "<input name='smallclassid' id='radio$nn$n' type='radio' value='$rown[classid]' />";
echo "<label for='radio$nn$n'>$rown[classname]</label>";
$nn ++;
if (($nn-1) % 7==0) {echo "<br/>";}
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
            <td align="right" class="border" >介绍<font color="#FF0000">（必填）</font>：</td>
            <td class="border" > <textarea name="sm" cols="100%" rows="10" id="sm" class="biaodan" style="height:auto" onClick="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';" onBlur="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';"></textarea></td>
          </tr>
          <tr> 
            <td align="right" class="border" >上传图片：(小于<?php echo maximgsize?>K)
 <input name="img" type="hidden" id="img" value="/image/nopic.gif"/></td>
            <td class="border" >
			 <table width="140" height="140" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
                <tr align="center" bgcolor="#FFFFFF"> 
                  <td width="120" id="showimg" onClick="openwindow('/uploadimg_form.php',400,300)"> <input name="Submit2" type="button"  value="上传图片" /></td>
                </tr>
              </table></td>
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

$sql="select * from zzcms_pp where id='$id'";
$rs = query($sql); 
$row = fetch_array($rs);
if ($id!=0 && $row["editor"]<>$username) {
markit();
showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>

<div class="admintitle">修改品牌信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="20%" align="right" class="border" >名称<font color="#FF0000">（必填）</font>：</td>
            <td width="80%" class="border" > <input name="proname" type="text" id="proname" class="biaodan" value="<?php echo $row["ppname"]?>" size="60" maxlength="45" onClick="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';" onBlur="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';"></td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border2" ><br>
              类别<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr> 
                  <td> <fieldset class="fieldsetstyle">
                    <legend>请选择所属大类</legend>
                    <?php
        $sqlB = "select classid,classname from zzcms_zsclass where parentid=0 order by xuhao asc";
		$rsB =query($sqlB); 
		$n=1;
		while($rowB= fetch_array($rsB)){
		if ($row['bigclassid']==$rowB['classid']){
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this);uncheckall()' value='$rowB[classid]' checked/><label for='E$n'>$rowB[classname]</label>";
		}else{
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this);uncheckall()' value='$rowB[classid]' /><label for='E$n'>$rowB[classname]</label>";
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
$sqlB="select classid,classname from zzcms_zsclass where parentid=0 order by xuhao asc";
$rsB =query($sqlB); 
$n=1;
while($rowB= fetch_array($rsB)){
if ($row["bigclassid"]==$rowB["classid"]) {  
echo "<div id='E_con$n' style='display:block;'>";
}else{
echo "<div id='E_con$n' style='display:none;'>";
}
echo "<fieldset class='fieldsetstyle'><legend>请选择所属小类</legend>";
$sqlS="select classid,classname from zzcms_zsclass where parentid='$rowB[classid]' order by xuhao asc";
$rsS =query($sqlS); 
$nn=0;
while($rowS= fetch_array($rsS)){
if ($row['smallclassid']==$rowS['classid']){
echo "<input name='smallclassid' id='radio$nn$n' type='radio' value='$rowS[classid]' checked/>";
}else{
echo "<input name='smallclassid' id='radio$nn$n' type='radio' value='$rowS[classid]' />";
}
echo "<label for='radio$nn$n'>$rowS[classname]</label>";
$nn ++;
if ($nn % 7==0) {echo "<br/>";}             
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
            <td align="right" class="border" >介绍<font color="#FF0000">（必填）</font>：</td>
            <td class="border" > <textarea name="sm" cols="100%" rows="10" id="sm" class="biaodan" style="height:auto" onClick="javascript:if (this.value=='此处不能为空') {this.value=''};" onBlur="javascript:if (this.value=='此处不能为空') {this.value=''};"><?php echo stripfxg($row["sm"]) ?></textarea></td>
          </tr>
          <tr> 
            <td align="right" class="border" >上传图片：（小于<?php echo maximgsize?>K） 
 <input name="oldimg" type="hidden" id="oldimg" value="<?php echo $row["img"] ?>"> 
              <input name="img"type="hidden" id="img" value="<?php echo $row["img"] ?>"></td>
            <td class="border" > <table height="140"  width="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
                <tr> 
                  <td align="center" bgcolor="#FFFFFF" id="showimg" onClick="openwindow('/uploadimg_form.php?noshuiyin=1',400,300)"> 
                    <?php
				  if($row["img"]<>""){
				  echo "<img src='".$row["img"]."' border=0 width=120 /><br>点击可更换图片";
				  }else{
				  echo "<input name='Submit2' type='button'  value='上传图片'/>";
				  }
				  ?>                  </td>
                </tr>
              </table></td>
          </tr>
         
		   
          <tr> 
            <td align="center" class="border2" >&nbsp;</td>
            <td class="border2" > <input name="cpid" type="hidden" value="<?php echo $row["id"] ?>"> 
              <input name="action" type="hidden"  value="modify"> 
              <input name="page" type="hidden"  value="<?php echo $page ?>"> 
              <input name="Submit" type="submit" class="buttons" value="保存修改结果"></td>
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