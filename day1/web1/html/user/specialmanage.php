<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>专题信息管理</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
<?php
if (str_is_inarr(usergr_power,'special')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限');
}
?>
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
<div class="admintitle">
<span><form name="form1" method="post" action="?">
标题：<input name="keyword" type="text" id="keyword"> <input type="submit" name="Submit" value="查找"></form>
</span>专题信息管理</div>
<?php
$bigclassid=isset($_GET["bigclassid"])?$_GET["bigclassid"]:'';
$keyword=isset($_POST["keyword"])?trim($_POST["keyword"]):'';
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);

$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_special where editor='".$username."' ";
$sql2='';
if ($bigclassid!=""){
checkid($bigclassid);
$sql2=$sql2." and bigclassid='".$bigclassid."'  ";
}
if ($keyword!=""){
$sql2=$sql2." and title like '%".$keyword."%' ";
}

$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];  
$totlepage=ceil($totlenum/$page_size);

$sql="select * from zzcms_special where editor='".$username."' ";	
$sql=$sql.$sql2;
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo '暂无信息';
}else{
?>
<form name="myform" method="post" action="del.php" onSubmit="return anyCheck(this.form)">
        <table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
          <tr class="trtitle"> 
          <td width="20%" class="border">标题</td>
		  <td width="20%" align="center" class="border">所属类别</td>
		  <td width="20%" align="center" class="border">更新时间</td>
		  <td width="10%" align="center" class="border">状态</td>
		  <td width="10%" align="center" class="border">点击</td>
		  <td width="10%" align="center" class="border">操作</td>
		  <td width="10%" align="center" class="border">删除</td>
          </tr>
          <?php
while($row = fetch_array($rs)){
?>
          <tr class="trcontent"> 
            <td><a href="<?php echo getpageurl("special",$row["id"])?>" target="_blank"><?php echo $row["title"]?></a>            </td>
            <td align="center"> 
			<a href="?bigclassid=<?php echo $row["bigclassid"]?>"><?php echo $row["bigclassname"]?></a> 
              - <?php echo $row["smallclassname"]?>            </td>
            <td align="center"><?php echo $row["sendtime"]?></td>
            <td align="center"> 
              <?php 
	if ($row["passed"]==1 ){ echo '已审';}else{ echo '<font color=red>待审</font>';}
	  ?>            </td>
            <td align="center"><?php echo $row["hit"]?></td>
            <td align="center"> 
           <a href="special.php?do=modify&id=<?php echo $row["id"]?>&page=<?php echo $page?>&bigclassid=<?php echo $bigclassid?>">修改</a></td>
            <td align="center"><input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>" /></td>
          </tr>
          <?php
}
?>
        </table>

<div class="fenyei">
<?php echo showpage('yes')?> 
 <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox" />
          <label for="chkAll">全选</label>
<input name="submit"  type="submit" class="buttons"  value="删除" onClick="return ConfirmDel()"> 
<input name="pagename" type="hidden" id="page2" value="specialmanage.php?page=<?php echo $page ?>"> 
<input name="tablename" type="hidden" id="tablename" value="zzcms_special"> 
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