<?php 
include("admin.php"); 
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/js/gg.js"></script>
<script language="JavaScript" type="text/JavaScript">
function checkform(){
if (document.form1.admins.value==""){
    alert("管理员名称不能为空！");
    document.form1.admins.focus();
    return false;
}
if (document.form1.passs.value==""){
    alert("密码不能为空！");
    document.form1.passs.focus();
    return false;
}
if (document.form1.passs.value!=document.form1.passs2.value){
alert ("两次密码输入不一致，请重新输入。");
document.form1.passs.value='';
document.form1.passs2.value='';
document.form1.passs.focus();
return false;
}  

if (document.form1.passs.value !=""){
    var re=/^[0-9a-zA-Z]{4,14}$/; //只输入数字和字母的正则
    if(document.form1.passs.value.search(re)==-1){
	alert("密码只能为字母和数字，字符介于4到14个。");
	document.form1.passs.value="";
	document.form1.passs.focus();
	return false;
    }
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
case "pwd";pwd();break;
case "save";save();break;
default;show();
}

function show(){
$action = isset($_GET['action'])?$_GET['action']:"";
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

if ($action=="del" ){
checkadminisdo("adminmanage");
query("delete from zzcms_admin where id='".$id."'");
echo  "<script>alert('删除成功');location.href='?'</script>";
}
$sql="select * from zzcms_admin order by id desc";
$rs = query($sql); 
?>
<div class="admintitle">管理员管理</div>
<div class="border2 center"><input name="submit3" type="submit" class="buttons" onClick="javascript:location.href='?do=add'" value="添加管理员"></div>
<table width="100%" border="0" cellpadding="5" cellspacing="1">
  <tr class="trtitle"> 
    <td width="5%" align="center">ID</td>
    <td width="10%" align="center">用户名</td>
    <td width="10%">所属用户组</td>
    <td width="5%" align="center">登录次数</td>
    <td width="10%" align="center">上次登录IP</td>
    <td width="10%" align="center">上次登录时间</td>
    <td width="10%" align="center">操 作</td>
  </tr>
 <?php
	while($row= fetch_array($rs)){
?>
  <tr class="trcontent">
    <td align="center"><?php echo $row["id"]?></td>
    <td align="center"><?php echo $row["admin"]?></td>
    <td>
	
	<?php 
			$rsn=query("select groupname from zzcms_admingroup where id='".$row['groupid']."'");
			$r=num_rows($rsn);
			if ($r){
			$r=fetch_array($rsn);
			echo $r["groupname"];
			}
			 ?>
    <a href="admingroup.php?do=modify&id=<?php echo $row["groupid"]?>">（查看此组权限）</a></td>
    <td align="center"><?php echo $row["logins"]?></td>
    <td><?php echo $row["showloginip"]?></td>
    <td><?php echo $row["showlogintime"]?></td>
    <td align="center"><a href="?do=modify&admins=<?php echo $row["admin"]?>">修改权限</a> 
	 | <a href="?do=pwd&admins=<?php echo $row["admin"]?>">修改密码</a> |   
	 <?php
$rsn2=query("select id from zzcms_admin where groupid=(select id from zzcms_admingroup where groupname='超级管理员')");
$rown2=num_rows($rsn2);//超级管理员数	 
	 
$rsn=query("select groupname from zzcms_admingroup where id=(select groupid from zzcms_admin where id=".$row["id"].")");
$rown=fetch_array($rsn);
if ($rown["groupname"]=='超级管理员' && $rown2 < 2){
echo "<span style='color:#666666' title='至少要保留1个“超级管理员”，添加新“超级管理员”后，才能删除老的'>删除</span>";
}else{
	 ?>
	 <a href="?action=del&id=<?php echo $row["id"] ?>" onClick="return ConfirmDel()">删除</a>
<?php
}
?>	  
    </td>
  </tr>
  <?php 
  }   
   ?>
</table>
  <?php 
}   


function add(){
global $admins,$passs;
checkadminisdo("adminmanage");
$action = isset($_POST['action'])?$_POST['action']:"";
$founderr=0;
if ($action=="add"){
	
	if($admins==''|| $passs==''){
	showmsg('用户名和密码不能为空');
	}

	$admins=trim($_POST["admins"]);
	$passs=md5(trim($_POST["passs"]));
	$groupid=$_POST["groupid"];
	
	$sql="select admin from zzcms_admin where admin='".$admins."'";
	$rs = query($sql);
	$row= num_rows($rs);//返回记录数
	if($row){ 
	$founderr=1;
	$ErrMsg="您填写的用户名已存在！请更换用户名！";
	}

	if ($founderr==1){
		WriteErrMsg($ErrMsg);
		}else{
		$sql="insert into zzcms_admin (admin,pass,groupid) values ('$admins','$passs','$groupid')";
		query($sql);
		echo "<script>location.href='adminlist.php'</script>";	
		}
}else{
?>
<div class="admintitle">添加管理员</div>
<form name="form1" method="post" action="?do=add" onSubmit="return checkform()">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td align="right" class="border">所属用户组：</td>
      <td class="border">
	   <select name="groupid" id="groupid">
          <?php
    $sql="Select * from zzcms_admingroup order by id asc";
    $rs = query($sql); 
	while($row= fetch_array($rs)){
	echo "<option value='".$row["id"]."'>".$row["groupname"]."</option>";
	}
	?>
        </select> </td>
    </tr>
    <tr> 
      <td width="36%" align="right" class="border">管理员：</td>
      <td width="64%" class="border"> <input name="admins" type="text" id="admins"></td>
    </tr>
    <tr> 
      <td align="right" class="border">密码：</td>
      <td class="border"> <input name="passs" type="password" id="passs"></td>
    </tr>
    <tr> 
      <td align="right" class="border">再次输入密码进行确认：</td>
      <td class="border"> <input name="passs2" type="password" id="passs2"></td>
    </tr>
    <tr> 
      <td class="border">&nbsp;</td>
      <td class="border"> <input type="submit" name="Submit" value="提交">
      <input name="action" type="hidden" id="action" value="add"></td>
    </tr>
  </table>
</form>

<?php
}
}

  
function modify(){
checkadminisdo("adminmanage");
$action = isset($_POST['action'])?$_POST['action']:'';

global $admins,$groupid;
checkid($groupid);

$FoundErr=0;
$ErrMsg="";
if ($action=="modify"){

query("update zzcms_admin set groupid='$groupid' where admin='".$admins."'");
echo "<SCRIPT language=JavaScript>alert('修改成功！');location.href='?'</SCRIPT>";	
}else{
$sql="select * from zzcms_admin where admin='" . $admins . "'";
$rs = query($sql);
$row= fetch_array($rs);
?>
<div class="admintitle">修改管理员信息</div>
<FORM name="form1" action="?do=modify" method="post" onSubmit="return CheckForm()">
          
  <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="36%" align="right" class="border">管理员：</td>
      <td width="64%" class="border"><?php echo $admins?>
      <input name="admins" type="hidden" value="<?php echo $admins?>"></td>
    </tr>
    <tr> 
      <td align="right" class="border">所属用户组：</td>
      <td class="border"> <select name="groupid">
          <?php
	$sqln="Select * from zzcms_admingroup order by id asc";
	$rsn =query($sqln,$conn);
	$rown= num_rows($rsn);
	if ($rown){
		while($rown=fetch_array($rsn)){
			if  ($rown["id"]==$row["groupid"]) {
	 		echo "<option value='".$rown["id"]."' selected>".$rown["groupname"]."</option>";
			}else{
			echo "<option value='".$rown["id"]."'>".$rown["groupname"]."</option>";
			}
		}
	}
		 ?>
        </select> </td>
    </tr>
    <tr> 
      <td align="center" class="border">&nbsp; </td>
      <td class="border"> <input name="Submit"   type="submit" id="Submit" value="保存"> 
        <input name="action" type="hidden" id="action" value="modify"> </td>
    </tr>
  </table>
</form>
<?php
}
}


function pwd(){
global $admins,$passs;
checkadminisdo("adminmanage");
$action = isset($_POST['action'])?$_POST['action']:'';
$FoundErr=0;
$ErrMsg="";
if ($action=="modify"){

	if($admins==''|| $passs==''){
	showmsg('用户名和密码不能为空');
	}
	
	$sql="select * from zzcms_admin where admin='" . $admins . "'";
	$rs = query($sql);
	$row= fetch_array($rs);
	$oldpassword=md5($_POST["oldpassword"]);
	$passs=md5($_POST["passs"]);
	$passs2=$_POST["passs2"];
	if ($oldpassword!=$row["pass"]) {
	$FoundErr=1;
	$ErrMsg=$ErrMsg . "<li>你输入的旧密码不对，没有权限修改！</li>";
	}
	if ($FoundErr==1){
	WriteErrMsg($ErrMsg);
	}else{
	query("update zzcms_admin set pass='$passs' where admin='".$admins."'");
	echo "<SCRIPT language=JavaScript>alert('修改成功！');history.go(-1)</SCRIPT>";
	}
}else{
?>


<div class="admintitle">修改管理员密码</div>
<FORM name="form1" action="?do=pwd" method="post" onSubmit="return checkform()">     
  <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="36%" align="right" class="border">管理员：</td>
      <td width="64%" class="border"><?php echo $admins?>
      <input name="admins" type="hidden" value="<?php echo $admins?>"></td>
    </tr>
    <tr> 
      <td align="right" class="border">旧密码：</td>
      <td class="border"> <INPUT  type="password" maxLength="16" size="30" name="oldpassword">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">新密码：</td>
      <td class="border"> <INPUT  type="password" maxLength="16" size="30" name="passs">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">确认新密码：</td>
      <td class="border"> <INPUT name="passs2"   type="password"  size="30" maxLength="16"></td>
    </tr>
    <tr> 
      <td align="center" class="border">&nbsp;</td>
      <td class="border"> <input name="Submit"   type="submit" id="Submit" value="保存">
        <input name="action" type="hidden" id="action" value="modify">      </td>
    </tr>
  </table>
</form>

<?php
}
}
?>  
</body>
</html>