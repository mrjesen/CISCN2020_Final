<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/js/gg.js"></script>
<style type="text/css">
<!--
.x {
	border-top-width: 1px;
	border-top-style: solid;
	border-top-color: #CCCCCC;
	background-color: #FFFFFF;
	border-left-style: solid;
	border-left-color: #CCCCCC;
	border-left-width: 1px;
}
-->
</style>
</head>
<body>
<div class="main">
<?php
if (check_user_power("dls_print")=="no"){
echo "<div class='box center'> 提示：您所在的用户组没有此权限！<br><br><input type='button' class='buttons'  value='升级成VIP会员' onClick='location.href=/one/vipuser.php'/></div>";
exit;
}
?>
<div class="box center"><a href="javascript:window.print()"><img src="/image/ico-dy.gif" width="18" height="17" border="0"> 打印</a></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"> 
      <?php
$id="";
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$id.($_POST['id'][$i].',');
    }
	$id=substr($id,0,strlen($id)-1);//去除最后面的","
}

if (!isset($id) || $id==""){
echo "<script lanage='javascript'>alert('操作失败！\\n至少要选中一条信息。');window.opener=null;window.close()</script>";
exit;
}
 	if (strpos($id,",")>0){
	//$sql="select * from zzcms_dl where passed=1 and saver='".$username."' and id in (". $id .") and id in(select max(id) from zzcms_dl group by tel)";
	$sql="select * from zzcms_dl where passed=1 and saver='".$username."' and id in (". $id .")";
	}else{
	$sql="select * from zzcms_dl where passed=1 and saver='".$username."' and id='$id' order by id desc";
	}
	
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "暂无信息";
}else{
?>
      <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr> 
        <td width="6%" height="30" align="center" class="x"><strong>序号</strong></td><td width="6%" class="x"><strong>联系人</strong></td><td width="14%" height="30" class="x"><strong>意向区域</strong></td><td width="20%" height="30" class="x"><strong>意向品种</strong></td><td width="34%" height="30" class="x"><strong>联系方式</strong></td><td width="20%" height="30" class="x"><strong>申请时间</strong></td>
        </tr>
        <?php
$i=1;		
while ($row=fetch_array($rs)){
?>
        <tr> 
          <td width="6%" height="30" align="center" class="x"><?php echo $i?> </td>
          <td width="6%" height="30" class="x"><?php echo $row['dlsname']?></td>
          <td width="14%" height="30" class="x"><?php echo $row['province'].$row['city']?></td>
          <td width="20%" height="30" class="x"><?php echo $row['cp']?></td>
          <td width="34%" height="30" class="x"><?php echo "电话：". $row['tel']."E-mail：".$row['email']?></td>
          <td width="20%" height="30" class="x"> <?php echo $row['sendtime']?></td>
        </tr>
<?php
$i++;
}
?>
      </table>
      
<?php
}	
?>
    </td>
  </tr>
</table>
</div>
</body>
</html>