<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<title>投标</title>
<script language="javascript" src="../js/timer.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.eliteendtime.value==""){
	document.myform.eliteendtime.focus();
    alert('请选择到期时间');
	return false;
}
if (document.myform.tag.value==""){
	document.myform.tag.focus();
    alert('关键词不能为空');
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
<div class="left">
<?php
include("left.php");
?>
</div>
<div class="right">
<?php
$err=0;
$action = isset($_POST['action'])?$_POST['action']:'';

$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);

$id = isset($_REQUEST['id'])?$_REQUEST['id']:'0';
checkid($id,1);//允许为0

if ($action=='modify'){

if (isset($_POST["eliteendtime"])){
$eliteendtime=$_POST["eliteendtime"];
}

if (strtotime($eliteendtime)<=time()){
$err=1;
$errmsg='时间已过期';
}

if (isset($_POST["oldeliteendtime"])){
$oldeliteendtime=$_POST["oldeliteendtime"];
	if (strtotime($oldeliteendtime)<time()){//设过值，过期了
	$oldeliteendtime=date('Y-m-d');
	}
}else{
$oldeliteendtime=date('Y-m-d');//没有设过值的
}

if (isset($_POST["tag"])){
$tag=$_POST["tag"];
}

$sql="select id,proname,eliteendtime,tag from zzcms_main where tag='".$tag."' and id<>'$id'";
$rs = query($sql); 
$row=num_rows($rs);
if ($row){
$row = fetch_array($rs);
$err=1;
$errmsg="此关键词已有中标产品:<a href='/zs/search.php?keyword=".$row['tag']."'>".$row['proname']."</a><br>中标期限至：".$row['eliteendtime'];
}

if ($err==1){
WriteErrMsg($errmsg);
}else{
$day=floor((strtotime($eliteendtime)-strtotime($oldeliteendtime))/(24*3600));//按到期时间计费，这样改关键词可免费，续期只收续期的费用
$jfpay=$day*jf_set_elite;
if ($jfpay<0){ $jfpay=0; }
//echo $jfpay;
switch (check_user_power('set_elite')){
case 'yes':
if (jifen=="Yes"){
$sqln="select totleRMB from zzcms_user where username='".$username."'";
$rsn =query($sqln);
$rown = fetch_array($rsn);
	if ($rown["totleRMB"]>=$jfpay){
	query("update zzcms_user set totleRMB=totleRMB-$jfpay where username='".$username."'");
	query("update zzcms_main set elitestarttime='".date('Y-m-d')."',eliteendtime='$eliteendtime',tag='$tag',elite=1 where id='$id'");
	query("insert into zzcms_pay (username,dowhat,RMB,mark,sendtime) values('$username','".channelzs."信息投标','-$jfpay','产品ID：<a href=zsmanage.php?id=$id>$id</a>','".date('Y-m-d H:i:s')."')");
	echo "<script>alert('成功 \\n计费时间：".$oldeliteendtime."至".$eliteendtime."\\n共计".$day."天，".jf_set_elite."个金币/天，共付出".$jfpay."个金币');</script>";
	echo "<script>location.href='zsmanage.php?page=".$page."'</script>";
	}else{
	echo "<script>alert('失败，你的金币不足".$jfpay."';history.back()</script>";
	}			
}elseif (jifen=="No") {
echo "<script>alert('积分功能关闭，无法使用此功能！');history.back(-1)</script>";
}
break;
case 'no':
echo "<script>alert('你所在的用户组没有此权限');history.back()</script>";
}

}
}else{

$sql="select id,editor,proname,eliteendtime,tag from zzcms_main where id='$id'";
$rs = query($sql); 
$row = fetch_array($rs);
if ($row["editor"]<>$username) {
markit();
echo  "<script>alert('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');history.back()</script>";
exit;
}
?>
<div class="content">
<div class="admintitle">投标</div>
<form action="?" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="18%" align="right" class="border" >产品名称：</td>
            <td width="82%" class="border" > <?php echo $row["proname"]?></td>
          </tr>
          <tr> 
            <td align="right" class="border" >到期时间：</td>
            <td class="border" > <input name="eliteendtime" type="text" id="eliteendtime"  class="biaodan" value="<?php echo $row["eliteendtime"]?>" size="30" maxlength="45" onFocus="JTC.setday(this)">
              <input name="oldeliteendtime" type="hidden"  value="<?php echo $row["eliteendtime"]?>" size="30" maxlength="45" />
              <?php echo jf_set_elite?>积分/天</td>
          </tr>
          
          <tr> 
            <td align="right" class="border" >关键词（tag）：</td>
            <td class="border" > <input name="tag" type="text" id="tag" class="biaodan" value="<?php echo $row["tag"] ?>" size="20" maxlength="20">
              (多个关键词以“,”隔开)</td>
          </tr>
          <tr> 
            <td align="center" class="border" >&nbsp;</td>
            <td class="border" > <input name="id" type="hidden"  value="<?php echo $row["id"] ?>"> 
              <input name="action" type="hidden"  value="modify">
              <input name="page" type="hidden" id="page" value="<?php echo $_GET["page"]?>" />
              <input name="Submit" type="submit" class="buttons" value="提交"></td>
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