<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<title>用户中心</title>
<?php
//接收通过此页跳转页的代码
$gotopage = isset($_GET['gotopage'])?$_GET['gotopage']:'';
//$gotopage=substr($gotopage,0,strpos($gotopage,".php")+4);

$canshu = isset($_GET['canshu'])?$_GET['canshu']:'';
$b = isset($_GET['b'])?$_GET['b']:0;
$s = isset($_GET['s'])?$_GET['s']:0;
?>
<form action="<?php echo $gotopage;?>" method='post' name='gotopage' target='_self' >
<input type='hidden' name='canshu' value='<?php echo $canshu;?>' />
<input type='hidden' name='b' value='<?php echo $b;?>' />
<input type='hidden' name='s' value='<?php echo $s;?>' />
</form>
<?php
$sql="select * from zzcms_user where username='".@$username."'";
$rs=query($sql);
$row=fetch_array($rs);
if ($row["usersf"]=="公司" ){
	if ($row["content"]=="" || $row["content"]=="&nbsp;"){
	 echo "<script>location.href='daohang_company.php'</script>";
	}
}
?>
<SCRIPT>
function gotopage(){
document.gotopage.submit();
}
</SCRIPT>
</head>
<body   <?php if ($gotopage<>""){echo "onLoad='gotopage()'";}?>  >
<div class="main">

<?php
echo $gotopage;
include ("top.php");
?>
<div class="pagebody">
<div class="left">
<?php
include ("left.php");
?>
</div>
<div class="right">
 <?php
$sql="select * from zzcms_message where sendto='' or  sendto='".@$username."'  order by id desc";
$rs=query($sql);
$row=num_rows($rs);
if($row){
$str="<div class='content' style='margin-bottom:10px'><div class='admintitle'>系统信息</div>";
while ($row=fetch_array($rs)){
$str=$str."<div class='box' style='margin-bottom:5px'>";
$str=$str."<div style='font-weight:bold' title='发送时间'>".$row["sendtime"]."</div>";
$str=$str.$row["content"];
$str=$str."</div>";
}
$str=$str."</div>";
echo $str;
}
?>
<div class="content">
<div class="admintitle">用户信息</div>
<table width="100%" border="0" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="13%" height="50" align="right" class="border2">注册时间：</td>
    <td width="87%"  class="border2"> 
	<?php
  $sql="select * from zzcms_user where username='".@$username."'";
  $rs=query($sql);
  $row=fetch_array($rs);
  echo "<b>".@$username ."</b><br>".$row["regdate"];
  ?> </td>
  </tr>
  <tr> 
    <td height="50" align="right"  class="border">您的积分：</td>
    <td  class="border"><b><?php echo $row["totleRMB"]?></b><br>
     说明： <a href="/one/help.php#1032" target="_blank">关于积分</a></td>
  </tr>
  <tr> 
    <td height="50" align="right"  class="border2">登录次数：</td>
    <td  class="border2"><b><?php echo $row["logins"]?></b><br>
    提示：若感到登录次数不对，那么请及时 <a href="managepwd.php" target="_self">[更换登录密码]</a> </td>
  </tr>
  <tr> 
    <td height="50" align="right"  class="border">上次登录IP：</td>
    <td  class="border"><b>
      <?php if ($row["showloginip"]<>"") {echo $row['showloginip'] ;}else{ echo "空" ;}?>
      </b><br>
      提示：若并没有用此IP登录过网站，那么请及时 <a href="managepwd.php" target="_self">[更换登录密码]</a> 
    </td>
  </tr>
  <tr> 
    <td height="50" align="right"  class="border2">上次登录时间：</td>
    <td  class="border2"><b><?php echo $row["showlogintime"]?></b><br>
     提示：若在以上时间并没有登录过网站，那么请及时 <a href="managepwd.php" target="_self">[更换登录密码]</a></td>
  </tr>
</table>
<?php
$sql="select id from zzcms_dl where saver='".@$username."' and looked=0 and del=0 and passed=1";
$rs=query($sql);
$row=num_rows($rs);
if($row){
?>
<script>
if(confirm("有新意向产品留言，要查看么？")) {window.location.href="dls.php" }	
</script>
<div class="box">新意向产品留言 <b><?php echo $row ?></b> 条 [ <a href='dls'>查看</a> ] </div>	 
<embed src="../image/sound.swf" loop="false" hidden="true" volume="50" autostart="true" width="0" height="0"  mastersound="mastersound"></embed>  
<?php
}
$sql="select id from zzcms_guestbook where saver='".@$username."' and looked=0 and passed=1";
$rs=query($sql);
$row=num_rows($rs);
if($row){
?>
<script>
if(confirm("有新的留言本留言，要查看么？")){window.location.href="ztliuyan.php" }	
</script>
<div class="box">新的留言本留言 <b><?php echo $row ?></b> 条 [ <a href='ztliuyan.php'>查看</a> ] </div>	 
<embed src="../image/sound.swf" loop="false" hidden="true" volume="50" autostart="true" width="0" height="0"  mastersound="mastersound"></embed>
<?php
}
?>
</div>
</div>
</div>
</div>
</div>

</body>
</html>
</body>
</html>