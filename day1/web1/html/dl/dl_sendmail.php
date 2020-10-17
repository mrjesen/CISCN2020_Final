<?php
ob_start();//打开缓冲区，可以setcookie
include("../inc/conn.php");
include("../inc/mail_class.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href="../template/<?php echo siteskin?>/style.css" rel="stylesheet" type="text/css">
<title>发邮件</title>
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
$n=isset($_GET['n'])?$_GET['n']:0;
$id="";
$i=0;
if(isset($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$id.($_POST['id'][$i].',');	
	}
}else{
	$founderr=1;
	$ErrMsg="<li>操作失败！请先选中要下载的信息</li>";
}
$id=substr($id,0,strlen($id)-1);//去除最后面的","

if ($n==0){
setcookie("dlid",$id,time()+3600*24,"/");
}
?>
<div class="main">
<?php
if (check_user_power("dls_print")=="no"){
$founderr=1;
$ErrMsg=$ErrMsg."<li>您所在的用户组没有权限！<br><a href='../one/vipuser.php'>升级为VIP会员</a></li>";
}

$size=5;//每轮群发个数
$sleeps=1;//每个间隔时间

$sql_n="select content from zzcms_msg where elite=1";
$rs_n=query($sql_n);
$row_n=num_rows($rs_n);
if (!$row_n){
showmsg('未设邮件内容，请先设邮件内容','/user/index.php?gotopage=msg_manage.php');
}else{
$row_n=fetch_array($rs_n);
}
$subject=$row_n['content'];
$mailbody=stripfxg($row_n['content'],true);
$smtp  =   new smtp(smtpserver,25,true,sender,smtppwd,sender);//25:smtp服务器的端口一般是25

if (strpos(@$_COOKIE['dlid'],",")>0){
$sql="select email from zzcms_dl where passed=1 and id in (". @$_COOKIE['dlid'] .") order by id asc limit $n,$size";
}else{
$sql="select email from zzcms_dl where passed=1  and id='".@$_COOKIE['dlid']."'";
}

	$rs=query($sql); 
	$row=num_rows($rs); 
	if ($row){
		while ($row=fetch_array($rs)){
		$to=$row['email']; //收件人
		$send=$smtp->sendmail($to,sender,$subject,$mailbody,"HTML");//邮件的类型可选值是 TXT 或 HTML 
		if($send){echo "<li>".$n."发送到".$to."成功</li>";}else{echo "<li>".$n."发送到".$to."失败</li>";}
		flush();  //不在缓冲中的或者说是被释放出来的数据发送到浏览器    
		//sleep($sleeps);
		$n=$n+1;

		}
		echo '<br><b>本轮群发'.$size.'个完成，正在转入下一轮</b><br/>';
		echo"<meta http-equiv=\"refresh\" content=\"1;url=dl_sendmail.php?n=$n\">";   
	}else{
	echo '完成';
	}
?>	
</div>

</body>
</html>