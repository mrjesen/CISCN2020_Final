<?php
setcookie("admin",'xxx',1,"/");//这里也要加目录参数，否则无法退出
setcookie("pass",'xxx',1,"/");//设为1意味着1970年1月1日8点零1秒,否则当客户端时间为过去时间时，退出产生deleted的cookie值
echo "<script>location.href='../index.php'</script>";
?>