<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/js/gg.js"></script>
<script language="JavaScript" type="text/JavaScript">
function ConfirmDelBig(){
   if(confirm("确定要删除此用户组吗！"))
     return true;
   else
     return false;	 
}
function checkform(){
	if (document.form1.groupname.value==""){
    alert("用户组名称不能为空！");
    document.form1.groupname.focus();
    return false;
	}
}
function CheckAll(form){
	for (var i=0;i<form.elements.length;i++){
    var e = form.elements[i];
    if (e.Name != "chkAll"){
    	e.checked = form.chkAll.checked;
    }
	}
}	
</script>
</head>
<body>
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
case "save";save();break;
default;show();
}


function save(){
checkadminisdo("admingroup");
$action=$_POST["action"];
$groupname=$_POST["groupname"];//或用global获取$groupname的值
$id = isset($_POST['id'])?$_POST['id']:0;
checkid($id,1);
$FoundErr=0;
$config="";
if (isset($_POST['config'])){
foreach($_POST['config'] as $i){$config .=$i."#";}
$config=substr($config,0,strlen($config)-1);//去除最后面的"#"
}


if ($action=="add"){	
	$sql="Select * From zzcms_admingroup where groupname='".$groupname."'";	
	$rs = query($sql);
	$row= num_rows($rs);//返回记录数
	if($row){ 
			$FoundErr=1;
			$ErrMsg="<li>用户组名称“" . $groupname . "”已经存在！</li>";
			WriteErrMsg($ErrMsg);
	}else{
	$sql="insert into zzcms_admingroup (groupname,config)values('$groupname','$config')";
	query($sql);
	echo "<script>location.href='?'</script>";
	}
}elseif($action=="modify"){
$sql="update zzcms_admingroup set groupname='$groupname',config='$config' where id='$id'";
$isok=query($sql);

if ($isok){
echo "<script>alert('修改成功');location.href='?do=modify&id=$id'</script>";
}else{
echo "<script>alert('修改失败');location.href='?'</script>";
}
}
}

function show(){
?>
<div class="admintitle">管理员组管理</div>
<div class="border2 center"><input  type="submit" class="buttons" onClick="javascript:location.href='?do=add'" value="添加管理组"></div>
<?php
$action = isset($_REQUEST['action'])?$_REQUEST['action']:"";

if ($action=="del" ){
checkadminisdo("admingroup");
$groupname=trim($_GET["groupname"]);
$id=trim($_GET["id"]);
if  ($groupname<>""){
	$sql="delete from zzcms_admingroup where groupname='" . $groupname . "'";
	query($sql);
} 
echo  "<script>location.href='?'</script>";
}

$sql="select * from zzcms_admingroup";
$rs = query($sql); 
?>
<table width="100%" border="0" cellpadding="5" cellspacing="1" >
  <tr class="trtitle"> 
    <td width="10%" align="center">ID</td>
    <td width="29%" align="center">名称</td>
    <td width="51%" align="center">权限</td>
    <td width="10%" align="center">操作选项</td>
  </tr>
  <?php
	while($row= fetch_array($rs)){
?>
   <tr class="trcontent"> 
    <td width="10%" align="center"><?php echo $row["id"] ?></td>
    <td align="center"><?php echo $row["groupname"]?></td>
    <td align="center"><?php echo $row["config"]?></td>
    <td align="center" class="docolor">
     
	  <a href="?do=modify&id=<?php echo $row["id"] ?>">修改</a> | 
	  <a href="?action=del&groupname=<?php echo $row["groupname"] ?>&id=<?php echo $row["id"] ?>" onClick="return ConfirmDelBig();">删除</a>	  </td>
  </tr>
  <?php 
  }
   ?>
</table>
<?php
}


