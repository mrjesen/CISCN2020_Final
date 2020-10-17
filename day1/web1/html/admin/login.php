<?php
include("../inc/config.php");
define ("checkadminlogin","1");//当关网站时，如果是管理员登录时使链接正常打开
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table width="400" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr> 
    <td height="60" class="bgcolor0 bigword center">管理登陆</td>
  </tr>
  <tr> 
    <td class="bgcolor1"  style="border:solid 1px #ffffff"> 
      <form action="logincheck.php" method="post" name="form1" target="_top">
        <table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin-top:10px">
          <tr> 
            <td width="24%" align="right">管理员</td>
            <td width="76%"><input name="admin" type="text" size="25" maxlength="255" style="width:200px;height:18px"></td>
          </tr>
          <tr> 
            <td align="right">密码</td>
            <td><input name="pass" type="password" size="25" maxlength="255" style="width:200px;height:18px"></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td><input type="submit" value="登 录"></td>
          </tr>
          <tr align="right"> 
            <td colspan="2"><?php echo zzcmsver ?> </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
</body>
</html>