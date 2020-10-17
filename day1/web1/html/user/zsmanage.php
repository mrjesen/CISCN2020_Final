<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo channelzs?>信息管理</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
if (str_is_inarr(usergr_power,'zs')=="no" && $usersf=='个人'){
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
产品名称：<input name="cpmc" type="text" id="cpmc" > 
<input name="Submit" type="submit" value="查找"> <input name="Submit2" type="button" class="buttons"  onClick="javascript:location.href='zsmanage.php?action=refresh'" value="一键刷新" title='一键刷新功能可使您的信息排名靠前，以提高被查看的机率。'>
</form>
</span>
<?php echo channelzs?>信息管理</div>
<?php
$sql="select refresh_number,groupid from zzcms_usergroup where groupid=(select groupid from zzcms_user where username='".$username."')";
$rs = query($sql); 
if(empty($rs)){
$refresh_number=3;
}else{
$row = fetch_array($rs);
$refresh_number=$row["refresh_number"];
}

$action=isset($_REQUEST["action"])?$_REQUEST["action"]:"";

$sql="select refresh,sendtime from zzcms_main where editor='".$username."' ";
$rs = query($sql);
$row = fetch_array($rs);
if ($action=="refresh") {
    if ($row["refresh"]< $refresh_number){
	query("update zzcms_main set sendtime='".date('Y-m-d H:i:s')."',refresh=refresh+1 where editor='".$username."'");
	echo "<script>alert('操作成功');self.location='?';</script>";
    }else{
	echo "<script>alert('操作失败！一天内只允许刷新 ".$refresh_number."次！');history.back(-1);</script>";
    }
}else{
	if (strtotime(date("Y-m-d H:i:s"))-strtotime($row['sendtime'])>12*3600){
	query("update zzcms_main set refresh=0 where editor='".$username."'");
  	}
}

$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);

$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_main where editor='".$username."' ";
$sql2='';
if (isset($cpmc)){
$sql2=$sql2 . " and proname like '%".$cpmc."%' ";
}
if ($bigclass<>0){
$sql2=$sql2 . " and bigclassid ='".$bigclass."'";
}

$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];
$totlepage=ceil($totlenum/$page_size);

$sql="select id,bigclassid,smallclassid,smallclassids,proname,refresh,img,province,city,xiancheng,sendtime,elite,passed,elitestarttime,eliteendtime,tag from zzcms_main where editor='".$username."' ";
$sql=$sql.$sql2;		
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo '暂无信息';
}else{
?>
<form name="myform" method="post" action="del.php"  onSubmit="return anyCheck(this.form)">
<table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
    <tr class="trtitle"> 
     <td width="20%" class="border">产品名称</td><td width="10%" align="center" class="border">所属类别</td><td width="10%" height="25" align="center" class="border">产品图片</td><td width="10%" align="center" class="border">招商区域</td><td width="10%" align="center" class="border">更新时间</td><td width="7%" align="center" class="border">已刷新</td><td width="10%" align="center" class="border">信息状态</td><td width="13%" align="center" class="border">操作</td><td width="5%" align="center" class="border">删除</td>
    </tr>
<?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td><a href="<?php echo getpageurl("zs",$row["id"])?>" target="_blank"><?php echo $row["proname"]?></a> </td>
      <td align="center">
	  <?php
	$sqln="select classname from zzcms_zsclass where classid='".$row["bigclassid"]."' ";
	$rsn =query($sqln); 
	$rown = fetch_array($rsn);
	echo $rown["classname"]."<br/>";
	
	if (strpos($row["smallclassids"],",")>0){
	$sqln="select classname from zzcms_zsclass where parentid='".$row["bigclassid"]."' and classid in (".$row["smallclassids"].") ";
	$rsn =query($sqln);
	while($rown = fetch_array($rsn)){
	echo " [".$rown["classname"]."]";
	}
	}else{
	$sqln="select classname from zzcms_zsclass where classid='".$row["smallclassid"]."' ";
	$rsn =query($sqln); 
	$rown = fetch_array($rsn);
	echo $rown["classname"];
	}
	  ?>	  </td>
      <td align="center"><a href="<?php echo $row["img"] ?>" target='_blank'><img src="<?php echo $row["img"] ?>" width="60"></a></td>
      <td align="center" title='<?php echo $row["city"]?>'> 
	  <?php echo $row["province"].$row["city"]?>        </td>
      <td align="center"><?php echo $row["sendtime"]?></td>
      <td align="center"><?php echo $row["refresh"]?></td>
      <td align="center"> 
	  <?php 
	if ($row["passed"]==1 ){ echo  '已审';}else{ echo  '<font color=red>待审</font>';}
	if ($row["elite"]<>0) { echo  "<br><font color=green title='中标关键词:".$row["tag"]."中标时间:".$row["elitestarttime"]."至".$row["eliteendtime"]."'>中标</font>";}
	  ?> </td>
            <td align="center" class="docolor"> 
              <a href="zs.php?do=modify&id=<?php echo $row["id"]?>&page=<?php echo $page?>">修改</a> 
              | <a href="zspx.php" target="_self">排序</a>| <a href="zs_elite.php?id=<?php echo $row["id"]?>&page=<?php echo $page?>">投标</a>		    </td>
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
          <input name="pagename" type="hidden" id="pagename" value="zsmanage.php?page=<?php echo $page ?>" />
          <input name="tablename" type="hidden" id="tablename" value="zzcms_main" />
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