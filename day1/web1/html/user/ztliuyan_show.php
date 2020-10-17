<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>展厅内留言详细信息</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
$id=trim($_REQUEST["id"]);
if ($id<>""){
checkid($id);
}else{
$id=0;
}
?>
</head>
<body>
<?php
$sql="select * from zzcms_guestbook where id='$id'";
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "不存在相关信息";
}else{
$row=fetch_array($rs);
$dlsname=$row['linkmen'];
$tel=$row['phone'];
$email=$row['email'];
$looked=$row['looked'];
function showlx($dlsname,$tel,$email){ 
global $f_array;
$str="<table width='100%' border='0' cellpadding=5 cellspacing=1 class=bgcolor>";
$str=$str."<tr>";
$str=$str."<td width=22% align=right class=bgcolor1>联系人：</td>";
$str=$str."<td width=78% bgcolor=#FFFFFF>".$dlsname."</td>";
$str=$str."</tr>";
$str=$str."<tr>";
$str=$str."<td align=right class=bgcolor1>电话：</td>";
$str=$str."<td bgcolor=#FFFFFF>".$tel."</td>";
$str=$str."</tr>";
$str=$str."<tr> ";
$str=$str."<td align=right class=bgcolor1>Email：</td>";
$str=$str."<td bgcolor=#FFFFFF>".$email."</td>";
$str=$str."</tr>";
$str=$str."</table>";
return $str;
}
if ($row["saver"]<>$_COOKIE["UserName"]){
markit();
echo "<script>alert('非法操作！警告：你的操作已被记录！');history.back(-1);</script>";
exit;
}
?>
<div class="content">
<div class="admintitle">展厅留言</div> 
    <table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
          <tr>
            <td width="22%" align="right" class="bgcolor1">标题：</td>
            <td width="78%" bgcolor="#FFFFFF"><?php echo $row["title"]?></td>
          </tr>
          <tr>
            <td align="right" class="bgcolor1">内容：</td>
            <td bgcolor="#FFFFFF"><?php echo nl2br($row["content"])?></td>
          </tr>
          <tr>
            <td align="right" class="bgcolor1">留言时间：</td>
            <td bgcolor="#FFFFFF"><?php echo $row["sendtime"]?></td>
          </tr>
</table><br>
        <div class="admintitle">联系方式</div>
		  
 <?php
         switch  (check_user_power("look_dls_liuyan")){
			case "yes" ;
			query("update zzcms_guestbook set looked=1 where id='$id'");
            echo showlx($dlsname,$tel,$email);
			break;
			case "no";
			    if (jifen=="Yes"){
				    if ($looked==1) {
					echo showlx($dlsname,$tel,$email);
					}
					$action=isset($_POST["action"])?$_POST["action"]:'';
					if ($action=="" && $looked==0) {?>
            		<div class="box">
					<form name="form1" method="post" action="">
                    <input type="submit" name="Submit2" style="height:30px" value="点击查看联系方式。注：需要您付出金币：<?php echo jf_lookmessage?>">
                    <input name="action" type="hidden" id="action" value="kan">
                  	</form>
				  	</div>		
					<?php		
			    	}elseif ($action=="kan" && $looked==0) {
                    $sql="select totleRMB from zzcms_user where username='".$_COOKIE["UserName"]."'";
					$rsuser=query($sql);
					$rowuser=fetch_array($rsuser);
			        	if ($rowuser["totleRMB"]>=jf_lookmessage) {
						query("update zzcms_user set totleRMB=totleRMB-".jf_lookmessage." where username='".$_COOKIE["UserName"]."'");//查看时扣除积分
						query("insert into zzcms_pay (username,dowhat,RMB,mark,sendtime) values('".@$_COOKIE['UserName']."','查看展厅留言','-".jf_lookmessage."','<a href=ztliuyan_show.php?id=$id>$id</a>','".date('Y-m-d H:i:s')."')");//写入冲值记录 
			       		query("update zzcms_guestbook set looked=1 where id='$id'");
						echo showlx($dlsname,$tel,$email);
						}else{
						echo "<script>alert('您的帐户中已不足".jf_lookmessage."金币，暂不能查看！')</script>";
		            	?>
						<div class="box">
						<input  type="button"  value="升级成VIP会员，联系方式随时查看！" onClick="location.href='/one/vipuser.php'"/>
						</div>
		         		<?php    
				 		}
					}	
			}elseif (jifen=="No" ){
			?>
<div class="box">
您所在的用户组没有查看留言的权限！<br><br><input name="Submit22" type="button"  value="升级成VIP会员" onClick="location.href='/one/vipuser.php'"/>
	</div>
             
			<?php
			}
		break;
		}
}
?>

</div>
</body>
</html>