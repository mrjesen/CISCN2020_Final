<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>展厅模板设置</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
if (str_is_inarr(usergr_power,'zt')=="no" && $usersf=='个人'){
echo "<script>alert('个人用户没有此权限');history.back(-1);</script>";
exit;
}
$action = isset($_GET['action'])?$_GET['action']:"";
if($action=="modify"){
query("update zzcms_usersetting set skin_mobile='$skin' where username='".$username."'");			
echo "<script>alert('成功更新设置');location.href='ztconfig_skin_mobile.php'</script>";
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
<div class="admintitle">展厅模板设置</div>
<form name="myform" method="post" action="?action=modify"> 
<table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
<tr>        
<?php 
$rs=query("select skin_mobile from zzcms_usersetting where username='".$username."'");
$row=fetch_array($rs);					
$fp=zzcmsroot."skin";	
if (file_exists($fp)==false){
WriteErrMsg($fp.'模板目录不存在');
exit;
}				
$dir = opendir($fp);
$i=0;
while(($file = readdir($dir))!=false){
  if ($file!="." && $file!=".." && strpos($file,".zip")==false && strpos($file,".rar")==false && strpos($file,"mobile")!==false) { //不读取. ..
    //$f = explode('.', $file);//用$f[0]可只取文件名不取后缀。 
?>
                    <td align="center" bgcolor="#FFFFFF"><table width="120" border="0" cellpadding="5" cellspacing="1">
                        <tr> 
                          <td align="center" <?php if($row["skin_mobile"]==$file){ echo "bgcolor='#FF0000'";}else{echo "bgcolor='#FFFFFF'"; }?>>
						  <img src='../skin/<?php echo $file?>/image/mb.gif' width="120"  border='0'/></td>
                        </tr>
                        <tr> 
                          <td align="center" bgcolor="#FFFFFF"> <input name="skin" type="radio" id='<?php echo $file?>' value="<?php echo $file?>" <?php if($row["skin_mobile"]==$file){ echo"checked";}?>/> 
                            <label for='<?php echo $file?>'><?php echo $file?></label><br />
<input name="Submit" type="submit" class="buttons" value="更新设置" />
</td>
                        </tr>
                    </table></td>
                    <?php 
				  $i=$i+1;
				  if($i % 5==0 ){
				  echo"<tr>";
				  }
				}
				}	
closedir($dir);
?>
        </table>  
</form>
</div>
</div>
</div>
</div>
</body>
</html>