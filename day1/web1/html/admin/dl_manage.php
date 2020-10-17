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
<?php
checkadminisdo("dl");

$action=isset($_REQUEST["action"])?$_REQUEST["action"]:'';
$page=isset($page)?$page:1;
checkid($page);

if (!isset($b)){$b=0;}
checkid($b,1);

$shenhe=isset($_REQUEST["shenhe"])?$_REQUEST["shenhe"]:'';
$keyword=isset($_REQUEST["keyword"])?$_REQUEST["keyword"]:'';
$kind=isset($_REQUEST["kind"])?$_REQUEST["kind"]:'';
$showwhat=isset($_REQUEST["showwhat"])?$_REQUEST["showwhat"]:'';

$isread=isset($_REQUEST["isread"])?$_REQUEST["isread"]:'';

if ($action=="pass"){
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$_POST['id'][$i];
	checkid($id);
	$sql="select passed from zzcms_dl where id ='$id'";
	$rs = query($sql); 
	$row = fetch_array($rs);
	if ($row['passed']=='0'){
	query("update zzcms_dl set passed=1 where id ='$id'");
    }else{
	query("update zzcms_dl set passed=0 where id ='$id'");
	}
	}	
}else{
echo "<script lanage='javascript'>alert('操作失败！至少要选中一条信息。');history.back()</script>";
}
echo "<script>location.href='?keyword=".$keyword."&page=".$page."'</script>";
}
?>
</head>
<body>
<div class="admintitle"><?php echo channeldl?>商信息库管理</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="border">
  <tr> 
      <td width="45%"><input name="submit32" type="submit" class="buttons" onClick="javascript:location.href='dl.php?do=add'" value="发布<?php echo channeldl?>信息">      </td>
    <td width="55%" align="right"> 
      <form name="form1" method="post" action="?">
	  <label> <input type="radio" name="kind" value="cpmc" <?php if ($kind=="cpmc") { echo "checked";}?>>
        按产品名称 </label> 
        <label> <input name="kind" type="radio" value="tel" <?php if ($kind=="tel") { echo "checked";}?> >
        按电话 </label> 
        <label> <input type="radio" name="kind" value="editor" <?php if ($kind=="editor") { echo "checked";}?>>
        按发布人</label>  
       <label>  <input type="radio" name="kind" value="saver" <?php if ($kind=="saver") { echo "checked";}?>>
        按接收人 </label> 
        <input name="keyword" type="text" id="keyword2" value="<?php echo $keyword?>"> 
        <input type="submit" name="Submit" value="查找">
        <a href="?isread=no">未查看的</a> 
      </form>		</td>
  </tr>
</table>
  <div class="border">
  <?php	
$str='';
$rs = query("select classid,classname from zzcms_zsclass where parentid=0 order by xuhao"); 
if ($rs){//当不出错时
while($row = fetch_array($rs)){
	$str=$str. "<a href=?b=".$row['classid'].">";  
	if ($row["classid"]==$b) {
	$str=$str."<b>".$row["classname"]."</b>";
	}else{
	$str=$str.$row["classname"];
	}
	$str=$str."</a>";  
}
} 
if ($str==""){echo '暂无分类';}else{echo $str;}
 ?>
  </div>
 
<?php
$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;

$sql2='';
if ($shenhe=="no") {  		
$sql2=$sql2." and passed=0 ";
}
if ($b<>0) {
$sql2=$sql2." and classid='".$b."'";
}
if ($isread=="no") {
$sql2=$sql2." and saver<>'' and looked=0";
}
if ($keyword<>"") {
	switch ($kind){
	case "editor";
	$sql2=$sql2. " and editor like '%".$keyword."%' ";
	break;
	case "cpmc";
	$sql2=$sql2. " and cp like '%".$keyword."%'";
	break;
	case "saver";
	$sql2=$sql2. " and saver like '%".$keyword."%'";
	break;
	case "tel";
	$sql2=$sql2. " and tel like '%".$keyword."%'";
	break;		
	default:
	$sql2=$sql2. " and cp like '%".$keyword."%'";
	}
}
$sql="select count(*) as total from zzcms_dl where id<>0 ";
$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];
$totlepage=ceil($totlenum/$page_size);

