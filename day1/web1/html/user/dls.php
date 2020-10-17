<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
<title><?php echo channeldl."留言"?></title>
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
$lxr = isset($_POST['lxr'])?$_POST['lxr']:"";
?>
<div class="content">
<div class="admintitle">
<span>
<form name="form1" method="post" action="?">
联系人姓名：<input name="lxr" type="text" id="lxr2" value="<?php echo $lxr?>"> 
<input type="submit" name="Submit" value="查找">
</form>
</span>
<?php echo channeldl."留言"?></div>
<?php
$page=isset($_GET["page"])?$_GET['page']:1;
checkid($page);
if (isset($_GET['page_size'])){
$page_size=$_GET['page_size'];
}else{
$page_size=pagesize_ht;  //每页多少条数据
}
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_dl where passed=1 and del=0 and saver='".$username."' ";
$sql2='';
if ($lxr<>"") {
$sql2=$sql2."and name like '%".$lxr."%' ";
}

$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];
$totlepage=ceil($totlenum/$page_size);

$sql="select * from zzcms_dl where passed=1 and del=0 and saver='".$username."' ";
$sql=$sql.$sql2;
$sql=$sql." order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo  "暂无信息";
}else{
?>
<form action="" method="post" name="myform" id="myform" onSubmit="return anyCheck(this.form)">
  <table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
    <tr class="trtitle"> 
    <td width="10%" class="border">联系人</td><td width="20%" class="border">意向区域</td><td width="20%" class="border">意向品种</td><td width="20%" class="border">申请时间</td><td width="10%" align="center" class="border">状态</td><td width="10%" align="center" class="border">操作</td><td width="10%" align="center" class="border"><label for="chkAll" style="text-decoration: underline;cursor: hand;">全选</label></td>
	 </tr>
          <?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td><?php echo $row["dlsname"]?></td>
      <td><?php echo $row["province"].$row["city"]?></td>
      <td><?php echo $row["cp"]?></td>
      <td><?php echo $row["sendtime"]?></td>
      <td align="center"><?php if($row["looked"]==0) { echo  "<span class='textbg'>未读</span>";}else{echo  "已读";}?></td>
      <td align="center"><a href="dls_show.php?id=<?php echo $row["id"]?>" target="_blank"><?php echo "查看联系方式"?></a></td>
      <td align="center"><input name="id[]" type="checkbox" id="id[]" value="<?php echo $row["id"]?>" /></td>
    </tr>
    <?php
}
?>
</table>
<div class="fenyei"  >
<?php echo showpage()?> 
          <select name="FileExt" id="FileExt">
          <option selected="selected" value="xls">下载类型</option>
          <option value="xls">excel表格</option>
          <option value="doc">word文件</option>
        </select> <select name="page_size" id="page_size" onChange="MM_jumpMenu('self',this,0)">
          <option value="?page_size=10" <?php if ($page_size==10) { echo "selected";}?>>10条/页</option>
          <option value="?page_size=20" <?php if ($page_size==20) { echo "selected";}?>>20条/页</option>
          <option value="?page_size=50" <?php if ($page_size==50) { echo "selected";}?>>50条/页</option>
          <option value="?page_size=100" <?php if ($page_size==100) { echo "selected";}?>>100条/页</option>
          <option value="?page_size=200" <?php if ($page_size==200) { echo "selected";}?>>200条/页</option>
        </select>
<input type="submit" class="buttons"  value="打印" onClick="myform.action='dls_print.php';myform.target='_blank'" />
<input type="submit" class="buttons"  value="下载" onClick="myform.action='dls_download.php';myform.target='_blank'" />
<input type="submit" class="buttons" value="删除" onClick="myform.action='del.php';myform.target='_self';return ConfirmDel()" > 
              <input name="pagename" type="hidden" value="dls.php?page=<?php echo $page ?>" /> 
              <input name="tablename" type="hidden" id="tablename" value="zzcms_dl" />
              <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox" />
              <label for="chkAll">全选</label> 
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