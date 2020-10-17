<?php
include("../inc/conn.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href="../template/<?php echo siteskin?>/style.css" rel="stylesheet" type="text/css">
<title></title>
<script src="../3/artDialog/artDialog.js?skin=default"></script> 
<script src="../3/artDialog/plugins/iframeTools.js"></script>
<script>
function OpenAndDataFunc() {
    var dialog = art.dialog.open('../user/login2.php?fromurl=<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>', {
	title: "用户登录",lock: true, width: 400,height: 200}, false);
}

</script>
</head>
<body>
<?php
$founderr=0;
$ErrMsg="";
$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
if (!isset($_COOKIE["UserName"]) || $_COOKIE["UserName"]==""){
echo "<script>OpenAndDataFunc()</script>";
exit;
}
$username=isset($_COOKIE["UserName"])?$_COOKIE["UserName"]:"";
$id="";
$i=0;
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$id.($_POST['id'][$i].',');
    }
	
}else{
	$founderr=1;
	$ErrMsg="<li>操作失败！请先选中要下载的信息</li>";
}
$id=substr($id,0,strlen($id)-1);//去除最后面的","
?>
<div class="main">
<?php
if (check_user_power("dls_print")=="no"){
$founderr=1;
$ErrMsg=$ErrMsg."<li>您所在的用户组没有打印".channeldl."信息的权限！<br><a href='../one/vipuser.php'>升级为VIP会员</a></li>";
}

//判断查看代理条数
$rslookedlsnumber=query("select looked_dls_number_oneday from zzcms_usergroup where groupid=(select groupid from zzcms_user where username='".$username."')");
$rown=fetch_array($rslookedlsnumber);
$lookedlsnumber=$rown["looked_dls_number_oneday"];

$rslookedlsnumbers=query("select looked_dls_number_oneday from zzcms_looked_dls_number_oneday where username='".$username."' and  timestampdiff(day,sendtime,now()) < 3600*24 ");
$rown=num_rows($rslookedlsnumbers);
if ($rown){
	if ($rown["looked_dls_number_oneday"]+$i>$lookedlsnumber){
	$founderr=1;
	$ErrMsg="<li>您所在的用户组每天只能查看 ".$lookedlsnumber." 条".channeldl."信息<br><a href='../one/vipuser.php'>升级为高级会员</a></li>";
	}
}

if ($founderr==1){
WriteErrMsg($ErrMsg);
}else{
	$rslooked=query("select * from zzcms_looked_dls_number_oneday where username='".$username."'");
	$rown=num_rows($rslooked);
	if (!$rown){
	query("insert into zzcms_looked_dls_number_oneday (looked_dls_number_oneday,username,sendtime)values(1,'".$username."','".date('Y-m-d H:i:s')."') ");
	}else{
		if (time()-strtotime($rown["sendtime"])<3600*24){
		query("update zzcms_looked_dls_number_oneday set looked_dls_number_oneday=looked_dls_number_oneday+".$i." where username='".$username."'");
		}else{
		query("update zzcms_looked_dls_number_oneday set looked_dls_number_oneday=".$i.",sendtime='".date('Y-m-d H:i:s')."' where username='".$username."'");
		}
	}
if (strpos($id,",")>0){
	$sql="select * from zzcms_dl where passed=1 and id in (". $id .") ";
	}else{
	$sql="select * from zzcms_dl where passed=1  and id=".$id." order by id desc";
}
	
$rs=query($sql);
echo "<div style=text-align:center><a href='javascript:window.print()'><img src='/image/ico-dy.gif' width='18' height='17' border='0'>打印</a></div>";
$table="<table width=100% cellspacing=0 cellpadding=0 border=0>";
$table=$table."<tr>";
$table=$table."<td width=8%  align=center class=x><strong>序号</strong></td>";
$table=$table."<td width=12%  class=x><b>".channeldl."人</b></td>";
$table=$table."<td width=15%  class=x><b>电话</b></td>";
$table=$table."<td width=22%  class=x><b>".channeldl."产品</b></td>";
$table=$table."<td width=19%  class=x><b>".channeldl."区域</b></td>";
$table=$table."<td width=24%  class=x><b>发布时间</b></td>";
$table=$table."</tr>";
$i=1;
while ($row=fetch_array($rs)){
$table=$table."<tr>\n";
$table=$table."<td width=8%  class=x align=center>".$i."</td>\n";
$table=$table."<td width=12% class=x>".$row['dlsname']."</td>\n";
$table=$table."<td width=15% class=x>".$row['tel']."</td>\n";
$table=$table."<td width=22% class=x>".$row['cp']."</td>\n";
$table=$table."<td width=19% class=x>".$row['province'].$row['city']."</td>\n";
$table=$table."<td width=24% class=x>".$row['sendtime']."</td>\n";
$table=$table."</tr>\n";

$i=$i+1;
}
$table=$table."</table>";
echo $table;
}
?>
</div>
</body>
</html>