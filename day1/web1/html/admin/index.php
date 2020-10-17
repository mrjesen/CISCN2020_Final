<?php
define('checkadminlogin',1);
include("admin.php");

if (opensite=='No' ){
echo "<script>location.href='siteconfig.php#SiteOpen'</script>";
}	
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>管理员后台</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style>
html,body{margin: 0px;height:100%}/*必须设height值，否则最外层DIV设值无效*/
</style>
<script>
var status = 1;
function switchSysBar(){
     if (1 == window.status){
		  window.status = 0;
          switchPoint.innerHTML = '<img src="image/manage_left.gif">';
          document.all("frmTitle").style.display="none"
     }
     else{
		  window.status = 1;
          switchPoint.innerHTML = '<img src="image/manage_right.gif">';
          document.all("frmTitle").style.display=""
     }
}
</script>
</head>
<body>
<div style="height:45px;background:#337ABB url('image/manage_top_bg.png'); ">
	<div style="float:left;width:200px"><img src="image/manage_admin.png" /></div>
	<div id="tabs">
		<ul>
			<li title="网站参数设置，具体内容见左侧子菜单"><a href="siteconfig.php" onMouseOver="parent.frmleft.disp(7);" target="frmright"><span>设置</span></a></li>
			<li title="用户发布的各类信息在这里管理"><a href="zs_manage.php" onMouseOver="parent.frmleft.disp(1);" target="frmright"><span>信息</span></a></li>			
			<li title="各类信息的类别在这里设置"><a href="class2.php?tablename=zzcms_zsclass" onMouseOver="parent.frmleft.disp(2);" target="frmright"><span>类别</span></a></li>
			<li title="网站上的广告在这里管理"><a href="ad_manage.php" onMouseOver="parent.frmleft.disp(3);" target="frmright"><span>广告</span></a></li>
			<li title="所有注册用户，用户组权限，所有管理员，管理员组权限，在这里管理"><a href="usermanage.php" onMouseOver="parent.frmleft.disp(4);" target="frmright"><span>用户</span></a></li>
			<li title="所有的上传文件在这里管理"><a href="uploadfile_nouse.php" onMouseOver="parent.frmleft.disp(5);" target="frmright"><span>文件</span></a></li>
			<li title="网站数据库在这里管理"><a href="databaseclear.php" onMouseOver="parent.frmleft.disp(8);" target="frmright"><span>数据库</span></a></li>
			<li title="标签(显示指定的内容文章列表用的，并可控制内容的布局及显示顺序；配合模的使用，可以改变前台的任何内容、形式、风格。)"><a href="javascript:void(0)" onMouseOver="parent.frmleft.disp(9);" target="frmright"><span>标签</span></a></li>
			<li title="模板指的是网站前台的皮肤文件，模板页内容为htm标签，自定义标签，css样式，js代码等。没有任何php源码。"><a href="template.php" onMouseOver="parent.frmleft.disp(10);" target="frmright"><span>模板</span></a></li>
			<li title="如果后台更新后，前台没有变化，点这里清理缓存"><a href="cachedel.php" onMouseOver="parent.frmleft.disp(11);" target="frmright"><span>清缓存</span></a></li>
			<li title="给注册用户发站内短消息，邮件，手机短信（需绑定第三方短信平台账号）"><a href="message.php?do=add" onMouseOver="parent.frmleft.disp(6);" target="frmright"><span>发消息</span></a></li>
		</ul>
	</div>
</div>

<div class="userbar">
 <?php $rs=query("select groupname from zzcms_admingroup where id=(select groupid from zzcms_admin where admin='".@addslashes($_COOKIE["admin"])."')");
	  $row= fetch_array($rs);
	  echo "您好<b>".@$_COOKIE["admin"]."</b>(" .$row["groupname"].")";
	  ?>
        [ <a href="/index.php" target="_top">返回首页</a> | <a href="loginout.php" target="_top">安全退出</a> 
        ] [ <a href="http://www.zzcms.net/zx/class/22" target="_blank">操作说明</a> ]
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" height="90%" style="background:#C3DAF9;">
  <tr>
    <td align="middle" valign="top" width="185" id="FrmTitle" height="100%">
	<iframe frameborder="0" id="frmleft" name="frmleft" src="left.php" style="height:100%; visibility: inherit;width: 185px;" allowtransparency="true"></iframe>
	</td>
	  <td width="18"  valign="middle" height="100%"> 
        <div onClick="switchSysBar()"> <span class="navpoint" id="switchPoint" title="关闭/打开左栏"><img src="image/manage_right.gif" alt="" /></span> 
        </div>
	</td>
	<td valign="top" height="100%">
<iframe frameborder="0" id="frmright" name="frmright" scrolling="yes" src="right.php" style="height:100%; visibility: inherit; width:100%; z-index:1;"></iframe>
	</td>
  </tr>
</table>
</body>
</html>