function add(){
?>
<div class="admintitle">添加管理组</div>
<form name="form1" method="post" action="?do=save" onSubmit="return checkform()">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="24%" align="right" class="border" >管理组名称</td>
      <td width="76%" class="border" > <input name="groupname" type="text" maxlength="30">      </td>
    </tr>
    <tr>
      <td align="right" class="border" ><?php echo channelzs?></td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="zs">查看</label>
		<label><input name="config[]" type="checkbox" value="zs_modify">修改</label>
		<label><input name="config[]" type="checkbox" value="zs_del"> 删除</label>
        <label><input name="config[]"  type="checkbox"  value="zsclass">类别管理</label>
        <label><input name="config[]" type="checkbox" value="zskeyword">关键字管理 </label></td>
    </tr>
    <tr>
      <td align="right" class="border" ><?php echo channeldl?></td>
      <td class="border" > <label><input name="config[]" type="checkbox"  value="dl">查看</label>
	<label><input name="config[]" type="checkbox" value="dl_add">添加</label>
	<label><input name="config[]" type="checkbox" value="dl_modify">修改</label>
	<label><input name="config[]" type="checkbox" value="dl_del"> 删除</label>
	<label><input name="config[]" type="checkbox" value="guestbook">展厅留言管理</label>	  </td>
    </tr>
    <tr>
      <td align="right" class="border" >展会</td>
      <td class="border" >
<label><input name="config[]" type="checkbox"  value="zh">查看 </label>
<label><input name="config[]" type="checkbox" value="zh_add">添加</label>
<label><input name="config[]" type="checkbox" value="zh_modify">修改</label>
<label><input name="config[]" type="checkbox" value="zh_del"> 删除</label>
<label><input name="config[]" type="checkbox"  value="zhclass">类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >资讯</td>
      <td class="border" ><label><input name="config[]" type="checkbox"  value="zx">查看 </label>
<label><input name="config[]" type="checkbox" value="zx_add">添加</label>
<label><input name="config[]" type="checkbox" value="zx_modify">修改</label>
<label><input name="config[]" type="checkbox" value="zx_del"> 删除</label>
        <label><input name="config[]" type="checkbox" value="zxclass">类别管理 </label>
        <label><input name="config[]" type="checkbox" value="zxpinglun">评论管理 </label>
       <label> <input name="config[]" type="checkbox"  value="zxtag">资讯广告标签管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >品牌</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="pp">查看</label>
<label><input name="config[]" type="checkbox" value="pp_modify">修改</label>
<label><input name="config[]" type="checkbox" value="pp_del"> 删除</label>	  </td>
    </tr>
    <tr>
      <td align="right" class="border" >招聘</td>
      <td class="border" ><label><input name="config[]" type="checkbox"  value="job">查看 </label>
<label><input name="config[]" type="checkbox" value="job_modify">修改</label>
<label><input name="config[]" type="checkbox" value="job_del"> 删除</label>
           <label><input name="config[]"  type="checkbox" value="jobclass">类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >专题</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="special">查看 </label>
<label><input name="config[]" type="checkbox" value="special_add">添加</label>
<label><input name="config[]" type="checkbox" value="special_modify">修改</label>
<label><input name="config[]" type="checkbox" value="special_del"> 删除</label>
          <label><input name="config[]" type="checkbox" value="specialclass">类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >网刊</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="wangkan">查看 </label>
<label><input name="config[]" type="checkbox" value="wangkan_add">添加</label>
<label><input name="config[]" type="checkbox" value="wangkan_modify">修改</label>
<label><input name="config[]" type="checkbox" value="wangkan_del"> 删除</label>	  
         <label> <input name="config[]" type="checkbox" value="wangkanclass">类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >报价</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="baojia">查看 </label>
<label><input name="config[]" type="checkbox" value="baojia_modify">修改</label>
<label><input name="config[]" type="checkbox" value="baojia_del"> 删除</label>	  </td>
    </tr>
    <tr>
      <td align="right" class="border" >问答</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="ask">查看 </label>
<label><input name="config[]" type="checkbox" value="ask_add">添加</label>
<label><input name="config[]" type="checkbox" value="ask_modify">修改</label>
<label><input name="config[]" type="checkbox" value="ask_del"> 删除</label>	 	  
       <label> <input name="config[]"  type="checkbox" value="askclass">类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >广告</td>
      <td class="border" ><label><input name="config[]"  type="checkbox" value="adv">查看 </label>
<label><input name="config[]" type="checkbox" value="adv_add">添加</label>
<label><input name="config[]" type="checkbox" value="adv_modify">修改</label>
<label><input name="config[]" type="checkbox" value="adv_del"> 删除</label>	  
        <label><input name="config[]" type="checkbox"  value="advclass">类别管理</label>
        <label><input name="config[]" type="checkbox"  value="adv_user">用户审请的广告管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >用户</td>
      <td class="border" >
<label><input name="config[]"  type="checkbox" value="user">查看</label>
<label><input name="config[]" type="checkbox" value="user_modify">修改</label>
<label><input name="config[]" type="checkbox" value="user_del"> 删除</label>	 	  
        <label><input name="config[]" type="checkbox"  value="usernoreg">未注册用户管理 </label>
        <label><input name="config[]" type="checkbox"  value="userclass">类别管理</label>
        <label><input name="config[]"  type="checkbox"  value="usergroup">用户组管理</label>       </td>
    </tr>
    <tr>
      <td align="right" class="border" >友情链接</td>
      <td class="border" >
	   <label><input name="config[]" type="checkbox" value="friendlink">查看</label>
          <label>
            <input name="config[]" type="checkbox" value="friendlink_add">
            添加</label>
          <label>
            <input name="config[]" type="checkbox" value="friendlink_modify">
            修改</label>
          <label>
            <input name="config[]" type="checkbox" value="friendlink_del">
            删除</label>      </td>
    </tr>
    <tr>
      <td align="right" class="border" >单页</td>
      <td class="border" >  <label><input name="config[]" type="checkbox"   value="about">查看</label>
<label><input name="config[]" type="checkbox" value="about_add">添加</label>
<label><input name="config[]" type="checkbox" value="about_modify">修改</label>
<label><input name="config[]" type="checkbox" value="about_del"> 删除</label>  </td>
    </tr>
    <tr>
      <td align="right" class="border" >模板/标签</td>
      <td class="border" >
       <label><input name="config[]" type="checkbox"  value="label">查看</label>
<label><input name="config[]" type="checkbox" value="label_add">添加</label>
<label><input name="config[]" type="checkbox" value="label_modify">修改</label>
<label><input name="config[]" type="checkbox" value="label_del"> 删除</label>		</td>
    </tr>
    <tr>
      <td align="right" class="border" >用户信息</td>
      <td class="border" ><label>
        <input name="config[]" type="checkbox" value="licence">
        用户资质管理</label>
          <label>
            <input name="config[]"  type="checkbox" value="fankui">
            用户反馈信息管理</label>
          <label>
            <input name="config[]" type="checkbox" value="badusermessage">
            不良用户操作记录管理</label>
      </td>
    </tr>
    <tr>
      <td align="right" class="border" >文件</td>
      <td class="border" >
	  <label><input name="config[]" type="checkbox"  value="uploadfiles">上传文件管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >发信功能</td>
      <td class="border" >
	  <label><input name="config[]" type="checkbox"  value="sendmessage">在线发信息管理 </label>
      <label><input name="config[]"  type="checkbox"  value="sendmail">在线发邮件管理</label>
      <label><input name="config[]" type="checkbox"  value="sendsms">在线发手机短信管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >网站信息</td>
      <td class="border" ><label><input name="config[]"  type="checkbox" value="announcement">网站公告管理</label>
        <label><input name="config[]" type="checkbox" value="helps">网站使用帮助管理</label>       </td>
    </tr>
    <tr>
      <td align="right" class="border" >网站配置</td>
      <td class="border" >
<label><input name="config[]" type="checkbox"  value="siteconfig">网站配置管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >管理员</td>
      <td class="border" >
	  <label><input name="config[]" type="checkbox"  value="adminmanage">管理员管理</label>
      <label> <input name="config[]" type="checkbox"  value="admingroup">
      管理员组管理</label></td>
    </tr>
    <tr> 
      <td align="right" class="border"  ><label><input name="chkAll" type="checkbox" onClick="CheckAll(this.form)" value="checkbox">
      全选/取消全选</label>      </td>
      <td class="border"  > <input name="action" type="hidden" id="action" value="add"> 
        <input name="Add" type="submit" class="buttons" value=" 添 加 "> </td>
    </tr>
  </table>
</form>
<?php
}