$sql="select * from zzcms_dl where id<>0 ";
$sql=$sql.$sql2;
$sql=$sql . " order by id desc limit $offset,$page_size";
//$sql=$sql." and id>=(select id from zzcms_dl order by id limit $offset,1) order by id desc limit $page_size";
$rs = query($sql); 
if(!$totlenum){
echo "暂无信息";
}else{
?>
<form name="myform" id="myform" method="post" action="" onSubmit="return anyCheck(this.form)">

      <div class="border2"> 
        <input name="submit" type="submit" onClick="myform.action='dl_sendmail.php';myform.target='_blank' "  value="给接收者发邮件提醒">
        <input name="submit23" type="submit" onClick="myform.action='dl_sendsms.php';myform.target='_blank' "  value="给接收者发手机短信提醒">
        <input name="submit4" type="submit"  onClick="myform.action='?action=pass';myform.target='_self'" value="【取消/审核】选中的信息"> 
        <input type="submit" onClick="myform.action='del.php';myform.target='_self';return ConfirmDel()" value="删除选中的信息">
        <input name="pagename" type="hidden"  value="dl_manage.php?b=<?php echo $b?>&shenhe=<?php echo $shenhe?>&page=<?php echo $page ?>"> 
        <input name="tablename" type="hidden"  value="zzcms_dl"> </div>

  <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr class="trtitle"> 
      <td width="4%" align="center"> <label for="chkAll" style="cursor: pointer;">全选</label></td>
      <td width="10%">类别</td>
      <td width="10%"><?php echo channeldl?>品种</td>
      <td width="10%"><?php echo channeldl?>区域</td>
      <td width="10%">联系人</td>
      <td width="10%">电话</td>
      <td width="10%">发布人</td>
      <td width="10%" align="center">接收者</td>
      <td width="10%">发布时间</td>
      <td width="10%" align="center">信息状态</td>
      <td width="5%" align="center">操作</td>
    </tr>
    <?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td align="center"> <input name="id[]" type="checkbox"  value="<?php echo $row["id"]?>">
     </td>
      <td><a href="?b=<?php echo $row["classid"]?>">
	  <?php
			$rsn=query("select classname from zzcms_zsclass where classid='".$row['classid']."'");
			if ($rsn){
			$r=fetch_array($rsn);
			echo $r["classname"];
			}
			 ?>
      </a></td>
      <td><a href="<?php echo getpageurl("dl",$row["id"])?>" target="_blank"><?php echo $row["cp"] ?></a></td>
      <td><?php echo $row["province"].$row["city"]?></td>
      <td><?php echo $row["dlsname"]?></td>
      <td><?php echo $row["tel"]?></td>
      <td><?php if ($row["editor"]<>''){ echo  $row["editor"];}else{ echo '未登录用户';}?></td>
      <td align="center">
        <?php if ($row["saver"]<>"") { echo"<a href='usermanage.php?keyword=".$row["saver"]."' target='_blank'>".$row["saver"]."</a>";}else{ echo"无";}?>      </td>
      <td><?php echo date("Y-m-d",strtotime($row["sendtime"]))?></td>
      <td align="center"> 
        <?php if ($row["passed"]==1) { echo"已审核";} else{ echo"<font color=red>未审核</font>";}?>
       |
        <?php
	if ($row["saver"]<>"") {
		if ($row["looked"]==0) { 
		echo"<font color='red'>未查看</font>" ;
		}else{
		echo "已查看" ;
		}
	}else{
	echo '非留言';
	}
		?>      </td>
      <td align="center" class="docolor"> <a href="dl.php?do=modify&id=<?php echo $row["id"]?>&page=<?php echo $page ?>">修改</a>      </td>
    </tr>
    <?php
}
?>
  </table>
      <div class="border"> 
        <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
         <label for="chkAll" style="cursor: pointer;">全选/不选</label>
        <input name="submit2" type="submit" onClick="myform.action='dl_sendmail.php';myform.target='_blank' "  value="给接收者发邮件提醒">
        <input name="submit232" type="submit" onClick="myform.action='dl_sendsms.php';myform.target='_blank' "  value="给接收者发手机短信提醒">
        <input name="submit5" type="submit"  onClick="myform.action='?action=pass';myform.target='_self'" value="【取消/审核】选中的信息"> 
        <input name="submit3" type="submit" onClick="myform.action='del.php';myform.target='_self';return ConfirmDel()" value="删除选中的信息"> 
      </div>
</form>
<div class="border center"><?php echo showpage_admin()?></div>
<?php
}
?>
</body>
</html>