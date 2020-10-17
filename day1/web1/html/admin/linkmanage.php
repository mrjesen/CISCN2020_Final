<?php
include("admin.php");
include("../inc/fy.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
</head>
<body>
<?php
checkadminisdo("friendlink");
$action=isset($_GET["action"])?$_GET["action"]:'';
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);
$shenhe=isset($_POST["shenhe"])?$_POST["shenhe"]:'';
$keyword=isset($_POST["keyword"])?$_POST["keyword"]:'';
$b=isset($_GET["b"])?$_GET["b"]:0;
checkid($b,1);
if ($action<>""){
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$_POST['id'][$i];
	checkid($id);
	switch ($action){
	case "pass";
	$sql="select passed from zzcms_link where id ='$id'";
	$rs = query($sql); 
	$row = fetch_array($rs);
		if ($row['passed']=='0'){
		query("update zzcms_link set passed=1 where id ='$id'");
		}else{
		query("update zzcms_link set passed=0 where id ='$id'");
		}
	break;	
	case "elite";
	$sql="select elite from zzcms_link where id ='$id'";
	$rs = query($sql); 
	$row = fetch_array($rs);
		if ($row['elite']=='0'){
		query("update zzcms_link set elite=1 where id ='$id'");
		}else{
		query("update zzcms_link set elite=0 where id ='$id'");
		}
	break;
	//case "del";
	//query("delete from zzcms_link where id ='$id'");
	//break;	
	}	
	}
}else{
echo "<script>alert('操作失败！至少要选中一条信息。');history.back()</script>";
}
echo "<script>location.href='?keyword=".$keyword."&page=".$page."'</script>";	
}
?>
<div class="admintitle">友情链接管理</div>
<div class="border2">
<span style="float:right">
			<form name="form1" method="post" action="?">
			网站名称： 
              <input name="keyword" type="text" id="keyword" value="<?php echo $keyword?>"> 
              <input type="submit" name="Submit" value="查找">
			  </form>
            </span>
           <input name="submit3" type="submit" class="buttons" onClick="javascript:location.href='link.php?do=add'" value="添加友情链接">
</div>
<?php
$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_link where id<>0 ";
$sql2='';
if ($shenhe=="no") {  		
$sql2=$sql2." and passed=0 ";
}
if ($b<>"") {
$sql2=$sql2. " and bigclassid ='".$b."' ";
}
if ($keyword<>"") {
$sql2=$sql2. " and sitename like '%".$keyword."%' ";
}
$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];    
$totlepage=ceil($totlenum/$page_size);

$sql="select * from zzcms_link where id<>0 ";
$sql=$sql.$sql2;
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo "暂无信息";
}else{
?>
<form name="myform" id="myform" method="post" action="" onSubmit="return anyCheck(this.form)">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr class="trtitle"> 
      <td width="2%" align="center"><label for="chkAll" style="cursor: pointer;">全选</label></td>
      <td width="5%">类型</td>
      <td width="10%">网站名称</td>
      <td width="10%">logo</td>
      <td width="10%">网站描述</td>
      <td width="10%">申请时间</td>
      <td width="5%" align="center">信息状态</td>
      <td width="5%" align="center">操作</td>
    </tr>
<?php
while($row = fetch_array($rs)){
?>
	<tr class="trcontent">  
      <td align="center" > <input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>"></td>
      <td ><a href="?b=<?php echo $row["bigclassid"]?>">
	  <?php
	  $rsn=query("select classname from zzcms_linkclass where classid=".$row["bigclassid"]." ");
	  $rown=fetch_array($rsn);
	  echo $rown["classname"]?></a></td>
      <td><b><?php echo $row["sitename"]?></b><br> 
        <a href="<?php echo $row["url"]?>" target="_blank"><?php echo $row["url"]?></a><br> 
            </td>
      <td>
	   <?php if ($row["logo"]<>""){?>
        <img src="<?php echo $row["logo"]?>" width="150" height="50"> 
        <?php }else{
		  echo "未填写LOGO地址";
		  }
		 ?> 
	  </td>
      <td><?php echo $row["content"]?></td>
      <td><?php echo $row["sendtime"]?></td>
      <td align="center" > 
<?php if ($row["passed"]==1) { echo"已审核";} else{ echo"<font color=red>未审核</font>";}?><br><?php if ($row["elite"]==1) { echo"已推荐";} else{ echo"未推荐";}?></td>
      <td align="center" class="docolor"><a href="link.php?do=modify&id=<?php echo $row["id"]?>&page=<?php echo $page ?>">修改</a>      </td>
    </tr>
<?php
}
?>
  </table>
      <div class="border"><input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
        <label for="chkAll" style="cursor: pointer;">全选</label>
        <input  type="submit" onClick="myform.action='?action=pass'" value="【取消/审核】选中的信息">
        <input  type="submit" onClick="myform.action='?action=elite'" value="【取消/推荐】选中的信息">
      <input  type="submit" onClick="myform.action='del.php';myform.target='_self';return ConfirmDel()" value="删除选中的信息">
	   <input name="pagename" type="hidden"  value="linkmanage.php?b=<?php echo $b?>&shenhe=<?php echo $shenhe?>&page=<?php echo $page ?>">
      <input name="tablename" type="hidden"  value="zzcms_link">
	  <input name="page" type="hidden" id="page" value="<?php echo $page?>">
      <input name="shenhe" type="hidden" id="shenhe" value="<?php echo $shenhe?>">
      </div>
<div class="border center"><?php echo showpage_admin()?></div>
</form>
<?php
}

?>
</body>
</html>