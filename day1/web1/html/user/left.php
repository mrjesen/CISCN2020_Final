<?php 
//if(!isset($_SERVER['HTTP_REFERER'])){//禁止从外部直接打开
//exit;
//}

if (isset($_COOKIE["UserName"])){
$current_url=$_SERVER['PHP_SELF'];
$c_name= substr( $current_url,strrpos($current_url,'/')+1);  
?>
<script type="text/javascript">
<!--
function disp(n){
for (var i=0;i<9;i++){
	if (!document.getElementById("left"+i)) return;			
		document.getElementById("left"+i).style.display="none";
	}
	document.getElementById("left"+n).style.display="";
}

function Confirmdeluser(){
   if(confirm("注销后将不能恢复！确定要注销帐户么？"))
     return true;
   else
     return false;	 
}	
//-->
</script>
<div id="left1" style="display:block"  class="leftcontent"> 
<div class="lefttitle"><img src="image/ico/ico4.gif"> 发布信息 </div>
<div>
<ul>
<?php
if (str_is_inarr(usergr_power,'zs')=='yes'|| $usersf=='公司'){
	if ($c_name=='zs.php'||$c_name=='zsmanage.php'||$c_name=='zspx.php'||$c_name=='zs_elite.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='zs.php?do=add' target='_self'>发".channelzs."</a> | <a href='zsmanage.php' target='_self'>管理</a></li> ";
}

if (str_is_inarr(channel,'pp')=='yes'){
if (str_is_inarr(usergr_power,'pp')=='yes'|| $usersf=='公司'){
	if ($c_name=='pp.php'||$c_name=='ppmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='pp.php?do=add' target='_self'>发品牌</a> | <a href='ppmanage.php' target='_self'>管理</a></li> ";	
}
}

if (str_is_inarr(usergr_power,'dl')=='yes'|| $usersf=='公司'){
	if ($c_name=='dl.php'||$c_name=='dlmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='dl.php?do=add' target='_self'>发".channeldl."</a> | <a href='dlmanage.php' target='_self'>管理</a></li> ";
}

if (str_is_inarr(channel,'baojia')=='yes'){
if (str_is_inarr(usergr_power,'baojia')=='yes'|| $usersf=='公司'){
	if ($c_name=='baojia.php'||$c_name=='baojiamanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='baojia.php?do=add' target='_self'>发报价</a> | <a href='baojiamanage.php' target='_self'>管理</a></li> ";
}
}

if (str_is_inarr(channel,'zh')=='yes'){
if (str_is_inarr(usergr_power,'zh')=='yes'|| $usersf=='公司'){
	if ($c_name=='zh.php'||$c_name=='zhmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='zh.php?do=add' target='_self'>发展会</a> | <a href='zhmanage.php' target='_self'>管理</a></li> ";
}
}

if (str_is_inarr(channel,'wangkan')=='yes'){
if (str_is_inarr(usergr_power,'wangkan')=='yes'|| $usersf=='公司'){
	if ($c_name=='wangkan.php'||$c_name=='wangkanmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='wangkan.php?do=add' target='_self'>发网刊</a> | <a href='wangkanmanage.php' target='_self'>管理</a></li> ";
}
}

if (str_is_inarr(channel,'zx')=='yes'){
if (str_is_inarr(usergr_power,'zx')=='yes'|| $usersf=='公司'){
	if ($c_name=='zx.php'||$c_name=='zxmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='zx.php?do=add' target='_self'>发资讯</a> | <a href='zxmanage.php' target='_self'>管理</a></li> ";
}
}

if (str_is_inarr(channel,'special')=='yes'){
if (str_is_inarr(usergr_power,'special')=='yes'|| $usersf=='公司'){
	if ($c_name=='special.php'||$c_name=='specialmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='special.php?do=add' target='_self'>发专题</a> | <a href='specialmanage.php' target='_self'>管理</a></li> ";
}
}

if (str_is_inarr(channel,'ask')=='yes'){
if (str_is_inarr(usergr_power,'ask')=='yes'|| $usersf=='公司'){
	if ($c_name=='ask.php'||$c_name=='askmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='ask.php?do=add' target='_self'>发问答</a> | <a href='askmanage.php' target='_self'>管理</a></li> ";
}
}

if (str_is_inarr(channel,'job')=='yes'){
if (str_is_inarr(usergr_power,'job')=='yes'|| $usersf=='公司'){
	if ($c_name=='job.php'||$c_name=='jobmanage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='job.php?do=add' target='_self'>发招聘</a> | <a href='jobmanage.php' target='_self'>管理</a></li> ";
}
}

$sql_left="select classid from zzcms_zxclass where classname='公司新闻' ";
$rs_left=query($sql_left);
$row_left=fetch_array($rs_left);

if (str_is_inarr(channel,'zx')=='yes'){
if ($usersf=='公司'){
	if ($c_name=='zxadd.php'||$c_name=='zxmanage.php'||$c_name=='zxmodify.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='index.php?gotopage=zx.php?do=add&b=".$row_left['classid']."' target='_self'>发公司新闻</a> | <a href='zxmanage.php?bigclassid=".$row_left['classid']."' target='_self'>管理</a></li> ";
}
}

if ($c_name=='advzt.php'||$c_name=='advzt_manage.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='advzt.php?do=add' target='_self'>发广告</a> | <a href='advzt_manage.php' target='_self'>管理</a></li> ";
?>
</ul>
</div>
</div>

<?php if (str_is_inarr(usergr_power,'zs')=='yes'|| $usersf=='公司'){?>
<div id="left2" style="display:block" class="leftcontent">
<div class="lefttitle"><img src="image/ico/ico8.gif"> 查看留言</div>
<div>
<ul>
<?php
if ($c_name=='dls.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='dls.php' target='_self'>意向产品留言</a> ";		  
$sql_left="select id from zzcms_dl where saver='".@$username."' and looked=0 and del=0 and passed=1";
$rs_left=query($sql_left);
$row_left=num_rows($rs_left);
if($row_left){
echo "<span class='buttons'>".$row_left."</span>";
}
echo "</li>";

if ($c_name=='ztliuyan.php'){
	echo "<li class='current2'>";
	}else{
	echo "<li class='current1'>";
	}
echo "<a href='ztliuyan.php?show=all' target='_self'>展厅留言</a> ";

$sql_left="select id from zzcms_guestbook where saver='".@$username."' and looked=0 and passed=1";
$rs_left=query($sql_left);
$row_left=num_rows($rs_left);
if($row_left){
echo "<span class='buttons'>".$row_left."</span>";
}
echo "</li>";
?>			
</ul>		
</div>
</div>
<?php
 }
 ?>
 
<div id="left10" style="display:block" class="leftcontent"> 
<div class="lefttitle"><img src="image/ico/ico8.gif"> 代理信息库</div>
<div>
<ul>	
<li><a href="/dl/dl.php" target="_blank">查看代理信息库</a></li>
</ul>
</div>
</div>

<div id="left3" style="display:block" class="leftcontent"> 		
<div class="lefttitle"> <img src="image/ico/ico9.gif" width="12" height="16"> 抢广告位</div>
<div> 
<ul>
<li><a href="adv.php" target="_self">设置/更换广告词</a></li>
<li><a href="adv2.php" target="_self">抢占广告位</a><img src="image/ico/ico6.gif" width="23" height="12"></li>
</ul>
</div>
</div>

<?php 
if ($usersf=="公司"){ 
?>	
<div id="left4" style="display:block" class="leftcontent"> 
<div class="lefttitle"><img src="image/ico/ico5.gif" width="16" height="16"> 资质管理</div>
<div>
<ul>			
<li><a href="licence.php?do=add" target="_self">资质证书添加</a></li> 
<li><a href="licence_manage.php" target="_self" >资质证书管理</a></li>
</ul>
</div>
</div>
<?php 
}
?>
<div id="left5" style="display:block" class="leftcontent"> 
<div class="lefttitle"><img src="image/ico/ico7.gif" width="16" height="15"> 财务管理</div>
<div>
<ul>	
<li><a href="/3/alipay/" target="_blank">用支付宝充值</a></li>
<li><a href="/3/tenpay/" target="_blank"> 用财富通充值</a></li>
<li><a href="pay_manage.php" target="_self">我的财务记录</a></li>
</ul>
</div>
</div>
			
<div id="left6" style="display:block" class="leftcontent"> 
<div class="lefttitle"><img src="image/ico/ico10.gif" width="16" height="16"> 用户设置</div>
<div>
<ul>
<li><a href="vip_add.php" target="_self">会员自助升级</a></li> 
<li><a href="vip_xufei.php" target="_self">会员自助续费</a></li> 
<li><a href="manage.php" target="_self">修改注册信息</a></li>
<li><a href="managepwd.php" target="_self">修改登录密码</a></li>
<li><a href="/one/vipuser.php" target="_blank">查看我的权限</a></li>
<li><a href="index.php" target="_self">查看帐号信息</a></li> 
</ul>
</div>
</div>
<?php if ($usersf=="公司"){ ?>
<div id="left7" style="display:block" class="leftcontent"> 
<div class="lefttitle"><img src="image/ico/ico10.gif" width="16" height="16"> 展厅设置</div>
<div>
<ul>
<li><a href="ztconfig_skin.php" target="_self"> 模板更换</a></li>
<li><a href="ztconfig_skin_mobile.php" target="_self">手机版模板更换</a></li>
<li><a href="ztconfig.php" target="_self"> 用户展厅设置</a></li>
<li><a href="domain_manage.php" target="_self"> 绑定顶级域名</a></li>
</ul>
</div>
</div>				
<?php 
}
?>				
<div id="left8" style="display:block" class="leftcontent">			
<div class="lefttitle"><img src="image/ico/ico8.gif"> 群发信息</div>
<div>
<ul>
<li><a href="msg_manage.php" target="_self" >邮件/短信内容设置</a></li>
<li><a href="../dl/dl.php" target="_blank">给<?php echo channeldl?>商群发信息</a></li>			
</ul>		
</div>
</div>		

<div id="left9" style="display:block" class="leftcontent"> 
<div class="lefttitle"><img src="image/ico/ico3.gif"> 需要帮助</div>
<div>
<ul>
<li><a target=blank href=http://wpa.qq.com/msgrd?v=1&uin=<?php echo kfqq?>&Site=<?php echo sitename?>&Menu=yes><img border="0" src=http://wpa.qq.com/pa?p=1:<?php echo kfqq ?>:4>在线客服QQ</a></li>
<li><a href="###">电话：<?php echo kftel?></a></li>
<li><a href="/one/help.php" target="_blank">常见问题解答</a></li>
<li><a href="message.php">给管理员发信息</a></li>
</ul>
</div>
</div>
<?php 
}
?>