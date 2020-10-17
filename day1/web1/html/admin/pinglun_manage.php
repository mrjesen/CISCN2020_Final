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
checkadminisdo("zxpinglun");

$action=isset($_REQUEST["action"])?$_REQUEST["action"]:'';
if( isset($_GET["page"]) && $_GET["page"]!="") {$page=$_GET['page'];}else{$page=1;}
checkid($page);
$shenhe=isset($shenhe)?$shenhe:'';
$keyword=isset($keyword)?$keyword:'';

if ($action<>""){
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$_POST['id'][$i];
	checkid($id);
	switch ($action){
	case "pass";
	$sql="select passed from zzcms_pinglun where id ='$id'";
	$rs = query($sql); 
	$row = fetch_array($rs);
		if ($row['passed']=='0'){
		query("update zzcms_pinglun set passed=1 where id ='$id'");
		}else{
		query("update zzcms_pinglun set passed=0 where id ='$id'");
		}
	break;	
	case "del";
	query("delete from zzcms_pinglun where id ='$id'");
	break;	
	}	
	}
}else{
echo "<script>alert('操作失败！至少要选中一条信息。');history.back()</script>";
}
echo "<script>location.href='?keyword=".$keyword."&page=".$page."'</script>";	
}

?>
<div class="admintitle">评论管理</div>
<form name="form1" method="post" action="?">
<div class="border"> 内容： <input name="keyword" type="text" id="keyword" value="<?php echo $keyword?>"> <input type="submit" name="Submit" value="查寻"> </div>
</form>
<?php
$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_pinglun where id<>0 ";
$sql2='';
if ($shenhe=="no") {  		
$sql2=$sql2." and passed=0 ";
}
if ($keyword<>"") {
$sql2=$sql2. " and content like '%".$keyword."%' ";
}
$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];  
$totlepage=ceil($totlenum/$page_size);

$sql="select * from zzcms_pinglun where id<>0 ";
$sql=$sql.$sql2;
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo "暂无信息";
}else{
?>
<form name="myform" method="post" action="" onSubmit="return anyCheck(this.form)">
  <table width="100%" border="0" cellspacing="1" cellpadding="5">
    <tr class="trtitle"> 
      <td width="5%" align="center"><label for="chkAll" style="cursor: pointer;">全选</label></td>
      <td width="15%">评论内容</td>
      <td width="15%">被评文章ID</td>
      <td width="15%" align="center">是否审核</td>
      <td width="15%" align="center">发布时间</td>
      <td width="15%" align="center">评论人</td>
      <td width="15%" align="center">评论人IP</td>
    </tr>
<?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td align="center" class="docolor"> <input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>"></td>
      <td ><?php echo $row["content"]?></td>
      <td ><a href="<?php echo getpageurl("zx",$row["about"])?>" target="_blank"><?php echo $row["about"]?></a></td>
      <td align="center" > <?php if ($row["passed"]==1) { echo"已审核";} else{ echo"<font color=red>未审核</font>";}?></td>
      <td align="center"><?php echo $row["sendtime"]?></td>
      <td align="center"><a href="usermanage.php?keyword=<?php echo $row["username"]?>"><?php echo $row["username"]?></a></td>
      <td align="center"><?php echo $row["ip"]?></td>
    </tr>
<?php
}
?>
  </table>
      <div class="border"> <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
        <label for="chkAll" style="cursor: pointer;">全选</label> 
        <input type="submit" onClick="myform.action='?action=pass';myform.target='_self'" value="【取消/审核】选中的信息"> 
        <input type="submit" onClick="myform.action='del.php';myform.target='_self';return ConfirmDel()" value="【删除】选中的信息"> 
		 <input name="tablename" type="hidden"  value="zzcms_pinglun">   
	 <input name="pagename" type="hidden"  value="pinglun_manage.php?page=<?php echo $page ?>">
      </div>
</form>
<div class="border center"><?php echo showpage_admin()?></div>
<?php
}
?>
</body>
</html>