<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>品牌信息管理</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
if (str_is_inarr(usergr_power,'pp')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限');
}
?>
<script language="JavaScript" src="../js/gg.js"></script>
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
$cpmc=isset($_POST["cpmc"])?$_POST["cpmc"]:"";
$bigclass=isset($_GET["bigclass"])?$_GET["bigclass"]:0;
checkid($bigclass);
?>
<div class="content">
<div class="admintitle">
<span>
<form name="form1" method="post" action="?">
名称：
<input name="cpmc" type="text" id="cpmc"> <input type="submit" name="Submit" value="查找" />      
 </form>
</span>
品牌信息管理</div>
<?php
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);

$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_pp where editor='".$username."' ";
$sql2='';
if (isset($cpmc)){
$sql2=$sql2 . " and ppname like '%".$cpmc."%' ";
}
if ($bigclass<>""){
$sql2=$sql2 . " and bigclassid ='".$bigclass."'";
}

$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total']; 
$totlepage=ceil($totlenum/$page_size);	

$sql="select * from zzcms_pp where editor='".$username."' ";
$sql=$sql .$sql2;	
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo '暂无信息';
}else{
?>
<form name="myform" method="post" action="del.php" onSubmit="return anyCheck(this.form)">
<table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
    <tr class="trtitle"> 
    <td width="10%" class="border">名称</td><td width="5%" align="center" class="border">所属类别</td><td width="5%" height="25" align="center" class="border">图片</td><td width="5%" align="center" class="border">更新时间</td><td width="5%" align="center" class="border">信息状态</td><td width="5%" align="center" class="border">修改</td><td width="5%" align="center" class="border">删除</td>
    </tr>
<?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td><a href="<?php echo getpageurl("pp",$row["id"])?>" target="_blank"><?php echo $row["ppname"]?></a> </td>
      <td align="center">
	  <?php
	$sqln="select classname from zzcms_zsclass where classid='".$row["bigclassid"]."' ";
	$rsn =query($sqln); 
	$rown = fetch_array($rsn);
	echo $rown["classname"];
	$sqln="select classname from zzcms_zsclass where classid='".$row["smallclassid"]."' ";
	$rsn =query($sqln); 
	$rown = fetch_array($rsn);
	echo "<br/>".$rown["classname"];
	  ?>	  </td>
      <td align="center"><a href="<?php echo $row["img"] ?>" target='_blank'><img src="<?php echo $row["img"] ?>" width="60" height="60" border="0"></a></td>
      <td align="center"><?php echo $row["sendtime"]?></td>
      <td align="center"> 
	  <?php 
	if ($row["passed"]==1 ){ echo '已审';}else{ echo '<font color=red>待审</font>';}
	
	  ?> </td>
            <td align="center" class="docolor"> 
              <a href="pp.php?do=modify&id=<?php echo $row["id"]?>&page=<?php echo $page?>">修改</a></td>
            <td align="center" class="docolor"><input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>" /></td>
    </tr>
<?php
}
?>
  </table>
<div class="fenyei">
<?php echo showpage()?> 
          <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox" />
          <label for="chkAll">全选</label>
          <input name="submit"  type="submit" class="buttons"  value="删除" onClick="return ConfirmDel()" />
          <input name="pagename" type="hidden" id="pagename" value="ppmanage.php?page=<?php echo $page ?>" />
          <input name="tablename" type="hidden" id="tablename" value="zzcms_pp" />
        </div>
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