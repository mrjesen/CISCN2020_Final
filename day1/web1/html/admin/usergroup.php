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

//定义正则表达式部分
var strP=/^\d+$/;
if(!strP.test(document.form1.groupid.value)) {
alert("用户组ID只能填数字！"); 
document.form1.groupid.focus(); 
return false; 
}

if(!strP.test(document.form1.RMB.value)) {
alert("所需费用只能填数字！"); 
document.form1.RMB.focus(); 
return false; 
}  

if(!strP.test(document.form1.refresh_number.value)) {
alert("每天刷新次数需填写数字！"); 
document.form1.refresh_number.focus(); 
return false; 
} 

if(!strP.test(document.form1.addinfo_number.value)) {
alert("每天发布信息数需填写数字！"); 
document.form1.addinfo_number.focus(); 
return false; 
} 

if(!strP.test(document.form1.addinfototle_number.value)) {
alert("发布信息总数需填写数字！"); 
document.form1.addinfototle_number.focus(); 
return false; 
} 

if(!strP.test(document.form1.looked_dls_number_oneday.value)) {
alert("每天查看<?php echo channeldl?>商信息数需填写数字！"); 
document.form1.looked_dls_number_oneday.focus(); 
return false; 
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
default;show();//列表页在同页内，add,modify,save,show四项都用function封装，这样输出writeerrormsg时不显示内容
}

