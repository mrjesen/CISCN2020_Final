<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
body { margin:0px; background:transparent; overflow:hidden; background:url("image/manage_leftbg.gif"); }
img { float:none; vertical-align:middle; }
.left_color { text-align:right;padding-top:5px }
.left_color li { 
	font-size:12px; display:block;width:175px;text-align:right;
	background:url("image/menubg.png") right top no-repeat; 
	height:23px; line-height:25px;
	padding-right:10px; margin-bottom:2px;
	}
.left_color li:hover {background-position:right -23px; }
.left_color li a{display:inline;padding:5px 5px;color: #083772;text-decoration: none;}
.left_color li a:hover{color: #7B2E00; }
</style>
<script type="text/javascript">
function disp(n){
	for (var i=0;i<12;i++){
		if (!document.getElementById("left"+i)) return;			
		document.getElementById("left"+i).style.display="none";
		}
		document.getElementById("left"+n).style.display="";
}	
</script>
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td valign="top" class="left_color">
	<div id="left0" style="display:"> 
	<li><a href="zs_manage.php" target="frmright"><?php echo channelzs?></a></li>
	<li><a href="dl_manage.php" target="frmright"><?php echo channeldl?></a></li>
     </div>	
     <div id="left1" style="display:none"> 
	  <li><a href="tag.php?tablename=zzcms_tagzs" target="frmright">关键词</a><a href="zs_manage.php" target="frmright"><?php echo channelzs?></a></li> 
	  <li><a href="dl_data.php" target="frmright" >导入</a><a href="dl_manage.php" target="frmright" ><?php echo channeldl?></a></li>
	 
	  <?php
	if (str_is_inarr(channel,'pp')=='yes'){
	?>
	  <li><a href="pp_manage.php" target="frmright">品牌</a></li> 
	<?php
	}
	if (str_is_inarr(channel,'job')=='yes'){
	?>  
	<li><a href="job_manage.php" target="frmright">招聘</a></li>
	<?php
	}
	if (str_is_inarr(channel,'zh')=='yes'){
	?>
	  <li><a href="zh_manage.php" target="frmright"  >展会</a></li> 
	<?php
	}
	if (str_is_inarr(channel,'zx')=='yes'){
	?>  
	<li><a href="pinglun_manage.php" target="frmright">评论</a>
		<a href="tag.php?tablename=zzcms_tagzx" target="frmright">关键词 </a>
		<a href="zx_manage.php" target="frmright">资讯</a>
		</li> 
       
	<?php
	}
	if (str_is_inarr(channel,'wangkan')=='yes'){
	?>	 
		 <li><a href="wangkan_manage.php" target="frmright">网刊</a></li> 
	<?php
	}
	if (str_is_inarr(channel,'baojia')=='yes'){
	?>	 
		  <li><a href="baojia_manage.php" target="frmright" >报价</a></li>
	<?php
	}
	if (str_is_inarr(channel,'special')=='yes'){
	?>	  
		 <li><a href="special_manage.php" target="frmright">专题</a></li> 
	<?php
	}
	if (str_is_inarr(channel,'ask')=='yes'){
	?>
	 <li><a href="ask_manage.php" target="frmright">问答</a></li> 
	 <?php
	}
	?>
		<li><a href="ztliuyan_manage.php" target="frmright">展厅留言</a></li>
		<li><a href="usermessage.php" target="frmright">用户返馈</a></li> 
		<li><a href="licence.php" target="frmright">资质证书</a></li> 
		<li><a href="linkmanage.php" target="frmright">友情链接</a></li> 
		<li><a href="help_manage.php?b=2" target="frmright">公告信息</a></li>
		<li><a href="help_manage.php?b=1" target="frmright">帮助信息</a></li>
		<li><a href="domain_manage.php" target="frmright">展厅域名</a></li>
      </div>
	  
      <div id="left2" style="display:none"> 
	  <li><a href="class2.php?tablename=zzcms_zsclass" target="frmright"><?php echo channelzs?>/<?php echo channeldl?>类别</a></li> 
	 <?php
	if (str_is_inarr(channel,'zx')=='yes'){
	?>
	  <li><a href="class2.php?tablename=zzcms_zxclass" target="frmright">资讯类别</a></li>
	  <?php
	}
	if (str_is_inarr(channel,'special')=='yes'){
	?>
	   <li><a href="class2.php?tablename=zzcms_specialclass" target="frmright">专题类别</a></li> 
	   <?php
	}
	if (str_is_inarr(channel,'job')=='yes'){
	?>
	  <li><a href="class2.php?tablename=zzcms_jobclass" target="frmright">招聘类别</a></li>
	<?php
	}
	if (str_is_inarr(channel,'ask')=='yes'){
	?>
	  <li><a href="class2.php?tablename=zzcms_askclass" target="frmright">问答类别</a></li> 
	    <?php
	}
	if (str_is_inarr(channel,'zh')=='yes'){
	?>  
      <li><a href="class.php?tablename=zzcms_zhclass" target="frmright">展会类别</a></li> 
	  <?php
	}
	if (str_is_inarr(channel,'wangkan')=='yes'){
	?>
	  <li><a href="class.php?tablename=zzcms_wangkanclass" target="frmright">网刊类别</a></li>  
	  <?php
	}
	
	
	?>
	<li><a href="class2.php?tablename=zzcms_userclass" target="frmright">企业类别</a></li>
      <li><a href="adclass.php" target="frmright">广告类别</a></li> 
	<li><a href="class.php?tablename=zzcms_linkclass" target="frmright">友情链接类别</a></li> 
      </div>

      <div id="left3" style="display:none"> 
	   <li><a href="ad_manage.php" target="frmright">管理</a><a href="ad.php?do=add" target="frmright">添加</a></li>
		<li><a href="adclass.php" target="frmright">类别设置</a></li>
		<li><a href="siteconfig.php?#qiangad" target="frmright">广告设置</a></li>
		 <li><a href="ad_user_manage.php" target="frmright">用户审请的广告</a></li>
		</div>

      <div id="left4" style="display:none"> 
		<li><a href="usermanage.php" target="frmright">用户</a></li>
		<li><a href="usergroup.php?do=show" target="frmright">用户组</a></li>
		<li><a href="siteconfig.php#usergr_power" target="frmright">个人用户权限</a></li>
	 	<li><a href="usernotreg.php" target="frmright">未进行邮箱验证的用户</a></li>
 		<li><a href="domain_manage.php" target="frmright">用户展厅域名</a></li>
		<li><a href="bad.php" target="frmright">用户不良操作记录</a></li>
		
		<li><a href="usermessage.php" target="frmright">用户返馈信息</a></li>
        <li><a href="adminlist.php" target="frmright">管理员</a></li>
		<li><a href="admingroup.php" target="frmright">管理员组</a></li>
	</div>
				
      <div id="left5" style="display:none"> 
        <li><a href="siteconfig.php#UpFile" target="frmright">上传功能设置</a></li>
        <li><a href="siteconfig.php#addimage" target="frmright">添加水印功能设置</a></li>
        <li><a href="uploadfile_nouse.php" target="frmright"> 清理无用的上传文件</a></li>
		 </div>

			<div id="left6" style="display:none"> 
				<li><a href="message.php?do=add" target="frmright">发站内短消息</a></li> 
				<li><a href="message.php" target="frmright">站内短消息管理</a></li>
				<li><a href="sendmail.php" target="frmright">发E-mali</a></li> 
				<li><a href="siteconfig.php#sendmail" target="frmright">E-mali设置</a></li>
				<li><a href="sendsms.php" target="frmright">发手机短信</a></li>
				<li><a href="siteconfig.php#sendSms" target="frmright">手机短信设置</a></li>
			</div>
			
			<div id="left7" style="display:none"> 
			<li><a href="siteconfig.php#siteskin" target="frmright">风格</a></li>
			<li><a href="siteconfig.php#SiteInfo" target="frmright">基本信息</a></li>
			<li><a href="siteconfig.php#SiteOpen" target="frmright">运行状态</a></li>
			<li><a href="siteconfig.php#SiteOption" target="frmright">功能参数</a></li>
            <li><a href="about_manage.php" target="frmright">底部链接</a></li> 
			<li><a href="siteconfig.php#stopwords" target="frmright">限制字符</a></li> 
			<li><a href="siteconfig.php#SiteOpen" target="frmright">限制来访IP</a></li>
            <li><a href="siteconfig.php#qiangad" target="frmright">广告设置</a></li>
			<li><a href="siteconfig.php#userjf" target="frmright">积分功能</a></li>
			<li><a href="siteconfig.php#UpFile" target="frmright">上传文件</a></li>
			<li><a href="siteconfig.php#addimage" target="frmright">添加水印功能</a></li>	 
			<li><a href="siteconfig.php#alipay_set" target="frmright">支付接口</a></li>	 
            <li><a href="siteconfig.php#sendmail" target="frmright">发邮件接口</a></li>
			<li><a href="siteconfig.php#sendsms" target="frmright">发手机短信接口</a></li>
			<li><a href="qqlogin_set.php" target="frmright">QQ互联接口</a></li> 
			<li><a href="ucenter_config.php" target="frmright">整合Discuz! Ucenter接口</a></li> 
			<li><a href="wjtset.php" target="frmright">文件头</a></li> 
			</div>
			
			<div id="left8" style="display:none">
			<li><a href="databaseclear.php" target="frmright">初始化数据库</a></li>
			<li><a href="data_back.htm" target="frmright">备份/还原数据库</a></li>
			</div>
			
			<div id="left9" style="display:none"> 
			<li><a href="labelshow.php?channel=zsshow" target="frmright"><?php echo channelzs?>内容标签</a>
			<a href="labelclass.php?classname=zsclass" target="frmright">类别标签</a></li>		
			<li><a href="labelshow.php?channel=dlshow" target="frmright"><?php echo channeldl?>内容标签</a>
			<a href="labelclass.php?classname=dlclass" target="frmright">类别标签</a></li>
			<?php
	if (str_is_inarr(channel,'pp')=='yes'){
	?>
			<li><a href="labelshow.php?channel=ppshow" target="frmright">品牌内容标签</a>
			<a href="labelclass.php?classname=ppclass" target="frmright">类别标签</a></li>
	<?php
	}
	if (str_is_inarr(channel,'job')=='yes'){
	?>		
			<li><a href="labelshow.php?channel=jobshow" target="frmright">招聘内容标签</a>
			<a href="labelclass.php?classname=jobclass" target="frmright">类别标签</a></li>		
	<?php
	}
	if (str_is_inarr(channel,'zx')=='yes'){
	?>		
			<li><a href="labelshow.php?channel=zxshow" target="frmright">资讯内容标签</a>
			<a href="labelclass.php?classname=zxclass" target="frmright">类别标签</a></li>
	<?php
	}
	if (str_is_inarr(channel,'wangkan')=='yes'){
	?>		
			<li><a href="labelshow.php?channel=wangkanshow" target="frmright">网刊内容标签</a>
			<a href="labelclass.php?classname=wangkanclass" target="frmright">类别标签</a></li>
	<?php
	}
	if (str_is_inarr(channel,'baojia')=='yes'){
	?>		
			<li><a href="labelshow.php?channel=baojiashow" target="frmright">报价内容标签</a>
			<a href="labelclass.php?classname=baojiaclass" target="frmright">类别标签</a></li>
	<?php
	}
	if (str_is_inarr(channel,'special')=='yes'){
	?>		
			<li><a href="labelshow.php?channel=specialshow" target="frmright">专题内容标签</a>
			<a href="labelclass.php?classname=specialclass" target="frmright">类别标签</a></li>
	<?php
	}
	if (str_is_inarr(channel,'zh')=='yes'){
	?>		
			<li><a href="labelshow.php?channel=zhshow" target="frmright">展会内容标签</a>
			<a href="labelclass.php?classname=zhclass" target="frmright">类别标签</a></li>
	<?php
	}
	if (str_is_inarr(channel,'ask')=='yes'){
	?>
	<li><a href="labelshow.php?channel=askshow" target="frmright">问答内容标签</a>
	<a href="labelclass.php?classname=askclass" target="frmright">类别标签</a></li>
	<?php
	}
	?>	
			<li><a href="labelshow.php?channel=companyshow" target="frmright">企业内容标签</a>
			<a href="labelclass.php?classname=companyclass" target="frmright">类别标签</a></li>
			<li><a href="labeladshow.php" target="frmright">广告内容标签</a>
			<a href="labeladclass.php" target="frmright">类别标签</a></li>
			<li><a href="labelshow.php?channel=linkshow" target="frmright">友情链接内容标签</a>
			<a href="labelclass.php?classname=linkclass" target="frmright">类别标签</a></li>
			<li><a href="labelshow.php?channel=helpshow" target="frmright">帮助内容标签</a></li>
			<li><a href="labelshow.php?channel=aboutshow" target="frmright">单页内容标签</a></li>
			
			<li><a href="labelshow.php?channel=guestbookshow" target="frmright">留言本内容标签</a></li>
			</div>
			
			<div id="left10" style="display:none"> 
			<li><a href="template.php" target="frmright">网站模板</a></li>
			<li><a href="template_user.php" target="frmright">用户展厅模板</a></li>
			</div>
			<div id="left11" style="display:none"> 
			<li><a href="cachedel.php" target="frmright">清理网站缓存</a></li>
			<li><a href="htmldel.php" target="frmright">清理HTML页</a></li>
			</div>		
	</td>
 </tr>
</table>
</body>
</html>