function modify(){
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

$sql="select * from zzcms_admingroup where id='$id'";
$rs = query($sql); 
$row= num_rows($rs);
if (!$row){
echo "暂无信息";
}else{
$row= fetch_array($rs);
?>
<div class="admintitle">修改管理员组</div>
<form name="form1" method="post" action="?do=save" onSubmit="return checkform()">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="24%"  align="right" class="border">管理组名称</td>
      <td width="76%" class="border"> <input name="groupname" type="text" value="<?php echo $row['groupname']?>" maxlength="30">      </td>
    </tr>
    <tr> 
      <td align="right" class="border"><?php echo channelzs?></td>
      <td  class="border"> 
	   <label><input name="config[]" type="checkbox"  value="zs" <?php if(str_is_inarr($row["config"],'zs')=='yes'){echo "checked";}?>>
       查看</label> 
<label><input name="config[]" type="checkbox" value="zs_modify" <?php if(str_is_inarr($row["config"],'zs_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="zs_del" <?php if(str_is_inarr($row["config"],'zs_del')=='yes'){echo "checked";}?>> 删除</label>	   
<label><input name="config[]" type="checkbox" value="zsclass" <?php if(str_is_inarr($row["config"],'zsclass')=='yes') { echo"checked";}?>>
        类别管理 </label>
        <label><input name="config[]" type="checkbox" value="zskeyword" <?php if(str_is_inarr($row["config"],'zskeyword')=='yes') { echo"checked";}?>>
        关键字管理 </label></td>
    </tr>
    <tr> 
      <td align="right" class="border"><?php echo channeldl?></td>
      <td  class="border">
	  <label><input name="config[]" type="checkbox" value="dl" <?php if(str_is_inarr($row["config"],'dl')=='yes'){ echo"checked";}?>>
        查看</label> 
<label><input name="config[]" type="checkbox" value="dl_add" <?php if(str_is_inarr($row["config"],'dl_add')=='yes'){echo "checked";}?>>添加</label>
<label><input name="config[]" type="checkbox" value="dl_modify" <?php if(str_is_inarr($row["config"],'dl_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="dl_del" <?php if(str_is_inarr($row["config"],'dl_del')=='yes'){echo "checked";}?>> 删除</label>			 
<label><input name="config[]" type="checkbox"  value="guestbook" <?php if(str_is_inarr($row["config"],'guestbook')=='yes') { echo"checked";} ?>>
        展厅留言管理</label>		 </td>
    </tr>
    <tr> 
      <td  align="right" class="border">展会</td>
      <td  class="border"> 
	   <label><input name="config[]" type="checkbox"  value="zh" <?php if(str_is_inarr($row["config"],'zh')=='yes') { echo"checked";}?>>
        查看 </label>
<label><input name="config[]" type="checkbox" value="zh_add" <?php if(str_is_inarr($row["config"],'zh_add')=='yes'){echo "checked";}?>>添加</label>
<label><input name="config[]" type="checkbox" value="zh_modify" <?php if(str_is_inarr($row["config"],'zh_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="zh_del" <?php if(str_is_inarr($row["config"],'zh_del')=='yes'){echo "checked";}?>> 删除</label>		
        <label><input name="config[]" type="checkbox"  value="zhclass" <?php if(str_is_inarr($row["config"],'zhclass')=='yes') { echo"checked";}?>>
         类别管理</label></td>
    </tr>
    <tr> 
      <td  align="right" class="border">资讯</td>
      <td  class="border"> 
	  <label><input name="config[]" type="checkbox"   value="zx" <?php if(str_is_inarr($row["config"],'zx')=='yes') { echo"checked";}?>>
        查看</label> 
<label><input name="config[]" type="checkbox" value="zx_add" <?php if(str_is_inarr($row["config"],'zx_add')=='yes'){echo "checked";}?>>添加</label>
<label><input name="config[]" type="checkbox" value="zx_modify" <?php if(str_is_inarr($row["config"],'zx_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="zx_del" <?php if(str_is_inarr($row["config"],'zx_del')=='yes'){echo "checked";}?>> 删除</label>		
       <label> <input name="config[]" type="checkbox" value="zxclass" <?php if(str_is_inarr($row["config"],'zxclass')=='yes') { echo"checked";}?>>
        类别管理 </label> 
        <label><input name="config[]" type="checkbox"  value="zxpinglun" <?php if(str_is_inarr($row["config"],'zxpinglun')=='yes') { echo"checked";}?>>
        评论管理 </label> 
        <label><input name="config[]" type="checkbox" value="zxtag" <?php if(str_is_inarr($row["config"],'zxtag')=='yes') { echo"checked";}?>>
        资讯广告标签管理</label> </td>
    </tr>
    <tr>
      <td align="right" class="border" >品牌</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="pp" <?php if(str_is_inarr($row["config"],'pp')=='yes') { echo"checked";}?>>
          查看</label>
<label><input name="config[]" type="checkbox" value="pp_modify" <?php if(str_is_inarr($row["config"],'pp_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="pp_del" <?php if(str_is_inarr($row["config"],'pp_del')=='yes'){echo "checked";}?>> 删除</label>		  </td>
    </tr>
    <tr>
      <td align="right" class="border" >招聘</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="job" <?php if(str_is_inarr($row["config"],'job')=='yes') { echo"checked";}?>>
          查看 </label>
<label><input name="config[]" type="checkbox" value="job_modify" <?php if(str_is_inarr($row["config"],'job_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="job_del" <?php if(str_is_inarr($row["config"],'job_del')=='yes'){echo "checked";}?>> 删除</label>		  
          <label><input name="config[]" type="checkbox" value="jobclass" <?php if(str_is_inarr($row["config"],'jobclass')=='yes') { echo"checked";}?>>
          类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >专题</td>
      <td class="border" >
	  <label><input name="config[]" type="checkbox"  value="special" <?php if(str_is_inarr($row["config"],'special')=='yes') { echo"checked";}?>>
          查看 </label>
<label><input name="config[]" type="checkbox" value="special_add" <?php if(str_is_inarr($row["config"],'special_add')=='yes'){echo "checked";}?>>添加</label>
<label><input name="config[]" type="checkbox" value="special_modify" <?php if(str_is_inarr($row["config"],'special_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="special_del" <?php if(str_is_inarr($row["config"],'special_del')=='yes'){echo "checked";}?>> 删除</label>		  
      <label><input name="config[]"  type="checkbox" value="specialclass" <?php if(str_is_inarr($row["config"],'specialclass')=='yes') { echo"checked";}?>>
          类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >网刊</td>
      <td class="border" > <label><input name="config[]" type="checkbox" value="wangkan" <?php if(str_is_inarr($row["config"],'wangkan')=='yes') { echo"checked";}?>>
         查看 </label>
<label><input name="config[]" type="checkbox" value="wangkan_add" <?php if(str_is_inarr($row["config"],'wangkan_add')=='yes'){echo "checked";}?>>添加</label>
<label><input name="config[]" type="checkbox" value="wangkan_modify" <?php if(str_is_inarr($row["config"],'wangkan_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="wangkan_del" <?php if(str_is_inarr($row["config"],'wangkan_del')=='yes'){echo "checked";}?>> 删除</label>		 
         <label> <input name="config[]"  type="checkbox" value="wangkanclass" <?php if(str_is_inarr($row["config"],'wangkanclass')=='yes') { echo"checked";}?>>
          类别管理</label></td>
    </tr>
    <tr>
      <td align="right" class="border" >报价</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="baojia" <?php if(str_is_inarr($row["config"],'baojia')=='yes') { echo"checked";}?>>
          查看 </label> 
<label><input name="config[]" type="checkbox" value="baojia_modify" <?php if(str_is_inarr($row["config"],'baojia_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="baojia_del" <?php if(str_is_inarr($row["config"],'baojia_del')=='yes'){echo "checked";}?>> 删除</label>		 </td>
    </tr>
    <tr>
      <td align="right" class="border" >问答</td>
      <td class="border" ><label><input name="config[]" type="checkbox" value="ask" <?php if(str_is_inarr($row["config"],'ask')=='yes') { echo"checked";}?>>
          查看 </label>
<label><input name="config[]" type="checkbox" value="ask_add" <?php if(str_is_inarr($row["config"],'ask_add')=='yes'){echo "checked";}?>>添加</label>
<label><input name="config[]" type="checkbox" value="ask_modify" <?php if(str_is_inarr($row["config"],'ask_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="ask_del" <?php if(str_is_inarr($row["config"],'ask_del')=='yes'){echo "checked";}?>> 删除</label>		  
        <label><input name="config[]" type="checkbox" value="askclass" <?php if(str_is_inarr($row["config"],'askclass')=='yes') { echo"checked";}?>>
        类别管理</label></td>
    </tr>
    <tr> 
      <td align="right"  class="border">广告</td>
      <td  class="border"> 
<label><input name="config[]" type="checkbox"  value="adv" <?php if(str_is_inarr($row["config"],'adv')=='yes') { echo"checked";} ?>>查看 </label>
<label><input name="config[]" type="checkbox" value="adv_add" <?php if(str_is_inarr($row["config"],'adv_add')=='yes'){echo "checked";}?>>添加</label>
<label><input name="config[]" type="checkbox" value="adv_modify" <?php if(str_is_inarr($row["config"],'adv_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="adv_del" <?php if(str_is_inarr($row["config"],'adv_del')=='yes'){echo "checked";}?>> 删除</label>
        <label> <input name="config[]" type="checkbox"  value="advclass" <?php if(str_is_inarr($row["config"],'advclass')=='yes') { echo"checked";} ?>>
       类别管理</label> 
        <label><input name="config[]" type="checkbox" value="adv_user" <?php if(str_is_inarr($row["config"],'adv_user')=='yes'){ echo"checked";} ?>>
        用户审请的广告管理</label></td>
    </tr>
    <tr> 
      <td align="right"  class="border">用户</td>
      <td  class="border">
<label><input name="config[]" type="checkbox"  value="user" <?php if(str_is_inarr($row["config"],'user')=='yes') { echo"checked";} ?>>查看 </label>
<label><input name="config[]" type="checkbox" value="user_modify" <?php if(str_is_inarr($row["config"],'user_modify')=='yes'){echo "checked";}?>>修改</label>
<label><input name="config[]" type="checkbox" value="user_del" <?php if(str_is_inarr($row["config"],'user_del')=='yes'){echo "checked";}?>> 删除</label>	   
<label><input name="config[]" type="checkbox" value="usernoreg" <?php if(str_is_inarr($row["config"],'usernoreg')=='yes') { echo"checked";} ?>>未注册用户管理</label>
      <label><input name="config[]" type="checkbox"  value="userclass" <?php if(str_is_inarr($row["config"],'userclass')=='yes') { echo"checked";} ?>>
       类别管理 </label>
    <label><input name="config[]" type="checkbox" value="usergroup" <?php if(str_is_inarr($row["config"],'usergroup')=='yes') { echo"checked";} ?>>用户组管理 </label>       </td>
    </tr>
    <tr>
      <td align="right" class="border" >友情链接</td>
      <td class="border" ><label>
        <input name="config[]" type="checkbox" value="friendlink" <?php if(str_is_inarr($row["config"],'friendlink')=='yes') { echo"checked";} ?>>
        查看</label>
          <label>
          <input name="config[]" type="checkbox" value="friendlink_add" <?php if(str_is_inarr($row["config"],'friendlink_add')=='yes') { echo"checked";} ?>>
            添加</label>
          <label>
          <input name="config[]" type="checkbox" value="friendlink_modify" <?php if(str_is_inarr($row["config"],'friendlink_modify')=='yes') { echo"checked";} ?>>
            修改</label>
          <label>
          <input name="config[]" type="checkbox" value="friendlink_del" <?php if(str_is_inarr($row["config"],'friendlink_del')=='yes') { echo"checked";} ?>>
            删除</label>      </td>
    </tr>
    <tr>
      <td align="right" class="border" >单页</td>
      <td class="border" ><label>
        <input name="config[]" type="checkbox"   value="about" <?php if(str_is_inarr($row["config"],'about')=='yes') { echo"checked";} ?>>
        查看</label>
          <label>
            <input name="config[]" type="checkbox" value="about_add" <?php if(str_is_inarr($row["config"],'about_add')=='yes') { echo"checked";} ?>>
            添加</label>
          <label>
            <input name="config[]" type="checkbox" value="about_modify" <?php if(str_is_inarr($row["config"],'about_modify')=='yes') { echo"checked";} ?>>
            修改</label>
          <label>
            <input name="config[]" type="checkbox" value="about_del" <?php if(str_is_inarr($row["config"],'about_del')=='yes') { echo"checked";} ?>>
            删除</label>      </td>
    </tr>
    <tr>
      <td align="right" class="border" >模板/标签</td>
      <td class="border" ><label>
        <input name="config[]" type="checkbox"  value="label" <?php if(str_is_inarr($row["config"],'label')=='yes') { echo"checked";} ?>>
        查看</label>
          <label>
            <input name="config[]" type="checkbox" value="label_add" <?php if(str_is_inarr($row["config"],'label_add')=='yes') { echo"checked";} ?>>
            添加</label>
          <label>
            <input name="config[]" type="checkbox" value="label_modify" <?php if(str_is_inarr($row["config"],'label_modify')=='yes') { echo"checked";} ?>>
            修改</label>
          <label>
            <input name="config[]" type="checkbox" value="label_del" <?php if(str_is_inarr($row["config"],'label_del')=='yes') { echo"checked";} ?>>
            删除</label>      </td>
    </tr>
    <tr>
      <td align="right"  class="border">用户信息</td>
      <td  class="border"><label>
        <input name="config[]" type="checkbox" value="licence" <?php if(str_is_inarr($row["config"],'licence')=='yes'){ echo"checked";} ?>>
        用户资质管理 </label>
          <label>
            <input name="config[]" type="checkbox" value="fankui" <?php if(str_is_inarr($row["config"],'fankui')=='yes') { echo"checked";} ?>>
            用户反馈信息管理</label>
          <label>
            <input name="config[]" type="checkbox" value="badusermessage" <?php if(str_is_inarr($row["config"],'badusermessage')=='yes'){ echo"checked";} ?>>
            不良用户操作记录管理 </label>
      </td>
    </tr>
    <tr> 
      <td align="right"  class="border">文件</td>
      <td  class="border">
	 <label> <input name="config[]" type="checkbox"  value="uploadfiles" <?php if(str_is_inarr($row["config"],'uploadfiles')=='yes') { echo"checked";} ?>>
        上传文件管理 </label></td>
    </tr>
    <tr> 
      <td align="right"  class="border">发信功能</td>
      <td  class="border">
	   <label><input name="config[]" type="checkbox" value="sendmessage" <?php if(str_is_inarr($row["config"],'sendmessage')=='yes') { echo"checked";} ?>>在线发信息管理</label> 
        <label><input name="config[]" type="checkbox" value="sendmail" <?php if(str_is_inarr($row["config"],'sendmail')=='yes'){ echo"checked";} ?>>
        在线发邮件管理 </label>
       <label> <input name="config[]" type="checkbox" value="sendsms" <?php if(str_is_inarr($row["config"],'sendsms')=='yes'){ echo"checked";} ?>>
        在线发手机短信管理</label> </td>
    </tr>
    <tr> 
      <td align="right"  class="border">网站信息 </td>
      <td  class="border">
	   <label><input name="config[]" type="checkbox" value="announcement" <?php if(str_is_inarr($row["config"],'announcement')=='yes'){ echo"checked";} ?>>
        网站公告管理</label> 
        <label><input name="config[]" type="checkbox" value="helps" <?php if(str_is_inarr($row["config"],'helps')=='yes') { echo"checked";} ?>>
        网站使用帮助管理</label> 
        <label></label><label></label></td>
    </tr>
    <tr> 
      <td align="right"  class="border">网站配置</td>
      <td  class="border">
	  <label><input name="config[]" type="checkbox" value="siteconfig" <?php if(str_is_inarr($row["config"],'siteconfig')=='yes') { echo"checked";} ?>>
        网站配置管理</label><label></label></td>
    </tr>
    <tr> 
      <td align="right"  class="border">管理员</td>
      <td  class="border">
<label><input name="config[]" type="checkbox" value="adminmanage" <?php if(str_is_inarr($row["config"],'adminmanage')=='yes') { echo"checked";} ?>>管理员管理 </label>
<label> <input name="config[]" type="checkbox"  value="admingroup" <?php if(str_is_inarr($row["config"],'admingroup')=='yes'){ echo"checked";} ?>>
        管理员组管理</label></td>
    </tr>
    <tr> 
      <td align="right" class="border">
<label><input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
        全选/取消全选</label></td>
      <td class="border"> <input name="id" type="hidden" id="id" value="<?php echo $row["id"] ?>"> 
        <input name="action" type="hidden" id="action" value="modify"> 
		<input name="Save" type="submit" id="Save" value="修改" class="buttons">      </td>
    </tr>
  </table>

</form>
<?php
}
}
?>
</body>
</html>