function show(){
global $action,$groupid;
if (@$action=="del"){
checkadminisdo("siteconfig");
checkid($groupid);

if  ($groupid<>"") {
	query("delete from zzcms_usergroup where groupid='$groupid'");
}
echo "<script>location.href='?'</script>";      
}
?>

<div class="admintitle">用户组管理</div>
<div class="border2 center"><input type="submit" class="buttons" onClick="javascript:location.href='?do=add'" value="添加用户组"></div>
<?php
$sql="select * from zzcms_usergroup";
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "暂无信息";
}else{
?>

<table width="100%" border="0" cellpadding="5" cellspacing="1" >
  <tr class="trtitle"> 
    <td width="10%" align="center" ><strong>用户组名称</strong></td>
    <td width="10%" align="center" ><strong>等级图片</strong></td>
    <td width="10%" align="center" ><strong>用户组ID</strong></td>
    <td width="10%" align="center"><strong>所需费用</strong></td>
    <td width="50%" ><strong>用户权限</strong> (注：没有相应权限的用户组，可用积分换得相应权限，关闭<a href="SiteConfig.php#userjf" target="_self">积分功能</a>后，则不再有此权限。)</td>
    <td width="10%" align="center" ><strong>操作选项</strong></td>
  </tr>
  <?php
while($row=fetch_array($rs)){
?>
   <tr class="trcontent">  
    <td align="center"><?php echo $row["groupname"]?></td>
    <td align="center"><img src="../<?php echo $row["grouppic"]?>"></td>
    <td align="center"><?php echo $row["groupid"]?></td>
    <td align="center"><?php echo $row["RMB"]?>积分/年</td>
    <td> <table width="100%" border="0" cellpadding="3" cellspacing="0">
        <tr> 
          <td width="42%" align="right">查看<?php echo channeldl?>商信息库联系方式：</td>
          <td width="58%"> 
            <?php if (strpos($row["config"],'look_dls_data')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr> 
          <td align="right">查看<?php echo channeldl?>商留言联系方式：</td>
          <td> 
            <?php if (strpos($row["config"],'look_dls_liuyan')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr> 
          <td align="right">显示注册信息的联系方式：</td>
          <td> 
            <?php if (strpos($row["config"],'showcontact')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr> 
          <td align="right">打印<?php echo channeldl?>商留言：</td>
          <td> 
            <?php if (strpos($row["config"],'dls_print')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr> 
          <td align="right">下载<?php echo channeldl?>商留言：</td>
          <td> 
            <?php if (strpos($row["config"],'dls_download')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr> 
          <td align="right">抢占广告位：</td>
          <td> 
            <?php if (strpos($row["config"],'set_text_adv')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr> 
          <td align="right">置顶信息：</td>
          <td> 
            <?php if (strpos($row["config"],'set_elite')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr>
          <td align="right">信息免审：</td>
          <td><?php if (strpos($row["config"],'passed')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr>
          <td align="right">上传视频：</td>
          <td><?php if (strpos($row["config"],'uploadflv')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr>
          <td align="right">seo：</td>
          <td><?php if (strpos($row["config"],'seo')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr>
          <td align="right">装修展厅：</td>
          <td><?php if (strpos($row["config"],'set_zt')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr>
          <td align="right">在展厅内显网站上其它用户的广告：</td>
          <td><?php if (strpos($row["config"],'showad_inzt')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>          </td>
        </tr>
        <tr> 
          <td align="right">每天刷新次数：</td>
          <td><?php echo $row["refresh_number"]?></td>
        </tr>
        <tr> 
          <td align="right">每天发布信息数/栏目：</td>
          <td><?php echo $row["addinfo_number"]?></td>
        </tr>
        <tr> 
          <td align="right">发布信息总数/栏目：</td>
          <td><?php echo $row["addinfototle_number"]?></td>
        </tr>
        <tr>
          <td align="right">每天查看<?php echo channeldl?>商信息数：</td>
          <td><?php if ($row["looked_dls_number_oneday"]==999 ){echo  "不限制"; }else{ echo $row["looked_dls_number_oneday"];}?>          </td>
        </tr>
        <tr> 
          <td align="right">选择招商展示页模板：</td>
          <td> 
            <?php if (strpos($row["config"],'zsshow_template')!==false){echo "<font color=green>√</font>"; }else{ echo"<font color=red>×</font>"; }?>         </td>
        </tr>
     </table></td>
    <td align="center" class="docolor"> <a href="?do=modify&id=<?php echo $row["id"]?>">修改</a> 
      | 
     <a href="?action=del&groupid=<?php echo $row["groupid"]?>" onClick="return ConfirmDelBig();">删除</a></td>
  </tr>
  <?php
  }
  ?>
</table>
<?php
  }
} 


function save(){

global $action,$id,$groupid,$oldgroupid,$groupname,$grouppic,$RMB,$refresh_number,$addinfo_number,$addinfototle_number,$looked_dls_number_oneday;
$FoundErr=0;
$ErrMsg="";

$config="";
if (isset($_POST['config'])){
foreach( $_POST['config'] as $i){$config .=$i."#";}
$config=substr($config,0,strlen($config)-1);//去除最后面的"#"
}

checkid($groupid);

if (@$action=="add") {
checkadminisdo("usergroup");

	$sql="Select * From zzcms_usergroup Where groupid='".$groupid."'";
	$rs=query($sql);
	$row=num_rows($rs);
	if ($row){
		$FoundErr=1;
		$ErrMsg=$ErrMsg."<li>用户组ID“" . $groupid . "”已经存在！</li>";
	}
	
	
	$sql="select * from zzcms_usergroup where groupname='" . $groupname . "'";
	$rs=query($sql);
	$row=num_rows($rs);
	if ($row){
		$FoundErr=1;
		$ErrMsg=$ErrMsg . "<li>“" . $groupname . "”已经存在！</li>";
	}
		
		
	if ($FoundErr==1) {
	WriteErrMsg($ErrMsg);
	}else{
		query("insert into zzcms_usergroup (
		groupname,grouppic,groupid,RMB,config,
		refresh_number,addinfo_number,addinfototle_number,looked_dls_number_oneday
		)values(
		'$groupname','$grouppic','$groupid','$RMB','$config',
		'$refresh_number','$addinfo_number','$addinfototle_number','$looked_dls_number_oneday'
		)");
		echo "<script>location.href='?'</script>";  
	}
}elseif (@$action=="modify"){
	checkadminisdo("usergroup");
	checkid($id);
	query("update zzcms_usergroup set groupname='$groupname',grouppic='$grouppic',groupid='$groupid',RMB='$RMB',config='$config',
	refresh_number='$refresh_number',addinfo_number='$addinfo_number',addinfototle_number='$addinfototle_number',
	looked_dls_number_oneday='$looked_dls_number_oneday' where id='$id'");
	if ($groupid<>$oldgroupid){
	query("Update zzcms_user set groupid='" . $groupid . "' where groupid='" . $oldgroupid."'");
	query("Update zzcms_main set groupid='" . $groupid . "' where groupid='" . $oldgroupid."'");
    }		
	echo "<script>location.href='?'</script>";
}
}


function add(){
?>
<div class="admintitle">添加用户组</div>
<form name="form1" method="post" action="?do=save" onSubmit="return checkform()">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr>
      <td width="20%" align="right" class="border">用户组名称</td>
      <td width="80%" class="border"><input name="groupname" type="text" maxlength="30"></td>
    </tr>
    
	<tr>
      <td align="right" class="border">等级图片</td>
      <td class="border"><input name="grouppic" type="text" id="grouppic" maxlength="30"></td>
    </tr>
	
    <tr>
      <td align="right" class="border">用户组ID</td>
      <td class="border"><input name="groupid" type="text" id="groupid" maxlength="30">
        （填数字 ）</td>
    </tr>
	
    <tr>
      <td align="right" class="border">所需费用</td>
      <td class="border"><input name="RMB" type="text" id="RMB" maxlength="30">(积分/年，填数字) </td>
    </tr>
	
    <tr>
      <td align="right" class="border">给权限</td>
      <td class="border"><label><input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
          全选/取消全选</label>
          <br>
        <label><input type="checkbox" name="config[]" value="look_dls_data"> 查看<?php echo channeldl?>商数据库联系方式</label>
        <label><input type="checkbox" name="config[]" value="look_dls_liuyan">查看<?php echo channeldl?>商留言联系方式</label>
        <label><input type="checkbox" name="config[]" value="dls_print">打印<?php echo channeldl?>留言</label>
        <label><input type="checkbox" name="config[]" value="dls_download">下载<?php echo channeldl?>留言</label>
          <br>
        <label><input type="checkbox" name="config[]" value="set_text_adv">抢占广告位</label>
        <label><input type="checkbox" name="config[]" value="set_elite"> 置顶信息</label>
        <label><input type="checkbox" name="config[]" value="uploadflv">上传视频</label>
        <label><input type="checkbox" name="config[]" value="set_zt" >装修展厅</label>
        <label><input type="checkbox" name="config[]" value="passed" >  信息免审</label>
        <label><input type="checkbox" name="config[]" value="seo" >  SEO设置</label>
        <br/>
        <label><input type="checkbox" name="config[]" value="showcontact" >显示注册信息的联系方式</label>
        <label><input type="checkbox" name="config[]" value="showad_inzt"> 在展厅内显网站上其它用户的广告(VIP会员建议不选)</label>
		<label><input type="checkbox" name="config[]" value="zsshow_template"> 选择招商展示页模板</label>
        
      </td>
    </tr>
	
    <tr>
      <td align="right"  class="border">每天刷新次数</td>
      <td class="border"><input name="refresh_number" type="text" id="refresh_number" maxlength="30"></td>
    </tr>
	
    <tr>
      <td align="right"  class="border">每天发布信息数/栏目</td>
      <td  class="border"><input name="addinfo_number" type="text" id="addinfo_number" maxlength="30"></td>
    </tr>
	
    <tr>
      <td align="right"  class="border">发布信息总数/栏目</td>
      <td  class="border"><input name="addinfototle_number" type="text" id="addinfototle_number" maxlength="30"></td>
    </tr>
	
    <tr>
      <td align="right"  class="border">每天查看<?php echo channeldl?>商信息数</td>
      <td  class="border"><input name="looked_dls_number_oneday" type="text" id="looked_dls_number_oneday" maxlength="30">(填999为不限制)</td>
    </tr>
	
    <tr>
      <td  class="border">&nbsp;</td>
      <td  class="border"><input name="add" type="submit" value=" 添 加 ">
      <input name="action" type="hidden" id="action" value="add"></td>
    </tr>
  </table>
</form>
<?php
}


function modify(){

$id=isset($_REQUEST['id'])?$_REQUEST['id']:0;
checkid($id,1);

$sql="Select * from zzcms_usergroup where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>

<div class="admintitle">修改用户组</div>
<form name="form1" method="post" action="?do=save" onSubmit="return checkform()">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="20%"  align="right" class="border">用户组名称</td>
      <td width="80%" class="border"> <input name="groupname" type="text" value="<?php echo $row["groupname"]?>" maxlength="30">      </td>
    </tr>
    <tr> 
      <td  align="right" class="border">等级图片</td>
      <td  class="border"> <input name="grouppic" type="text" id="grouppic" value="<?php echo $row["grouppic"]?>" maxlength="30">      </td>
    </tr>
    <tr> 
      <td height="11" align="right" class="border">用户组ID</td>
      <td height="11" class="border"><input name="groupid" type="text" id="groupid" value="<?php echo $row["groupid"]?>" size="4" maxlength="4">
        (为必免用户信息及产品信息排序混乱，不要随意改ID值) 
        <input name="oldgroupid" type="hidden" id="oldgroupid" value="<?php echo $row["groupid"]?>"></td>
    </tr>
    <tr> 
      <td height="11" align="right" class="border">所需费用</td>
      <td height="11" class="border"><input name="RMB" type="text" id="RMB" value="<?php echo $row["RMB"]?>" size="4" maxlength="30">
        (积分/年) </td>
    </tr>
    <tr>
      <td align="right" class="border">给权限
      </td>
      <td class="border"> <label><input name="chkAll" type="checkbox" onClick="CheckAll(this.form)" value="checkbox">全选/取消全选</label>
          <br>
<label> <input type="checkbox" name="config[]" value="look_dls_data"  <?php if(str_is_inarr($row["config"],'look_dls_data')=='yes'){echo "checked";}?>>
查看<?php echo channeldl?>商数据库联系方式</label>
<label> <input type="checkbox" name="config[]" value="look_dls_liuyan"  <?php if(str_is_inarr($row["config"],'look_dls_liuyan')=='yes'){echo "checked";}?>>
查看<?php echo channeldl?>商留言联系方式</label>
<label><input type="checkbox" name="config[]" value="dls_print"  <?php if(str_is_inarr($row["config"],'dls_print')=='yes'){echo "checked";}?>>
打印<?php echo channeldl?>留言</label>
<label><input type="checkbox" name="config[]" value="dls_download"  <?php if(str_is_inarr($row["config"],'dls_download')=='yes'){echo "checked";}?>>下载<?php echo channeldl?>留言</label>
<br>
<label><input type="checkbox" name="config[]" value="set_text_adv" <?php if(str_is_inarr($row["config"],'set_text_adv')=='yes'){echo "checked";}?>>抢占广告位</label>
<label><input type="checkbox" name="config[]" value="set_elite"  <?php if(str_is_inarr($row["config"],'set_elite')=='yes'){echo "checked";}?>>置顶信息</label>
<label><input type="checkbox" name="config[]" value="uploadflv"  <?php if(str_is_inarr($row["config"],'uploadflv')=='yes'){echo "checked";}?>>上传视频</label>
<label><input type="checkbox" name="config[]" value="set_zt"  <?php if(str_is_inarr($row["config"],'set_zt')=='yes'){echo "checked";}?>>装修展厅</label>
<label><input type="checkbox" name="config[]" value="passed" <?php if(str_is_inarr($row["config"],'passed')=='yes'){echo "checked";}?>>信息免审</label>
<label><input type="checkbox" name="config[]" value="seo"  <?php if(str_is_inarr($row["config"],'seo')=='yes'){echo "checked";}?>>SEO设置</label>
<br/>
<label> <input type="checkbox" name="config[]" value="showcontact" <?php if(str_is_inarr($row["config"],'showcontact')=='yes'){echo "checked";}?>>显示注册信息的联系方式</label>
<label><input type="checkbox" name="config[]" value="showad_inzt" <?php if(str_is_inarr($row["config"],'showad_inzt')=='yes'){echo "checked";}?>>
在展厅内显网站上其它用户的广告(VIP会员建议不选)</label>
<label><input type="checkbox" name="config[]" value="zsshow_template" id="zsshow_template" <?php if(str_is_inarr($row["config"],'zsshow_template')=='yes'){echo "checked";}?>>选择招商展示页模板 </label></td>
    </tr>
    <tr> 
      <td align="right"  class="border">每天刷新次数</td>
      <td  class="border"><input name="refresh_number" type="text" id="refresh_number" value="<?php echo $row["refresh_number"]?>" size="4" maxlength="30">      </td>
    </tr>
    <tr> 
      <td align="right"  class="border">每天发布信息数/栏目</td>
      <td  class="border"><input name="addinfo_number" type="text" id="addinfo_number" value="<?php echo $row["addinfo_number"]?>" size="4" maxlength="30"></td>
    </tr>
    <tr> 
      <td align="right"  class="border">发布信息总数/栏目</td>
      <td  class="border"><input name="addinfototle_number" type="text" id="addinfototle_number" value="<?php echo $row["addinfototle_number"]?>" size="4" maxlength="30"></td>
    </tr>
    <tr> 
      <td align="right"  class="border">每天查看<?php echo channeldl?>商信息数</td>
      <td  class="border"><input name="looked_dls_number_oneday" type="text" id="looked_dls_number_oneday" value="<?php echo $row["looked_dls_number_oneday"]?>" size="4" maxlength="30">
        (填999为不限制)</td>
    </tr>
    <tr> 
      <td class="border">&nbsp;</td>
      <td class="border"> <input name="id" type="hidden" id="id" value="<?php echo $row["id"]?>"> 
        <input name="action" type="hidden" id="action" value="modify">
		 <input name="save" type="submit" id="Save" value="修改">      </td>
    </tr>
  </table>
</form>
<?php
}
?>
</body>
</html>