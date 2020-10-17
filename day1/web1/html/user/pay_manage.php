<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/js/gg.js"></script>
</head>
<body>
<div class="main">
<?php
include ("top.php");
?>
<div class="pagebody">
<div class="left">
<?php
include ("left.php");
?>
</div>
<div class="right">
<div class="content">
<div class="admintitle">我的财务记录</div>
<?php
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);

$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select * from zzcms_pay where username='".$username."'";
$rs = query($sql); 
$totlenum= num_rows($rs);  
$totlepage=ceil($totlenum/$page_size);  

$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
$row= num_rows($rs);//返回记录数
if(!$row){
echo "暂无信息";
}else{
?>
 
      <table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
        <tr class="trtitle"> 
        <td width="5%" align="center" class="border">序号</td><td width="20%" class="border">摘要</td><td width="10%" class="border">金币</td><td width="20%" class="border">记帐时间</td><td width="25%" class="border">备注</td> 
        </tr>
        <?php
$i=1;
while($row = fetch_array($rs)){
?>
         <tr class="trcontent"> 
          <td align="center"><?php echo $i?></td>
          <td><?php echo $row["dowhat"]?></td>
          <td><?php echo $row["RMB"]?></td>
          <td><?php echo $row["sendtime"]?></td>
          <td><?php echo $row["mark"]?></td>
        </tr>
        <?php
$i=$i+1;
}
?>
      </table>
<div class="fenyei">
<?php echo showpage()?> 
</div>
<?php
}
?>
</div>
</div>
</div>
</div>
</body>
</html>