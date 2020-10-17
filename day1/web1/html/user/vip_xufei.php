<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
$ErrMsg="";
$FoundErr=0;
$action = isset($_GET['action'])?$_GET['action']:"";
$rs=query("Select * from zzcms_usergroup where groupid=(select groupid from zzcms_user where username='".$username."')");
$row=fetch_array($rs);
$groupname=$row["groupname"];
$RMB_xufei=$row["RMB"];

if( $action=="modify"){
	$sj=trim($_POST["sj"]);
	if ($sj<>"") {
	checkid($sj);
	}

	$rs=query("select * from zzcms_user where username='" . $username ."'");
	$row=num_rows($rs);
	if (!$row){
		$FoundErr=1;
		$ErrMsg=$ErrMsg."<li>找不到指定的用户！</li>";
		WriteErrMsg($ErrMsg);
	}else{
	$row=fetch_array($rs);
	$enddate=$row['enddate'];
		if ($row["groupid"]==1){
		$FoundErr=1;
		$ErrMsg=$ErrMsg ."<li>您目前是免费会员不用续费！</li>" ;
		WriteErrMsg($ErrMsg);
		}else{
			if ($row["totleRMB"]< $RMB_xufei) {
			$FoundErr=1;
			$ErrMsg=$ErrMsg ."<li>您的余额不足，请先<a href='/3/alipay'>充值</a>！</li>";
			WriteErrMsg($ErrMsg);
			}else{			
			query("update zzcms_user set enddate='".date('Y-m-d',strtotime($enddate)+3600*24*365*$sj)."',totleRMB=totleRMB-".$sj*$RMB_xufei." where username='" . $username ."'");
			query("insert into zzcms_pay (username,dowhat,RMB,mark,sendtime)values('$username','续会员费','".$sj*$RMB_xufei."','续费".$sj."年','".date('Y-m-d H:i:s')."')");
		
			echo "<script>alert('续费成功');location.href='vip_xufei.php'</script>";
			}
		}
	}	
}else{
$rs=query("select * from zzcms_user where username='" . $username ."'");
$row=fetch_array($rs);		
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
<div class="admintitle">会员续费</div>
<FORM name="myform" action="?action=modify" method="post">
<table width="100%" border="0" cellpadding="3" cellspacing="1">
            <tr> 
              <td width="15%" align="right" class="border">用户名：</td>
              <td width="85%" class="border"><?php echo  $username?></td>
            </tr>
            <tr> 
              <td align="right" class="border2">当前所属用户组：</td>
              <td width="85%" class="border2"> <?php echo $groupname?> </td>
            </tr>
			<?php if ($row["groupid"]>1){ ?>
            <tr> 
              <td align="right" class="border"><?php echo $groupname?>开通时间：</td>
              <td class="border"> <?php echo $row["startdate"]?> </td>
            </tr>
            <tr> 
              <td align="right" class="border2"><?php echo $groupname?>到期时间：</td>
              <td class="border2"> <?php echo $row["enddate"]?>  </td>
            </tr>
			<?php
			}
			?>
            <tr> 
              <td align="right" class="border">续费年限：</td>
              <td class="border"><select name="sj" id="sj">
                  <option value="1" selected>一年(<?php echo $RMB_xufei?>金币)</option>
                  <option value="2">两年(<?php echo 2*$RMB_xufei?>金币)</option>
                  <option value="3">三年(<?php echo 3*$RMB_xufei?>金币)</option>
                  <option value="5">五年(<?php echo 5*$RMB_xufei?>金币)</option>
                </select> </td>
            </tr>
            <tr > 
              <td align="right" class="border2">&nbsp;</td>
              <td class="border2"> <input name="Submit2"   type="submit" class="buttons" id="Submit2" value="保存"></td>
            </tr>
          </table>
  </form>
</div>
</div>
</div>
</div>  
</body>
</html>
<?php
}
?>