<?php
error_reporting(0);
session_start();

if (time() % 1800 === 0)
{
   @exec("rm -rf /var/www/html/upload/*.png");
}

if (isset($_SESSION['username']))
    header("Location: dashboard.php");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>工业冶炼系统后台管理平台</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="all,follow">
    <link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/bootstrap/4.2.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
</head>
<body>
<div class="page login-page">
    <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
            <div class="row">
                <div class="col-lg-6">
                    <div class="info d-flex align-items-center">
                        <div class="content">
                            <div class="logo">
                                <h1>欢迎登录</h1>
                            </div>
                            <p>工业冶炼系统后台管理平台</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 bg-white">
                    <div class="form d-flex align-items-center">
                        <div class="content">
                            <form method="post" action="./dashboard.php" class="form-validate" id="loginFrom">
                                <div class="form-group">
                                    <input id="login-username" type="text" name="username" required data-msg="请输入用户名" placeholder="username"  class="input-material">
                                </div>

                                <div class="form-group">
                                    <input id="login-password" type="password" name="password" required data-msg="请输入密码" placeholder="password" class="input-material">
                                </div>

                                <div class="form-group">
                                    <input id="login-username" type="text" name="email" required data-msg="请输入邮箱" placeholder="email"  class="input-material">
                                </div>

                                <div class="form-group">
                                    <input id="login-username" type="text" name="age" required data-msg="请输入年龄" placeholder="age"  class="input-material">
                                </div>

                                <button id="login" type="submit" class="btn btn-primary">登录</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="vendor/jquery-validation/jquery.validate.min.js"></script>
<script src="js/front.js"></script>
<script>
    $(function(){
        var check1s=localStorage.getItem("check1");
        var check2s=localStorage.getItem("check2");
        var oldName=localStorage.getItem("userName");
        var oldPass=localStorage.getItem("passWord");
        if(check1s=="true"){
            $("#login-username").val(oldName);
            $("#login-password").val(oldPass);
            $("#check1").prop('checked',true);
        }else{
            $("#login-username").val('');
            $("#login-password").val('');
            $("#check1").prop('checked',false);
        }
        if(check2s=="true"){
            $("#check2").prop('checked',true);
            $("#loginFrom").submit();
        }else{
            $("#check2").prop('checked',false);
        }
        $("#login").click(function(){
            var userName=$("#login-username").val();
            var passWord=$("#login-password").val();
            /*获取当前输入的账号密码*/
            localStorage.setItem("userName",userName)
            localStorage.setItem("passWord",passWord)
            /*获取记住密码  自动登录的 checkbox的值*/
            var check1 = $("#check1").prop('checked');
            var check2 = $('#check2').prop('checked');
            localStorage.setItem("check1",check1);
            localStorage.setItem("check2",check2);
        })

    })
</script>
</body>
</html>

