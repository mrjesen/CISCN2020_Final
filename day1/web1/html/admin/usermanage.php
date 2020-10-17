<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../js/gg.js"></script>
</head>
<?php
checkadminisdo("user");//本页涉及到用户密码信息，验证权限放在开始的地方
$action=isset($_GET["action"])?$_GET["action"]:'';
$page=isset($_GET["page"])?$_GET["page"]:1;
checkid($page);
$shenhe=isset($_GET["shenhe"])?$_GET["shenhe"]:'';
$keyword=isset($_GET["keyword"])?$_GET["keyword"]:'';//翻页中会以GET传值 

$px=isset($_GET["px"])?$_GET["px"]:'id';
$usersf=isset($_GET["usersf"])?$_GET["usersf"]:'';

if ($action=="pass"){
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$_POST['id'][$i];
	checkid($id);
	$sql="select passed from zzcms_user where id ='$id'";
	$rs = query($sql); 
	$row = fetch_array($rs);
		if ($row['passed']=='0'){
		query("update zzcms_user set passed=1 where id ='$id'");
		}else{
		query("update zzcms_user set passed=0 where id ='$id'");
		}
	}
}else{
echo "<script>alert('操作失败！至少要选中一条信息。');history.back()</script>";
}
echo "<script>location.href='?keyword=".$keyword."&page=".$page."'</script>";	
}
?>
<body>
<div class="admintitle">用户管理</div>
<form name="form1" method="get" action="?">
      <div class="border2">
        <input name="keyword" type="text" id="keyword" value="<?php echo $keyword?>" size="30" maxlength="255">
        <input type="submit" name="Submit2" value="查寻">
<span>排序方式：<a href="?px=lastlogintime">按登录时间</a><a href="?px=logins">按登录次数</a><a href="?usersf=vip">VIP用户</a><a href="?usersf=lockuser">锁定的用户</a><a href="?usersf=elite">置顶的用户</a><a href="?usersf=person">个人用户</a></span></div>
  
</form>
<?php
$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select count(*) as total from zzcms_user where id<>0 ";
$sql2='';
if ($shenhe=="no") {  		
$sql2=$sql2." and passed=0 ";
}

if ($keyword<>"") {//取消kind选择，让程序判断输入内容类型
if( preg_match("/^[a-zA-Z0-9_.]+@([a-zA-Z0-9_]+.)+[a-zA-Z]{2,3}$/",$keyword)) {//email
$sql2=$sql2. " and email like '%".$keyword."%'";

}elseif( preg_match('/^400(\d{3,4}){2}$/',$keyword) && preg_match('/^400(-\d{3,4}){2}$/',$keyword) && preg_match('/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',$keyword)){//电话号码
$sql2=$sql2. " and tel like '%".$keyword."%' ";

}elseif(preg_match("/1[34578]{1}\d{9}$/",$keyword) ){//手机号
$sql2=$sql2. " and  mobile like '%".$keyword."%' ";

}elseif (preg_match("/([\x81-\xfe][\x40-\xfe])/",$keyword)) {//含有汉字的 
$sql2=$sql2. " and comane like '%".$keyword."%'";

}elseif (is_numeric($keyword)!==false) {
$sql2=$sql2. " and id = '".$keyword."'";

}else{
$sql2=$sql2. " and username like '%".$keyword."%'";
}
}


switch ($usersf){
	case "person";
	$sql2=$sql2." and usersf='个人'";
	break;
	case "vip";
	$sql2=$sql2." and groupid>1";
	break;
	case "lockuser";
	$sql2=$sql2. " and lockuser=1";
	break;
	case "elite";
	$sql2=$sql2. " and elite>0";
	break;
	}
$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];   
$totlepage=ceil($totlenum/$page_size);

$sql="select * from zzcms_user where id<>0 ";
$sql=$sql.$sql2;
$sql=$sql . " order by ".$px." desc limit $offset,$page_size";

