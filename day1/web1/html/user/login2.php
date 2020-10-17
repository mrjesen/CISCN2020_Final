<?php
include("../inc/conn.php");
$fromurl=isset($_GET['fromurl'])?$_GET['fromurl']:"";
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>用户登录</title>
<style type="text/css">
<!--
body{font-size:12px}
.biaodan{height:18px;border:solid 1px #cccccc;width:150px;background-color:#FFFFFF}
-->
</style>
<script type="text/javascript" src="../js/jquery.js"></script>
<script>
$(function(){
$("#getcode_math").click(function(){
		$(this).attr("src",'/one/code_math.php?' + Math.random());
	});
});

function CheckUserForm(){
if(document.UserLogin.username.value=="")
		{
			alert("请输入用户名！");
			document.UserLogin.username.focus();
			return false;
		}
		if(document.UserLogin.password.value == "")
		{
			alert("请输入密码！");
			document.UserLogin.password.focus();
			return false;
		}
			if(document.UserLogin.yzm.value == "")
		{
			alert("请输入验证问题的正确答案！");
			document.UserLogin.yzm.focus();
			return false;
		}
}	
</script>
</head>
<body>

<form action='logincheck.php' method='post' name='UserLogin' onSubmit='return CheckUserForm();' target='_parent'><!--target='_parent'注：可以模态窗自动消失 -->
<table width="100%" height="100" border="0" cellpadding="5" cellspacing="0">
      <tr>
        <td width="100" align="right">用户名</td>
        <td><input name="username" type="text" class="biaodan" id="username" tabindex="1" value="" size="14" maxlength="255" />
        <a href="/reg/<?php echo getpageurl3("userreg")?>" target="_parent">注册用户	</a></td>
      </tr>
      <tr>
        <td width="100" align="right">密码</td>
        <td><input name="password" type="password" class="biaodan" id="password" tabindex="2" size="14" maxlength="255" />
          <a href="/one/getpassword.php" target="_parent">找回密码</a></td>
      </tr>
      <tr>
        <td width="100" align="right">答案 </td>
        <td><input name="yzm" type="text" id="yzm" value="" tabindex="3" size="10" maxlength="50" style="width:40px" class="biaodan"/>
        <img src="/one/code_math.php" align="absmiddle" id="getcode_math" title="看不清，点击换一张" /></td>
      </tr>
      <tr>
        <td width="100" align="right">&nbsp;</td>
        <td><label><input name="CookieDate[]" type="checkbox" id="CookieDate" value="1">
          记住我的登录状态</label>
          <input name="fromurl" type="hidden" value="<?php echo $fromurl//这里是由上页JS跳转来的，无法用$_SERVER['HTTP_REFERER']?>" /></td>
      </tr>
      <tr>
        <td width="100" align="right">&nbsp;</td>
        <td><input type="submit" name="Submit" value="登 录	" tabindex="4" /></td>
      </tr>
    </table>
</form>
 			  
</body>
</html>