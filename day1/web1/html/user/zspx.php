<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>排序</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
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

if (@$_REQUEST["action"]=="px"){
$sql="select xuhao,id from zzcms_main where editor='".$username."'";
$rs = query($sql); 
while($row = fetch_array($rs)){
$xuhao=$_POST["xuhao".$row["id"]];//表单名称是动态显示的，并于FORM里的名称相同。
	   if (trim($xuhao) == "" || is_numeric($xuhao) == false) {
	       $xuhao = 0;
	   }elseif ($xuhao< 0){
	       $xuhao = 0;
	   }else{
	       $xuhao = $xuhao;
	   }
query("update zzcms_main set xuhao=$xuhao where id=".$row['id']."");
}
}
?>
<div class="content">
<div class="admintitle"><?php echo channelzs?>信息排序</div>
 <?php
$sql="select * from zzcms_main where editor='".$username."' order by xuhao desc";
$rs = query($sql); 
?>
<form action="?action=px" method="post" name="form" id="form" >
  
            <div class="border"> 
              <input type="submit" class="buttons"  value="更新序号">
             提示：在表单内填上每条信息的序号（0-9999）数字越大显示越向前，然后点击
              <input name="submit22" type="submit" class="buttons" id="submit23"  value="更新序号">
              </div>
      
        <table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
          <tr class="trtitle"> 
            <td width="10%" align="center" class="border">序号</td>
            <td width="40%" class="border">产品名称</td>
            <td width="40%" align="center" class="border">产品图片</td>
          </tr>
         <?php
		 while($row = fetch_array($rs)){
		 ?>
          <tr class="trcontent"> 
            <td width="75" align="center"> <input name='<?php echo "xuhao".$row["id"]?>' type="text" value="<?php echo $row["xuhao"]?>" size="4" maxlength="4"> 
            </td>
            <td width="431"><a href="<?php echo getpageurl("zs",$row["id"])?>" target="_blank"><?php echo $row["proname"]?></a></td>
            <td width="479" align="center"><a href="<?php echo "/".$row["img"]?>" target="_blank"><img src="<?php echo $row["img"]?>" width="60" height="60" border="0"></a></td>
          </tr>
        <?php
		}
		?>
        </table>
        
            <div class="border"> <input name="submit" type="submit" class="buttons" id="submit"  value="更新序号">
                 提示：在表单内填上每条信息的序号（0-9999）数字越大显示越向前，然后点击
<input type="submit" class="buttons"  value="更新序号">
              </div>
      
</form>
</div>
</div>
</div>
</div>

</body>
</html>