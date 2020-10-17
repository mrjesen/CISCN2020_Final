<?php
ob_start();//打开缓冲区，可以setcookie
include("../inc/conn.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<?php
//checkyzm($_POST["yzm"]);
	$contents = isset($_POST['contents'])?$_POST['contents']:"";
	$name = isset($_POST['name'])?$_POST['name']:"";
	$tel = isset($_POST['tel'])?$_POST['tel']:"";
	$email = isset($_POST['email'])?$_POST['email']:"";
	$saver = isset($_POST['saver'])?$_POST['saver']:"";
	$fromurl = isset($_POST['fromurl'])?$_POST['fromurl']:"";

if (@$_COOKIE['cuestip']==getip()){
	showmsg('此IP留过言了！');
}
if ($contents==''||$name==''||$tel==''){
	showmsg('请完整填写您的信息');
}

checkstr($name,'quanzhongwen','姓名');
checkstr($tel,'tel','电话号码');
	
$rs=query("select * from zzcms_guestbook where linkmen='$name' and phone='$tel' and saver='$saver'");
$row=num_rows($rs);
if ($row){
showmsg('您已留过言了！');
}else{	
$addok=query("insert into zzcms_guestbook (content,linkmen,phone,email,saver,sendtime)
values('$contents','$name','$tel','$email','$saver','".date('Y-m-d H:i:s')."')");
setcookie("dlliuyan",$saver,time()+3600,"/");//供留言后显示联系方式处用
setcookie("cuestip",getip(),time()+3600,"/");
$addok?showmsg('您的留言已成功提交！',$fromurl):showmsg('失败，您的留言没有被提交！');
}
?>