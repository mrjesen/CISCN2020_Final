<?php
error_reporting(0);
session_start();
$_SESSION = [];
echo "<script>location.href='./index.php'</script>";
die;
?>