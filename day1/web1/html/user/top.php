<?php
//if(!isset($_SERVER['HTTP_REFERER'])){//禁止从外部直接打开
//exit;
//}
if (isset($_COOKIE["UserName"])){
?>
<div class="menu">
  <div class="system_logo"></div>
  <div id="tabs"> 
    <ul>
	 <?php 
	 if ($usersf=="公司"){ 
	 ?>
      <li><a href="zsmanage.php" target="_self"><span>我的产品</span></a></li>
      <li><a href="dls.php"  target="_self"><span>我的留言</span></a></li>
      <li><a href="adv2.php"  target="_self"><span>广告设置</span></a></li>
      <li><a href="licence.php" target="_self"><span>公司资质</span></a></li>
      <li><a href="pay_manage.php" target="_self"><span>财务管理</span></a></li>
      <?php
	  }
	  ?>
      <li><a href="manage.php"  target="_self"><span>会员资料</span></a></li>
      <li><a href="managepwd.php"  target="_self"><span>修改密码</span></a></li>
	  
	  <?php
	  if (str_is_inarr(usergr_power,'zt')=='yes' || $usersf=='公司'){
	  ?>
	  <li><a href="ztconfig.php"  target="_self"><span>展厅设置</span></a></li>
	   <?php 
	   }
	  ?>
    </ul>
	</div>
</div>
<div style="clear:both"></div>
<div class="userbar">
<span style="float:right"> [ <a href="/<?php echo getpageurl3("index")?>" target="_top"> <img src="../image/home.gif"> 网站首页</a> 
<?php
	if (str_is_inarr(usergr_power,'zt')=='yes' || $usersf=='公司'){
	echo " | ";
		if (sdomain=="Yes"){
			echo "<a href='http://".$username.".".substr(siteurl,strpos(siteurl,".")+1)."' target='_blank'><img src='../image/pic.gif' > 我的展厅</a>";
		}else{
			echo "<a href='".getpageurl("zt",$userid)."'  target='_blank'><img src='../image/pic.gif'> 我的展厅</a>";	
		}
	}
?>
        | <a href='/one/help.php#64' target='blank'><img src="../image/ico6.gif" > 操作说明</a> |
		<a href="logout.php" target="_top"><img src="../image/ding.gif" > 安全退出</a> ] 
</span>
		您好！<strong><?php echo $_COOKIE["UserName"];?></strong>( <?php echo ShowUserSf();?>) 
<?php
}

function ShowUserSf(){
	if ($_COOKIE["UserName"]<>"" ){
		$sql="select groupname,grouppic from zzcms_usergroup where groupid=(select groupid from zzcms_user where username='".$_COOKIE["UserName"]."')";
        $rs=query($sql);
		$row=fetch_array($rs);
		$rownum=num_rows($rs);
		if ($rownum){
        $str= "<b>".$row["groupname"]."</b><img src='../".$row["grouppic"]."'> " ;
		}
 		   
		$sql="select groupid,totleRMB,startdate,enddate from zzcms_user where username='" .$_COOKIE["UserName"]. "'";
        $rs=query($sql);
		$row=fetch_array($rs);
		$rownum=num_rows($rs);
		if ($rownum){
			if ($row["groupid"]>1){
			$str=$str ."服务时间：".$row["startdate"]." 至 ".$row["enddate"];
			}elseif ($row["groupid"]==1){
			$str=$str . "<a href='../one/vipuser.php' target='_blank'>查看我的权限</a>";
			}
		}else{
			$str=$str . "用户不存在";
		}		
		
	}else{
	$str=$str. "您尚未登录";
	}
echo $str;			 
}
?>		
</div>