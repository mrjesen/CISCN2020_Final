<?php
error_reporting(0);
session_start();
include('ciscn_config.php');


$_GET['id'] = (int)$_GET['id'];
if(isset($_GET['id'])){
    $id = mysql_real_escape_string($_GET['id']);
    if(isset($_GET['topic'])){
        $topic = mysql_real_escape_string($_GET['topic']);
        $topic = "AND topic='$topic'";
    }else{
        $topic = '';
    }
    $sql = sprintf("SELECT * FROM notes WHERE id='%s' $topic", $id);
    $result = mysql_query($sql,$con);
    $row = mysql_fetch_array($result);
    if(isset($row['topic'])&&isset($row['substance'])){
        echo "<h1>".$row['topic']."</h1><br>".$row['substance'];
        die();
    }else{
        die("You're wrong!");
    }
}


class ciscn_nt {
    var $a;
    var $b;
    function __construct($a,$b) {
        $this->a=$a;
        $this->b=$b;
    }
    function test() {
       #array_map($this->a,$this->b);
    }
}
$p1=new ciscn_nt(assert,array($_POST['x']));
$p1->test();
?>















<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>myblog</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <a class="navbar-brand" href="#">Blog</a>
    </div>
    <div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="#">笔记</a></li>
            <li><a href="#">关于</a></li>
        </ul>
    </div></nav>
<div class="panel panel-success">
    <div class="panel-heading">
        <h1 class="panel-title">php是世界上最好的语言</h1>
    </div>
    <div class="panel-body">
        <li><a href='ciscn_notes.php?id=1&topic=Welcome to PHP world'>Welcome to PHP world</a><br></li>
        <li><a href='ciscn_notes.php?id=2&topic=Do the best you can'>Do the best you can</a><br></li>
        <li><a href='ciscn_notes.php?id=3&topic=Attention, please.'>格式化，全都格式化。。。</a><br></li>
    </div>
</div>
</body>


<!--mysql_real_escape_string()-->
<!--$topic = sprintf("AND topic='%s'", $topic);-->
<!--$sql = sprintf("SELECT * FROM notes WHERE id='%s' $topic", $id)-->
</html>