$rs = query($sql); 
if(!$totlenum){
echo "暂无信息";
}else{
?>
<form name="myform" method="post" action="" onSubmit="return anyCheck(this.form)">
  <table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr class="trtitle"> 
      <td width="5%" align="center"> <label for="chkAll" style="cursor: pointer;">全选</label> </td>
      <td width="8%" align="center"> 用户名 | 密码</td>
      <td width="12%" align="center" >公司名称</td>
      <td width="5%" align="center" >企业类型</td>
      <td width="5%" align="center" >用户组</td>
      <td width="5%" align="center" >登录次数</td>
      <td width="10%" align="center" >最后登录IP</td>
      <td width="5%" align="center"  title="最后登录时间">最后登录</td>
      <td width="5%" align="center" >注册时间</td>
      <td width="5%" align="center" > 状态</td>
      <td width="10%" align="center" > 操作</td>
    </tr>
    <?php
while($row = fetch_array($rs)){
?>
     <tr class="trcontent">  
      <td align="center" class="docolor"> <input name="id[]" type="checkbox"  value="<?php echo $row["id"]?>"> 
       <a name="<?php echo $row["id"]?>"></a></td>
      <td align="center">
	  <a href="<?php echo getpageurl("zt",$row["id"])?>" target="_blank"><?php echo str_replace($keyword,"<font color=red>".$keyword."</font>",$row["username"])?></a>
<?php
	$rsn=query("select config from zzcms_admingroup where id=(select groupid from zzcms_admin where pass='".@$_COOKIE["pass"]."' and admin='".@$_COOKIE["admin"]."')");//只验证密码会出现，两个管理员密码相同的情况，导致出错,前加@防止_COOKIE失效后出错提示
	$rown=fetch_array($rsn);
	if(str_is_inarr($rown["config"],'user')=='no'){
		echo ""; 
	}else{
		echo " | ";
		if ($row["passwordtrue"]!=''){ echo $row["passwordtrue"];}else{ echo $row["password"];} 
	}
?>
	  </td>
      <td> 
        <?php if ($row["comane"]<>"") {
	echo  str_replace($keyword,"<font color=red>".$keyword."</font>",$row["comane"]);
	}else{
	echo  "个人用户";
	}
	?>
      </td>
      <td align="center"> 
        <?php
	  if ($row["bigclassid"]<>"" && $row["bigclassid"]<>0 ){
	  $rskind=query("select classname from zzcms_userclass where classid=".$row["bigclassid"]."");
	  $r=fetch_array($rskind);
	  echo  $r["classname"];
	  }
	  ?>
      </td>
      <td align="center"> <?php
	$rsn=query("select groupname from zzcms_usergroup where groupid='".$row["groupid"]."'");
	$rown=fetch_array($rsn);
	   echo $rown["groupname"]?> </td>
      <td align="center"><?php echo $row["logins"]?></td>
      <td><?php echo $row["loginip"]?></td>
      <td title="<?php echo $row["lastlogintime"]?>"><?php echo date("Y-m-d",strtotime($row["lastlogintime"]))?></td>
      <td title="<?php echo $row["regdate"]?>"><?php echo date("Y-m-d",strtotime($row["regdate"]))?></td>
      <td align="center"><?php
	  if ($row["lockuser"]==1) {
	  	echo  "<font color=red>锁定</font><br>";
	  }
	  if ($row["passed"]==1) {
	  	echo  "已审";
	  }else{
	  	echo  "<font color=red>未审</font>";
	  }
	  if ($row["elite"]>0) {
	  echo "<br>置顶(".$row["elite"].")";
	  }
	  ?></td>
      <td align="center" class="docolor"><a href="usermodify.php?id=<?php echo $row["id"]?>">修改</a> |
        <?php if ($row["lockuser"]==0) { ?>
        <a href="userlock.php?action=lock&id=<?php echo $row["id"]?>&page=<?php echo $page?>">锁定</a> 
        <?php
		}else{
		?>
        <a href="userlock.php?action=cancellock&id=<?php echo $row["id"]?>&page=<?php echo $page?>">解锁</a> 
        <?php
		}
		?>
        | <a href="sendmail.php?tomail=<?php echo $row["email"]?>">发信</a> </td>
    </tr>
    <?php
   }
   ?>
  </table>

  <div class="border"><input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
        <label for="chkAll" style="cursor: pointer;">全选</label>  
        <input name="submit2"  type="submit" onClick="myform.action='?action=pass'" value="【取消/审核】选中的信息"> 
        <input name="submit4" type="submit" onClick="myform.action='deluser.php';myform.target='_self';return ConfirmDel()" value="【删除】选中的信息">
        <input name="page" type="hidden" id="page" value="<?php echo $page?>"> 
        <input name="pageurl" type="hidden"  value="usermanage.php?keyword=<?php echo $keyword?>&shenhe=<?php echo $shenhe?>&page=<?php echo $page ?>">
       </div>
</form>

    <div class="border center">	页次：<strong><font color="#CC0033"><?php echo $page?></font>/<?php echo $totlepage?>　</strong> 
      <strong><?php echo $page_size?></strong>条/页　共<strong><?php echo $totlenum ?></strong>条
<?php
		$cs="px=".$px."&usersf=".$usersf."&keyword=".$keyword."&shenhe=".$shenhe;
		if ($page<>1) {
			echo "【<a href='?".$cs."&page=1'>首页</a>】 ";
			echo "【<a href='?".$cs."&page=".($page-1)."'>上一页</a>】 ";
		}else{
			echo "【首页】【上一页】";
		}
		if ($page<>$totlepage) {
			echo "【<a href='?".$cs."&page=".($page+1)."'>下一页</a>】 ";
			echo "【<a href='?".$cs."&page=".$totlepage."'>尾页</a>】 ";
		}else{
			echo "【下一页】【尾页】";
		}       
	?>
    </div>

<?php
}
?>
</body>
</html>